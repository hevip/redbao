<?php

namespace app\admin\controller;

use app\common\controller\Api;
use anu\SingleFactory;
use think\Request;
use app\admin\service\ProblemService;

class Problem extends Api
{
    /**
     * 问题列表显示
     *
     */
    public function problemList()
    {
        $list = ProblemService::problemList();

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(ProblemService::getError());
        }
    }

    /**
     * 问题列表添加
     *
     */
    public function problemAdd()
    {
        $data = Request::instance()->post();
        $list = ProblemService::problemAdd($data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(ProblemService::getError());
        }
    }

    /**
     * 问题列表删除
     *
     */
    public function problemDelete()
    {
        $data = Request::instance()->post();
        $list = ProblemService::problemDelete($data);

        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(ProblemService::getError());
        }
    }
}