<?php
namespace app\common\controller;

use think\Controller;
use think\Request;
use think\Response;
use Firebase\JWT\JWT;
use app\common\service\auth\AuthService;
use anu\SingleFactory;
use think\Hook;

/**
 * Class Base
 * 中间件:拦截判断是否已经授权登录
 * @package app\common\controller
 */
class  Middle extends Controller
{

    public $auth;
    public function __construct()
    {

       $request = Request::instance();
       $header  = $request->header();
       if(!isset($header['identity']))
       {
           Response::create([
               'status'=> 'failed',
               'error' =>[
                   'status_code'=> 4001,
                   'message'    => 'missing param identity in http-header',
               ],
           ],'json')->send();//exit();
       }else{
            //进行是否需要登录验证
           $identity = $header['identity'];
           $http_url = $request->module().'/'.$request->controller().'/'.$request->action();
           if($identity=='yezhu'){
            // var_dump($noauth);die;
               $noauth = config('?yezhu_noauth.'.$http_url);

           }else{
               $noauth = false;
           }
           /*if(!$noauth==true){
                //需要登录授权，进行验证
                $this->checkToken($header);
                if($identity=='admin'){
                    $this->checkPriv($http_url);
                }

           }*/
           if(!$noauth==true){
               // 需要登录授权，进行验证
               $this->checkToken($header);
               /*if($identity=='admin'){

               }*/
               // RBAC访问权限的控制
               //$this->checkPriv($request);

           }elseif (isset($header['authorization'])){

               $this->checkToken($header);
           }
           //验证不需要就可通过
       }
    }

    /**
     * 授权登录验证
     */
    public function checkToken($header)
    {
        if(!isset($header['authorization'])||empty($header['authorization'])){
            Response::create([
                'status'=> 'failed',
                'error' =>[
                    'status_code'=> 4003,
                    'message'    => 'missing param authorization in http-header'
                ]

            ],'json')->send();//exit();

        }else{
            //验证token
            $token   = $header['authorization'];
            $key     = config('jwt-key');
            $payload = JWT::decode($token,$key,array('HS256'));

            if(time()>$payload->exp){
                //token过期
                Response::create([
                    'status'=> 'failed',
                    'error' =>[
                        'status_code'=> 4004,
                        'message'    => 'token expired'
                    ]
                ],'json')->send();exit();
            }else{
                $this->auth = AuthService::createAuth($payload);
                SingleFactory::overAllData('auth',$this->auth);
            }
        }
    }

    public function checkPriv($httpurl)
    {
        //检测权限

    }
}
