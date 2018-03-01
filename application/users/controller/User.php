<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/1/26
 * Time: 下午4:20
 */
namespace app\users\controller;


use app\common\controller\Api;
use app\users\service\UserService;
use think\Request;

class User extends Api
{
    /*
     * 获取用户信息
     */
    public function getUserInfo()
    {
        $data = Request::instance()->post();
        $uid = $this->auth['user_id'] ?? '';
        $userInfo = UserService::getUserInfo($data['userInfo'],$uid);
        if($userInfo){
            return $this->responseSuccess($userInfo);
        }else{
            return $this->responseError(UserService::getError());
        }
    }
    public function getInfoBytoken()
    {
        $uid = $this->auth['user_id']??'';
        $userInfo = UserService::read($uid);
        if($userInfo){
            return $this->responseSuccess($userInfo);
        }else{
            return $this->responseError(UserService::getError());
        }
    }


    //我的记录  收到和发出头部
    public function record($re_type){
        $uid = $this->auth['user_id']??'';
        $record = UserService::record($re_type,$uid);
        if($record){
            return $this->responseSuccess($record);
        }else{
            return $this->responseError(UserService::getError());
        }
    }
    //我的记录  收到和发出底部
    public function record_list($re_type,$more = null){
        $uid = $this->auth['user_id']??'';
        $record = UserService::record_list($re_type,$uid,$more);
        if($record){
            return $this->responseSuccess($record);
        }else{
            return $this->responseError(UserService::getError());
        }
    }

    //红包详情头部（点击记录后）
    public function red_details($re_type,$red_id){
        $uid = $this->auth['user_id']??'';
        $data = UserService::red_details($re_type,$red_id,$uid);
        if($data){
            return $this->responseSuccess($data);
        }else{
            return $this->responseError(UserService::getError());
        }
    }

    //红包详情记录列表
    public function details_list($red_id,$page = 0){
        $data = UserService::details_list($red_id,$page);
        if($data!==false){
            return $this->responseSuccess($data);
        }else{
            return $this->responseError(UserService::getError());
        }
    }
    /*
     * 获取我的声音
     */
    public function getMyVoice()
    {
        $uid = $this->auth['user_id']??'';
        $list= UserService::getMyVoice($uid);
        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(UserService::getError());
        }
    }

    public function QR_code(){
        $data = UserService::QR_code();
        if($data){
            return $this->responseSuccess($data);
        }else{
            return $this->responseError(UserService::getError());
        }
    }


    //举报
    public function report($content,$phone = null,$weixin = null,$red_id){
        $uid = $this->auth['user_id']??'';
        $result = UserService::report($content,$phone,$weixin,$red_id,$uid);
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(UserService::getError());
        }
    }

    //后台举报查看列表
    public function report_list($page = null,$search = null){
        $result = UserService::report_list($page,$search);
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(UserService::getError());
        }

    }


    //查看被举报红包详情
    public function report_detail($red_id){
        $result = UserService::report_detail($red_id);
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(UserService::getError());
        }
    }




}