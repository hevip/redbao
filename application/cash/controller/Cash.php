<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:20
 */
namespace app\cash\controller;

use think\Controller;
use think\Request;
use app\common\controller\Api;
use app\pay\service\CashService;

class Cash extends Api
{
    /**
     * 创建提现订单
    **/
    public function cash_create()
    {
        $uid = $this->auth['user_id'] ?? '';
        $data = Request::instance()->post();
        $list = CashService::cash_apply($uid,$data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(CashService::getError());
        }
    }


}