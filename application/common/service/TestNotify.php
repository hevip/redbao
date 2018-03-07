<?php
namespace app\common\service;
use Payment\Notify\PayNotifyInterface;
use Payment\Config;
use app\common\model\PayOrders;
use think\Db;
use think\Log;

/**
 * @author: helei
 * @createTime: 2016-07-20 18:31
 * @description:
 */

/**
 * 客户端需要继承该接口，并实现这个方法，在其中实现对应的业务逻辑
 * Class TestNotify
 * anthor helei
 */

class TestNotify implements PayNotifyInterface
{
    public function notifyProcess(array $data)
    {
        $channel = $data['channel'];
        if ($channel === Config::ALI_CHARGE) {// 支付宝支付
        } elseif ($channel === Config::WX_CHARGE) {// 微信支付
        } elseif ($channel === Config::CMB_CHARGE) {// 招商支付
        } elseif ($channel === Config::CMB_BIND) {// 招商签约
        } else {
            // 其它类型的通知
        }

        Log::write('微信支付回调内容：'.json_encode($data));

        //获取用户信息
        $user_data = Db::name('users')->where('user_openid',$data['openid'])->field('user_id')->find();

        //查询订单
        $order_info = Db::name('send')->where(['user_id'=>$user_data['user_id'],'order_sn'=> $data['out_trade_no']])->field('order_sn,pay_money')->find();
        //不存在订单

        if (empty($order_info)) {
            return false;
        }

        /*$pay_money = bcdiv($data['total_fee'],100,2);
        //判断金额是否相同
        if ($order_info['pay_money'] != $pay_money) {
            return false;
        }*/

        Db::startTrans();
        try{
            //更改状态
            Db::name('send')->where(['order_sn'=>$order_info['order_sn']])->update(['trade_no' => $data['transaction_id'],'is_pay' => 1]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw new \think\Exception($e->getMessage(),$e->getCode());
            return false;
        }
    }
}