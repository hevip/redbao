<?php
/**
 * 管理员service
 * User: greatsir
 * Date: 17-6-30
 * Time: 上午11:47
 */
namespace app\admin\service;
use app\common\service\BaseService;

use Firebase\JWT\JWT;
use think\Validate;
use think\Db;
use think\Debug;

class PayService extends BaseService
{
    /**
     * 支付订单列表
     */
    public static function payOderList($data)
    {
        //页数
        $page = $data['page'];
        if(empty($page)|| $page <= 1){
            $start_page = 0;
        }else{
            $start_page = ($page-1)*10;
        }

        $order_list =  Db::name('pay_orders')->alias('p')->join('wj_users u','p.user_id = u.user_id')
            ->order('id desc')->field('u.user_name,p.order_sn,p.pay_money,p.trade_no,p.goods_id')
            ->limit($start_page,10)->select();

        if(!$order_list){
            self::setError([
                'status_code'=>509,
                'message'=>'没有排行数据',
            ]);
            return false;
        }
        return $order_list;
    }
}