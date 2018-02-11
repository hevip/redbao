<?php
/**
 * 管理员模块－管理员控制器
 * User: greatsir
 * Date: 17-6-29
 * Time: 下午1:35
 */
namespace app\admin\controller;

use app\common\controller\Api;
use anu\SingleFactory;
use think\Request;
use app\admin\service\CashService;

class Cash extends Api
{
    //提现订单列表
    public function cashList()
    {
        $data = Request::instance()->post();

        $list = CashService::cashList($data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(CashService::getError());
        }
    }

    //更改订单状态
    public function upCashList()
    {
        $data = Request::instance()->post();
        $list = CashService::upStatus($data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(CashService::getError());
        }
    }
}