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
//        $file = request()->file();
//        $arr = self::object_array($file['image']);
//        foreach($arr as $k=>$v){
//            if(is_array($v) && !empty($v)){
//                $arr = $v;
//            }
//        }
//        var_dump($arr);exit;
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

    //对象转数组
    public static function object_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)self::object_array($v);
            }
        }

        return $obj;
    }

    //口令展示
    public function word()
    {
//        $uid = $this->auth['user_id'] ?? '';
//        $info = Request::instance()->post();
        $res = MessageService::word();
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MessageService::getError());
        }
    }
    //口令添加
    public function add_word()
    {
//        $uid = $this->auth['user_id'] ?? '';
        $info = Request::instance()->post();
        $res = MessageService::postWord($info);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MessageService::getError());
        }
    }

    //口令删除
    public function del_word()
    {
//        $uid = $this->auth['user_id'] ?? '';
        $info = Request::instance()->post();
        $res = MessageService::del_word($info);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(MessageService::getError());
        }
    }
}