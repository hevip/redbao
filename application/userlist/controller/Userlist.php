<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-01-31
 * Time: 17:11
 */

namespace app\userlist\controller;

use app\common\controller\Api;
use app\userlist\service\UserlistService;
use think\Request;

class Userlist extends Api
{
    public function index()
    {
        $user = Request::instance()->post();
        $res = UserlistService::user($user);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(UserlistService::getError());
        }
    }
}