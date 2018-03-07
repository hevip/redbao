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
use app\common\service\LoadConfigService;
use app;
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
    public static function cash_apply($uid, $data)
    {
        date_default_timezone_set('Asia/Shanghai');
        //查询余额
        $user_data = Db::name('users')->where('user_id', $uid)->field('user_balance,user_openid')->find();

        //验证提交的金额
        if (!is_numeric($data['cash_money']) || $data['cash_money'] < 1) {
            self::setError([
                'status_code' => '500',
                'message' => '金额格式不正确或金额小于1元！',
            ]);
            return false;
        }
            //频繁限制
            $last_cash = Db::name('cash_log')->where('user_id',$uid)->order('create_time desc')->limit(1)->field('create_time')->find();

            $times = time()-$last_cash['create_time'];
            if ($times < 60) {
                self::setError([
                    'status_code' => '500',
                    'message' => '操作频繁！',
                ]);
                return false;
            }
            //判断提现每日次数
            $today_start = strtotime(date("Y-m-d"),time());
            $today_end = $today_start + 24*60*60;
            $day_times = Db::name('cash_log')->where('user_id',$uid)->where('create_time','between',[$today_start,$today_end])->count();

            if ($day_times >= 3) {
                self::setError([
                    'status_code' => '500',
                    'message' => '每日提现次数不得超过3次',
                ]);
                return false;
            }

            //计算剩余余额
            $new_balance = bcsub($user_data['user_balance'], $data['cash_money'], 2);
            if ($new_balance < 0) {
                self::setError([
                    'status_code' => '500',
                    'message' => '余额不足！',
                ]);
                return false;
            }


            //提现订单号
            $cash_no = 'WJTX' . date('YmdHis') . rand(1000, 9999);

            //获取后台配置项
            $proportion = Db::name('backstage')->select();
            $backstack = [];
            foreach ($proportion as $k => $v) {
                $backstack[$v['id']] = ['setitem' => $v['setitem'], 'item' => $v['item']];
            }

            //获取服务费比例
            $service = sprintf("%.2f", bcmul($data['cash_money'], $backstack[5]['item'], 3));

            //数据准备
            $save_data = [
                'user_id' => $uid,
                'cash_money' => $data['cash_money'],
                'server_money' => $service,
                'pay_no' => $cash_no,
                'create_time' => time(),
            ];

            //创建订单
            $cashModel = new Cash();
            $saveCode = $cashModel->saveCashLog($uid, $save_data, $new_balance);
            if (!$saveCode) {
                self::setError([
                    'status_code' => '500',
                    'message' => '服务超时！'
                ]);
                return false;
            }

            //提现金额
            $cash_money = bcsub($data['cash_money'], $service, 2);
            $wxConfig = config('wxpay');
            //获取服务器ip
            $ip = gethostbyname($_SERVER['SERVER_NAME']);
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
                    Log::write('企业付款到零钱：' . json_encode($ret, JSON_UNESCAPED_UNICODE));
                    $ret['message'] = '提现申请成功！';
                    return $ret;
                } catch (PayException $e) {
                    echo $e->errorMessage();
                    exit;
                }
            } else {
                return [
                    'static_code' => 1,
                    'message' => '提现申请成功！'
                ];
            }


    }
}