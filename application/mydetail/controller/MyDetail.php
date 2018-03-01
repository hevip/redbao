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

        $uid = $this->auth['user_id'] ?? '';

        $res = MyDetailService::send($uid);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MyDetailService::getError());
        }
    }

    public function receive()
    {
        $uid = $this->auth['user_id'] ?? '';

        $res = MyDetailService::receive($uid);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MyDetailService::getError());
        }
    }

    //提现
    public function cash()
    {

        $uid = $this->auth['user_id'] ?? '';

        $res = MyDetailService::cash($uid);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MyDetailService::getError());
        }
    }
    //我发出红包的列表
    public function send_red_list()
    {
//        $uid = $this->auth['user_id'] ?? '';
        $page = Request::instance()->post();
        $res = MyDetailService::send_red_list($page);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MyDetailService::getError());
        }
    }
    //我收到红包的列表
    public function received_red_list()
    {
//        $uid = $this->auth['user_id'] ?? '';
        $page = Request::instance()->post();
        $res = MyDetailService::received_red_list($page);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MyDetailService::getError());
        }
    }
}