<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-01-31
 * Time: 17:11
 */

namespace app\mydetail\controller;

use app\common\controller\Api;
use app\mydetail\service\MyDetailService;
use think\Request;

class MyDetail extends Api
{

    public function send()
    {
//        $user = $this->auth['uer_id'] ?? '';
        $user = Request::instance()->post();
        $res = MyDetailService::send($user);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MyDetailService::getError());
        }
    }

    public function receive()
    {
//        $user = $this->auth['uid'] ?? '';
        $user = Request::instance()->post();
        $res = MyDetailService::receive($user);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MyDetailService::getError());
        }
    }

    //提现
    public function cash()
    {
//        $user = $this->auth['uid'] ?? '';
        $user = Request::instance()->post();
        $res = MyDetailService::cash($user);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MyDetailService::getError());
        }
    }
}