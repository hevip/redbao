<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:20
 */
namespace app\pay\controller;

use think\Request;
use app\common\controller\Api;
use app\pay\service\PayService;

class Pay extends Api
{
    /**
     * 创建支付订单
    **/
    public function pay_create()
    {
        $uid = $this->auth['user_id'] ?? '';
        $data = Request::instance()->post();
        $list = PayService::pay_creat($uid,$data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(PayService::getError());
        }
    }


}