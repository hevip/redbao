<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:20
 */
namespace app\cash\controller;

use think\Controller;
use think\Request;
use app\common\controller\Api;
use app\pay\service\ProblemService;

class Problem extends Api
{
    /**
     * 问题列表显示
    **/
    public function problemList()
    {

        $list = ProblemService::problemList();
        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(ProblemService::getError());
        }
    }


}