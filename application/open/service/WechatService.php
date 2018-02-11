<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/5
 * Time: 上午11:11
 */
namespace app\open\service;

use app\common\service\BaseService;
use greatsir\RedisClient;
use greatsir\Snowflake;
use GuzzleHttp\Client;
use think\Cache;
use think\Db;
use Firebase\JWT\JWT;

class WechatService extends BaseService
{
    public static $gateway='https://api.weixin.qq.com/sns/jscode2session?';
    /*
     * 获取openid
     */
    public static function getOpenid($code)
    {
        $appid = config('wechatapp_id');//小程序id
        $secret = config('wechatapp_secret');//小程序app secret
        $url = self::$gateway.'appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
        $client = new Client();
        $response = $client->get($url);
        $res =$response->getBody()->getContents();
        //判断res是否含有openid以及session_key
        $param = json_decode($res);
        if(isset($param->session_key)&&isset($param->openid)){
            //请求成功
            try{
                //判断openid是否存在
                //存在，那么直接拿到token即可
                $where['user_openid'] = $param->openid;
                if($user_info=Db::name('users')->where($where)->find()){



                    $payload['requesterID'] = $user_info['user_id'];
                    $payload['identity']    = 'yezhu';
                    $payload['exp']         = time()+604800;
                    $result['token']        = JWT::encode($payload,config('jwt-key'));
                    $result['identity']     = 'yezhu';
                }else{
                    //生成token
                    $userData['user_id'] = Snowflake::generateParticle(1);
                    $userData['user_openid'] = $param->openid;
                    Db::name('users')->insertGetId($userData);//插入到用户表
                    //$result = Db::name('users')->where(['user_id'=>$userData['user_id']])->find();
                    $payload['requesterID'] = $userData['user_id'];
                    $payload['identity']    = 'yezhu';
                    $payload['exp']         = time()+604800;
                    $result['token']        = JWT::encode($payload,config('jwt-key'));
                    $result['identity']     = 'yezhu';

                }
                //dump($param);die;
                //Cache::set('openid:'.$param->openid,$param->session_key);
                //保存session_key
                $redis = RedisClient::getHandle();
                $redis->setKey('openid:'.$param->openid,$param->session_key);//保
                return $result;//返回token
            }catch (\Exception $e){
                throw new \think\Exception($e->getMessage(),$e->getCode());
            }

        }else{
            self::setError([
                'status_code'=>$param->errcode,
                'message'    =>$param->errmsg
            ]);
            return false;
        }
    }
}