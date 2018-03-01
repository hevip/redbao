<?php
/**
 * 管理员模块－管理员控制器
 * User: greatsir
 * Date: 17-6-29
 * Time: 下午1:35
 */
namespace app\admin\controller;

use app\common\controller\Api;
use anu\SingleFactory;
use think\Request;
use app\admin\service\ConfigureService;

class Configure extends Api
{
    public function configureList()
    {
        $data = Request::instance()->post();
        $list = ConfigureService::configureList($data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(ConfigureService::getError());
        }
    }

    public function addConfigure()
    {
        $data = Request::instance()->post();
        $list = ConfigureService::addConfigure($data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(ConfigureService::getError());
        }
    }

    public function delConfigure()
    {
        $data = Request::instance()->post();
        $list = ConfigureService::delConfigure($data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(ConfigureService::getError());
        }
    }

    public function getProportion()
    {
        $list = ConfigureService::getProportion();

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(ConfigureService::getError());
        }
    }
}