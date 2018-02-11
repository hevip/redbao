<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-01-31
 * Time: 17:11
 */

namespace app\red\controller;

use app\common\controller\Api;
use app\red\service\RedService;
use think\Request;

class Red extends Api
{

    public function red_list()
    {
//        $user = $this->auth['uid'] ?? '';
        $red = Request::instance()->post();
        $res = RedService::red_list($red);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(RedService::getError());
        }
    }
}