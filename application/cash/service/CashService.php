<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:22
 */
namespace app\cash\service;

use app\common\model\Cash;
use app\common\service\BaseService;
use think\Db;
use Payment\Common\PayException;
use Payment\Client\Transfer;
use Payment\Config;
use think\Log;

class CashService extends BaseService
{
    /**
     * 微信提现申请
     */
    public static function cash_apply($uid,$data)
    {

        date_default_timezone_set('Asia/Shanghai');
        //查询余额
        $user_data= Db::name('users')->where('user_id',$uid)->field('user_balance,user_openid')->find();

        //验证提交的金额
        if (!is_numeric($data['cash_money']) || $data['cash_money'] < 1) {
            self::setError([
                'status_code'=> '500',
                'message'    => 'The money is not a number',
            ]);
            return false;
        }

        //计算剩余余额
        $new_balance = bcsub($user_data['user_balance'],$data['cash_money'],2);

        if ($new_balance < 0){
            self::setError([
                'status_code'=> '500',
                'message'    => 'Not sufficient funds',
            ]);
            return false;
        }

        //提现订单号
        $cash_no = 'WJTX'.date('YmdHis').rand(1000,9999);

        //获取服务费比例
        $proportion = Db::name('backstage')->select();

        $backstack = [];
        foreach ($proportion as $k=>$v) {
            $backstack[$v['id']] = ['setitem'=> $v['setitem'],'item'=>$v['item']];
        }

        $service =sprintf("%.2f", bcmul($data['cash_money'],$backstack[5]['item'],3));

        //数据准备
        $save_data = [
            'user_id'      => $uid,
            'cash_money'   => $data['cash_money'],
            'server_money' => $service,
            'pay_no'      => $cash_no,
            'create_time'  => time(),
        ];
        //创建订单
        $cashModel = new Cash();
        $saveCode = $cashModel->saveCashLog($uid,$save_data,$new_balance);

        if (!$saveCode) {
            self::setError([
                'status_code'=> '500',
                'message' => 'Failed to create order'
            ]);
            return false;
        }

        $cash_money = bcsub($data['cash_money'],$service,2);

        //获取服务器ip
	    $ip = gethostbyname($_SERVER['SERVER_NAME']);
        $wxConfig = config('wxpay');
        if ($wxConfig['is_open']) {
            $data = [
                'trans_no' => time(),
                'openid' => $user_data['user_openid'],
                'check_name' => 'NO_CHECK',// NO_CHECK：不校验真实姓名  FORCE_CHECK：强校验真实姓名   OPTION_CHECK：针对已实名认证的用户才校验真实姓名
                'payer_real_name' => '何磊',
                'amount' => $cash_money,
                'desc' => $backstack[9]['item'],
                'spbill_create_ip' => $ip,
            ];
            try {
                $ret = Transfer::run(Config::WX_TRANSFER, $wxConfig, $data);
                Log::write('企业付款到零钱：'.json_encode($ret, JSON_UNESCAPED_UNICODE));
                $ret['message'] = 'success';
                return $ret;
            } catch (PayException $e) {
                echo $e->errorMessage();
                exit;
            }
        }else{
            return [
                'static_code' => 1,
                'message' => 'success'
            ];
        }



    }


}
