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


}