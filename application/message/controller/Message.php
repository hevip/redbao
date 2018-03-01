<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-01-31
 * Time: 17:11
 */

namespace app\message\controller;

use app\common\controller\Api;
use app\message\service\MessageService;
use think\Request;

class Message extends Api
{

    public function index()
    {
        $uid = $this->auth['user_id'] ?? '';
        $status = Request::instance()->post();
        $res = MessageService::message($uid,$status);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MessageService::getError());
        }
    }
    public function uploadImg()
    {
        $res = MessageService::Img();
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MessageService::getError());
        }
    }

    public function serviceMessage()
    {
        $uid = $this->auth['user_id'] ?? '';
        $info = Request::instance()->post();
        $res = MessageService::serviceMessage($uid,$info);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MessageService::getError());
        }
    }
    public function template()
    {
        $uid = $this->auth['user_id'] ?? '';
        $info = Request::instance()->post();
        $res = MessageService::template($info,$uid);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MessageService::getError());
        }
    }
}