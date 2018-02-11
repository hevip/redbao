<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-6-30
 * Time: 下午1:08
 */
namespace app\common\service\auth;

use app\member\model\Member;
use app\users\model\User;
use think\Response;
use think\Hook;
use app\vgirl\service\MemberService;
use GuzzleHttp\Client;
use Firebase\JWT\JWT;
class AuthService
{
    /*public static function oauthLogin($data)
    {
        $userInfo = self::getUserInfo($data);
        $githubid = $userInfo['id'];
        $memberModel = new Member();
        $res = $memberModel->where('github_id',$githubid)->find();
        if($res){
            //返回token,
            $result = $res->getData();
            $payload['requesterID'] = $result['uid'];
            $payload['identity']    = 'yezhu';
            $payload['exp']         = time()+604800;
            $result['token']        = JWT::encode($payload,'yjlm');
            return Response::create([
                'status' => 'success',
                'data'   =>$result
            ],'json');


        }else{
            //创建用户生成token
            $mData['nickname'] = $userInfo['login'];
            $mData['face']     = $userInfo['avatar_url'];
            $mData['github_id']= $githubid;
            $saveRes= $memberModel->save($mData);
            if($saveRes){
                //返回保存结果
                $res = $memberModel->where('github_id',$githubid)->find()->getData();
                $payload['requesterID'] = $res['uid'];
                $payload['identity']    = 'yezhu';
                $payload['exp']         = time()+604800;
                $re['token']        = JWT::encode($payload,'yjlm');
                return Response::create([
                    'status' => 'success',
                    'data'   =>$res
                ],'json');

            }else{
                return Response::create([
                    'status'=> 'failed',
                    'error' => [
                        'status_code'=> 500,
                        'message'    =>'网络请求失败，请稍后重试'
                    ]
                ],'json');

            }
        }
    }*/
    public static function oauthLogin($data)
    {
        $userInfo = self::getUserInfo($data);
        $openid = $userInfo['openid'];
        $memberModel = new Member();
        $res = $memberModel->where('wx_openid',$openid)->find();
        if($res){
            //返回token,
            $result = $res->getData();
            $payload['requesterID'] = $result['uid'];
            $payload['identity']    = 'yezhu';
            $payload['exp']         = time()+604800;
            $result['token']        = JWT::encode($payload,'yjlm');
            return Response::create([
                'status' => 'success',
                'data'   =>$result
            ],'json');


        }else{
            //创建用户生成token
            $mData['nickname'] = $userInfo['nickname'];
            $mData['face']     = $userInfo['headimgurl'];
            $mData['wx_openid']= $openid;
            $saveRes= $memberModel->save($mData);
            if($saveRes){
                //返回保存结果
                $res = $memberModel->where('wx_openid',$openid)->find()->getData();
                $payload['requesterID'] = $res['uid'];
                $payload['identity']    = 'yezhu';
                $payload['exp']         = time()+604800;
                $res['token']        = JWT::encode($payload,'yjlm');
                return Response::create([
                    'status' => 'success',
                    'data'   =>$res
                ],'json');

            }else{
                return Response::create([
                    'status'=> 'failed',
                    'error' => [
                        'status_code'=> 500,
                        'message'    =>'网络请求失败，请稍后重试'
                    ]
                ],'json');

            }
        }
    }
    public static function getUserInfo($data)
    {
        $data = self::getAccessToken($data['code']);
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$data['accessToken'].'&openid='.$data['openid'];
        //$url = 'https://api.github.com/user?access_token='.$access_token;

        $res= self::cUrl($url);
        $userInfo = json_decode($res,true);
        return $userInfo;

    }


    public static function getAccessToken($code)
    {
        //dump(config('auth.github_Client_Id'));die;
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.config('auth.wechat_appid').'&secret='.config('auth.wechat_secret').'&code='.$code.'&grant_type=authorization_code';
        /*$url = 'https://github.com/login/oauth/access_token?client_id='.config('auth.github_Client_Id').'&client_secret='.config('auth.github_Client_Secret').'&code='.$code;
        $header =[
            'Content-Type'=>'application/json'
        ];
        $res = self::cUrl($url);
        $resArray = explode('&',$res);
        $accessTokenArray = explode('=',$resArray[0]);
        $accessToken = $accessTokenArray[1];*/
        $res = json_decode(self::cUrl($url));
        $data['accessToken'] = $res->access_token;
        $data['openid'] = $res->openid;
        return $data;
    }


    /**
     * [cUrl cURL(支持HTTP/HTTPS，GET/POST)]
     * @author qiuguanyou
     * @copyright 烟火里的尘埃
     * @version   V1.0
     * @date      2017-04-12
     * @param     [string]     $url    [请求地址]
     * @param     [Array]      $header [HTTP Request headers array('Content-Type'=>'application/x-www-form-urlencoded')]
     * @param     [Array]      $data   [参数数据 array('name'=>'value')]
     * @return    [type]               [如果服务器返回xml则返回xml，不然则返回json]
     */
    public static  function cUrl($url,$header=null, $data = null){
        //初始化curl
        $UserAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36';
        $curl = curl_init();
        //设置cURL传输选项

        if(is_array($header)){

            curl_setopt($curl, CURLOPT_HTTPHEADER  , $header);
        }
        curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);


        if (!empty($data)){//post方式
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        //获取采集结果
        $output = curl_exec($curl);

        //关闭cURL链接
        curl_close($curl);
        return $output;
    }

    //生成token
    public static function createToken($header)
    {
        if(!isset($header['identity'])){

            return Response::create([
                'status'=> 'failed',
                'error' => [
                    'status_code'=> 4001,
                    'message'    =>'missing params identity in http-header'
                ]
            ],'json');
        }
        if(!isset($header['loginway']))
        {
            return Response::create([
                'status' => 'failed',
                'error'  =>[
                    'statu_code'=>4002,
                    'message'   => 'missing params loginway in http-header'
                ]
            ],'json');
        }
        if(isset($header['identity'])&&isset($header['loginway'])){
            $class = '\app\common\service\auth\\'.$header['loginway'];
            $identity = $header['identity'];
            $loginWay = new $class();
            $reponse  = $loginWay->check($identity);
            $info = $reponse->getData();
            if(isset($info['data']['identity'])&&$info['data']['identity']=='admin'){
                Hook::listen('login_behavior',$info);
            }

            return $reponse;
        }
    }
    /**
     * 创建请求者
     * @param array　$payload token解密的用户标识
     * return userinfo
     */
    public static function createAuth($payload)
    {
        $identity = $payload->identity;
        $requesterID = $payload->requesterID;
        switch ($identity){
            case 'admin':
                $map['admin_id'] = $requesterID;
                $map['is_del']   = 0;
                $model = new \app\common\model\Admin();
                $res   = $model->field('admin_id,account,nickname')->where($map)->find();
                if($res){
                    $requesterInfo             = $res->getData();
                    $requesterInfo['identity'] = $identity;
                }else{
                    return Response::create([
                        'status' => 'failed',
                        'error'  =>[
                            'status_code'=>4002,
                            'message'   => 'no this account'
                        ]
                    ],'json')->send(); 
                }
                break;
            case 'yezhu':
                $map['user_id'] = $requesterID;
                $map['is_del']=0;
                $model = new \app\users\model\User();
                $res   = $model->field('user_id,user_name,user_icon')->where($map)->find();

                if($res){
                    $requesterInfo = $res->getData();
                    $requesterInfo['identity'] = $identity;
                    //$is_vip = MemberService::isVip($requesterInfo['uid']);
                    //$requesterInfo['is_vip'] = $is_vip;
                }else{
                    return Response::create([
                        'status'=>'failed',
                        'error' =>[
                            'status_code'=> 4003,
                            'message'    => 'no account not find'
                        ]
                    ],'json')->send();
                }
                break;
            case 'developer':
                $model = new \app\common\model\Developer();
                $map['did'] = $requesterID;
                $map['is_del'] = 0;
                $res = $model->where($map)->find();
                if($res){
                    $requesterInfo = $res->getData();
                    $requesterInfo['identity'] = $identity;
                }else{
                    return Response::create([
                        'status'=>'failed',
                        'error' =>[
                            'status_code'=>4025,
                            'message'    => 'no this account'
                        ]
                    ])->send();
                }
            default:

        }
        return $requesterInfo;

    }
}