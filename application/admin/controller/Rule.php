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
use app\admin\service\RuleService;

class Rule extends Api
{
    public function addRule()
    {

        $uid = $this->auth['uid'] ?? '';
        $data = Request::instance()->post();

        $list = RuleService::addRule($uid,$data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(RuleService::getError());
        }
    }

    public function delRule()
    {
        $uid = $this->auth['uid'] ?? '';
        $data = Request::instance()->post();

        $list = RuleService::delRule($uid,$data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(RuleService::getError());
        }
    }
}