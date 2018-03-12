<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:22
 */

namespace app\pay\service;

use app\common\model\PayOrders;
use app\common\service\BaseService;
use app\common\service\LoadConfigService;
use app\common\service\RedAllotService;
use greatsir\RedisClient;
use Overtrue\Pinyin\Pinyin;
use think\Db;
use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Config;
use app\users\service\UserService;
use think\Log;
use app\audio\service\AudioService;

class PayService extends BaseService
{
    /**
     * 微信支付创建
     */
    public static function pay_creat($uid, $data)
    {
        date_default_timezone_set('Asia/Shanghai');
        $payModel = new PayOrders();

//        验证post值
        if (!is_numeric($data['pay_money'])) {
            self::setError([
                'status_code' => '500',
                'message' => '金额格式错误！',
            ]);
            return false;
        }
        var_dump(config('wxpay'));exit;
        //判断敏感词
        $is_mg = self::curl_request('http://www.hoapi.com/index.php/Home/Api/check?',['str'=>$data['content'],'token'=>'4be68372707a741d90d38f3474328706']);
        $is_mg = json_decode($is_mg);
        if (!$is_mg->status) {
            self::setError([
                'status_code' => '500',
                'message' => '检测到敏感字符！',
            ]);
            return false;
        }

        //判断每个红包均分金额是否大于1元
        $distribution = bcdiv($data['pay_money'], $data['send_number'], 2);
        if ($distribution < 1) {
            self::setError([
                'status_code' => '500',
                'message' => '每个红包平均大于1元！',
            ]);
            return false;
        }

        //阿拉伯数字转中文数字
        $audioService = new AudioService();
        $content = $audioService::chinanum($data['content']);

        $data['content'] = implode('', $content);

        // 判断是否存在超时订单
        $overtime_data = $payModel->where(['user_id' => $uid, 'is_pay' => 0])->where('end_time', '<', time())->field('red_id,order_sn')->select();

        if (!empty($overtime_data)) {
            foreach ($overtime_data as $k => $v) {
                $payModel->where(['red_id' => $v['red_id'], 'order_sn' => $v['order_sn']])->update(['is_pay' => -1]);
            }
        }

        $order_sn = 'WJBS' . date('YmdHis') . rand(1000, 9999);


        //文字转拼音
        $pinyin = new Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
        $content_pinyin = implode(',', $pinyin->convert($data['content']));


        //数据准备
        $save_data = [
            'user_id' => $uid,
            'type' => $data['type'],
            'se_money' => $data['pay_money'],
            'se_number' => $data['send_number'],
            'voice' => $data['voice_url'] ?? '',
            'content' => $data['content'],
            'content_pinyin' => $content_pinyin,
            'pay_money' => $data['pay_money'],
            'trade_no' => '',
            'receive' => 0,
            'create_time' => time(),
            'end_time' => time() + 600,
            'order_sn' => $order_sn,
            'balance' => 0,
            'duration' => $data['duration'] ?? '',
        ];

        //储存数据
        $list = $payModel::savePayOrder($save_data);

        if (!is_numeric($list['data'])) {
            self::setError([
                'status_code' => '500',
                'message' => '网络错误，请重试！'
            ]);
            return false;
        }

        //生成二维码并保存路径
        $born_url = UserService::QR_code($list['data']);

        if ($born_url != false) {
            $up_url = Db::name('send')->where('red_id', $list['data'])->setField('qr_url', $born_url);
            if (!$up_url) {
                self::setError([
                    'status_code' => '500',
                    'message' => '网络错误，请重试！'
                ]);
                return false;
            }
        }

        $redAllot = new RedAllotService;
        $money_arr = $redAllot::getRedArray($data['pay_money'], $data['send_number'], 1);


        //遍历$money_arr，把数组每一项加入队列，
        $redis = RedisClient::getHandle(0);
        foreach ($money_arr as $k => $v) {
            $redis->pushList('red_money:' . $list['data'], $v);
        }

        //获取用户信息
        $user_data = Db::name('users')->where('user_id', $uid)->field('user_openid')->find();
        $openid = $user_data['user_openid'];

        //是否开启测试模式
        $proprotion = Db::name('backstage')->where('id', 8)->find();
        if ($proprotion['item']) {
            Log::write('真实模式:' . $proprotion['item']);
            $total = $data['pay_money'];
        } else {
            Log::write('测试模式:' . $proprotion['item']);
            $total = 0.01;
        }

        //获取config
        //$proportion = Db::name('backstage')->where('id',11)->find();


//        //获取指定微信支付和提现配置
//        $loadConfig = new LoadConfigService();
//        if (isset($backstack[11])) {
//            $wxConfig = config('wxpay'.$proportion['item']);
//            $pem = $loadConfig::getConfig($proportion['item']);
//            $wxConfig = array_merge($wxConfig,$pem);
//        }elseif ($proportion['item'] == 0){
//            $wxConfig = config('wxpay');
//            $pem = $loadConfig::getConfig(0);
//            $wxConfig = array_merge($wxConfig,$pem);
//        }else{
//            $wxConfig = config('wxpay');
//            $pem = $loadConfig::getConfig(0);
//            $wxConfig = array_merge($wxConfig,$pem);
//        }
        $wxConfig = config('wxpay');


        //统一下单
        $payData = [
            'body' => '拜年智力',
            'subject' => '微聚',
            'order_no' => $order_sn,
            'timeout_express' => time() + 600,// 表示必须 600s 内付款
            'amount' => $total,// 微信沙箱模式，需要金额固定为3.01,$money||$data['pay_money']
            'return_param' => '123',
            'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
            'openid' => $openid,
            'product_id' => '123',
        ];

        try {
            $ret = Charge::run(Config::WX_CHANNEL_LITE, $wxConfig, $payData);
            /*$info['red_id'] =$list['data'];
            Log::write('package内容：'.$ret['package']);
            parse_str($ret['package'],$prepay_id);
            Log::write('prepay内容:'.json_encode($prepay_id));
            $info['form_id']= $prepay_id['prepay_id'];
            $info['openid']  = $openid;
            Log::write('模板消息参数:'.json_encode($info));
            $res = MessageService::template($info);*/
            $ret['red_id'] = $list['data'];
            return $ret;
        } catch (PayException $e) {
            echo $e->errorMessage();
            exit;
        }

    }

    public static function curl_request($url, $post = '', $cookie = '', $returnCookie = 0)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if ($returnCookie) {
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie'] = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        } else {
            return $data;
        }
    }
}
