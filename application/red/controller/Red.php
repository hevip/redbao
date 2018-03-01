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
    /*
     * 获取红包信息
     */
    public function getRedInfo($id)
    {
        $uid = $this->auth['user_id']??'';
        $redinfo = RedService::read($id,$uid);

        if($redinfo){
            return $this->responseSuccess($redinfo);
        }else{
            return $this->responseError(RedService::getError());
        }
    }
    /*
     * 领取红包，对应红包类型1
     */
    public function getMoney()
    {
        $data = Request::instance()->post();
        $uid  = $this->auth['user_id']??'';
        $res  = RedService::getMoney($data,$uid);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(RedService::getError());
        }
    }
    /*
     * 广告推广红包
     */
    public function adRed()
    {
        $res = RedService::adRed();
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(RedService::getError());
        }
    }
    public function getAllAd($page=1)
    {
        if(!isset($this->auth['admin_id'])){
            //
            return $this->responseError([
                'status_code'=>4088,
                'message'    =>'权限不允许'
            ]);
        }
        $res = RedService::getAllAd($page);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(RedService::getError());
        }
    }
    /*
     * 设置广告红包
     */
    public function setAdRed()
    {
        if(!isset($this->auth['admin_id'])){
            //
            return $this->responseError([
                'status_code'=>4088,
                'message'    =>'权限不允许'
            ]);
        }
        $data = Request::instance()->post();
        $res = RedService::setAdRed($data);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(RedService::getError());
        }
    }
}