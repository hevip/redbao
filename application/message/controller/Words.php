<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-01-31
 * Time: 17:11
 */

namespace app\message\controller;

use app\message\service\WordsService;
use think\Controller;
use think\Response;

class Words extends Controller
{
    //口令展示
    public function word()
    {
        $res = WordsService::word();
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(WordsService::getError());
        }
    }

    public function barrage()
    {
        $res = WordsService::barrage();
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(WordsService::getError());
        }
    }
    /**
     * 错误响应
     */
    public function responseError(Array $error)
    {
        return Response::create([
            'status'=>'failed',
            'error'=>[
                'status_code'=>$error['status_code'],
                'message'=>$error['message'],
            ]
        ],'json');
    }

    /**
     * 正确响应
     */
    public function responseSuccess($data)
    {
        return Response::create([
            'status'=>'success',
            'data'  => $data
        ],'json');
    }
}