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
        $overtime_data = Db::name('send')->where('create_time', '<=', $overtimes)->where('is_over',0)->where('se_number', 'NEQ', 'receive')->field('red_id,user_id,se_number,receive')->select();
        Log::write('查询语句：'.Db::name('send')->getLastSql());

        //获取缓存中的金额
        $redis = RedisClient::getHandle(0);

            foreach ($overtime_data as $key => $val) {
                $red_money = $redis->listrange('red_money:' . $val['red_id']);
                $money = 0;
                foreach ($red_money as $v){
                    $money= bcadd($money,$v,2);
                }
                try {
                if ($money) {
                    Db::name('send')->where('red_id',$val['red_id'])->setField('is_over', 1);
                    Db::name('users')->where('user_id',$val['user_id'])->setInc('user_balance',$money);
                }
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                }
            }

    }
}