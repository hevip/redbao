<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/5
 * Time: 上午11:07
 */
namespace app\open\controller;


use app\open\service\WechatService;
use think\Controller;
use think\Request;
use think\Response;
class Wechat extends Controller
{
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
    /*
     * 获取用户openid
     */
    public function getOpenid()
    {
        $data = Request::instance()->post();
        $res = WechatService::getOpenid($data['code']);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(WechatService::getError());
        }
    }
}