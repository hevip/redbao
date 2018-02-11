<?php

namespace app\admin\controller;

use app\common\controller\Api;
use anu\SingleFactory;
use think\Request;
use app\admin\service\PayService;

class Pay extends Api
{
    public function payOderList()
    {
        $data = Request::instance()->post();

        $list = PayService::payOderList($data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(PayService::getError());
        }

    }

}