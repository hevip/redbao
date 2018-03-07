<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:22
 */
namespace app\common\service;


use app\common\service\BaseService;
use think\Controller;
use greatsir\RedisClient;

use think\Db;
use think\Log;

class payBackService extends Controller
{
    /**
     *  支付超时返余额
     */
    public static function outTimeBack()
    {
        //判断超时订单
        $overtimes = time() - 24 * 60 * 60 * 2;
        $overtime_data = Db::name('send')->where('is_pay',1)->where('is_over',0)->where('create_time', '<=', $overtimes)->where('se_number', 'NEQ', 'receive')->field('red_id,user_id,se_number,receive')->select();
        //获取缓存中的金额
        $redis = RedisClient::getHandle(0);

        foreach ($overtime_data as $key => $val) {
            //获取redis中缓存的金额
            $red_money = $redis->listrange('red_money:' . $val['red_id']);
            $money = 0;
            foreach ($red_money as $v){
                $money= bcadd($money,$v,2);
            }

            $save_data = [
                'red_id' => $val['red_id'],
                'refund_money' => $money,
                'user_id' => $val['user_id'],
                'refund_time'  => time()
            ];
            Db::startTrans();
            try {
                if ($money) {
                    //存记录
                    Db::name('refund')->insert($save_data);
                    //更新字段
                    Db::name('send')->where('red_id',$val['red_id'])->setField('is_over', 1);
                    Db::name('users')->where('user_id',$val['user_id'])->setInc('user_balance',$money);
                }
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
        }

    }

    public static function refunds()
    {
        //余额大于0的用户
        $user_data = Db::name('users')->where('user_balance','>','0')->field('user_id')->select();


        foreach ($user_data as $k => $v) {
            $user_received = 0;
            $se_money = 0;
            //查询发出记录
            $send_data = Db::name('send')->where('user_id',$v['user_id'])->where('is_pay',1)->field('red_id,se_money')->select();

            if (!empty($send_data)) {

                foreach ($send_data as $key => $value) {

                    $se_money += $value['se_money'];
                    $user_received += Db::name('received')->where('red_id',$value['red_id'])->sum('re_money');

                }


            }
            $user_received = bcsub($se_money,$user_received,2);
            //查询收到红包记录
            $received = Db::name('received')->where('user_id',$v['user_id'])->sum('re_money');

            $total =  bcadd($received,$user_received,2);

            Db::name('users')->where('user_id',$v['user_id'])->update(['user_balance'=>$total]);
        }

    }
}