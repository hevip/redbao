<?php

namespace app\common\controller;

use think\Controller;
use think\Request;
use app\common\service\auth\AuthService;
use Firebase\JWT\JWT;
use think\Debug;
/**
 * Class Login
 * 统一授权认证
 * @package app\common\controller
 */
class Auth extends Controller
{
    /**
     * 登录,根据identity参数和type参数确定校验方式
    */
    public function createToken(Request $request)
    {
        //统一登录服务
        //Debug::remark('begin');
        $header = $request->header();
        return AuthService::createToken($header);
    }
    /*
     * 回调地址
     */
    public function callback()
    {
        $data = Request::instance()->only(['code'],'post');
        $res  = AuthService::oauthLogin($data);
        return $res;
    }


}
