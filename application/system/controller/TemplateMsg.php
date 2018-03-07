<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/3/5
 * Time: 上午9:31
 */
namespace app\system\controller;

use app\common\controller\Api;
use app\system\service\TemplateService;
use think\Db;
use think\Request;

class TemplateMsg extends Api
{
    /*
     * 设置模板消息id
     */
    public function setTemplate()
    {
        $data = Request::instance()->post();
        $res  = TemplateService::setTemplate($data);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(TemplateService::getError());
        }
    }
    /*
     * 获取所有模板消息列表
     */
    public function getAll()
    {
        $res = Db::name('template')->select();
        return $this->responseSuccess($res);
    }
}