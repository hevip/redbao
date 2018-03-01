<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/3/1
 * Time: 下午2:42
 */

namespace app\admin\controller;


use app\admin\service\VersionService;
use app\common\controller\Api;
use think\Request;

class Version extends Api
{
    /*
     * 设置版本号
     */
    public function setVersion($id=null,$name=null,$appid=null,$app_version=null)
    {
        /*$data = Request::instance()->post();*/
        /*$admin_id = $this->auth['admin_id']??'';
        if(!$admin_id){
            return $this->responseError([
                'status_code'=>4089,
                'message'    =>'权限不够'
            ]);
        }*/
        $res = VersionService::setVersion($id,$name,$appid,$app_version);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(VersionService::getError());
        }
    }


    //前台查看版本号
    public function getVersion($id){
        $result = VersionService::getVersion($id);
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(VersionService::getError());
        }
    }


    //后台查看版本号
    public function versionList(){
        $result = VersionService::versionList();
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(VersionService::getError());
        }
    }

}