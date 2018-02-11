<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-6-30
 * Time: 下午2:16
 */
/*
 * 微信登陆服务
 */
namespace app\common\service\auth;

use app\users\service\UserService;
use think\Request;
use think\Response;

class weixin implements authInterface{
    public function check($identity)
    {
        //破解拿到openid以及unionid
        $data = Request::instance()->post();
        //$data = Request::instance()->only(['openid','unionid','nickname','user_icon'],'post');
        // TODO: Implement check() method. 获取请求身份
        if($identity=='yezhu'){
            $res = UserService::checkWx($data);
            if($res){
                //请求成功
                return Response::create([
                    'status' => 'success',
                    'data'   =>$res
                ],'json');
            }else{
                //失败
                return Response::create([
                    'status'=>'failed',
                    'error' =>UserService::getError()
                ],'json');
            }

        }else{

            return Response::create([
                'status'=>'failed',
                'error' =>[
                    'status_code'=> 4104,
                    'message'    => '不支持该登陆方式'
                ]
            ],'json');
        }

    }
    public function createToken()
    {
        // TODO: Implement createToken() method.
    }
}