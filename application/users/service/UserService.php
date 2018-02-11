<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/1/26
 * Time: 下午4:22
 */
namespace app\users\service;


use app\common\service\BaseService;
use app\users\model\User;
use greatsir\RedisClient;
use greatsir\Snowflake;
use greatsir\wechat\WXBizDataCrypt;
use function GuzzleHttp\Psr7\str;
use think\Db;
use Firebase\JWT\JWT;

class UserService extends BaseService
{
    /*
     * 获取用户信息
     */
    public static function getUserInfo($data,$uid)
    {
        $userInfo = self::read($uid);
        if(!$userInfo){
            return false;
        }
        $openid = $userInfo['user_openid'];
        try{
            //校验用户信息
            $redis = RedisClient::getHandle();
            $session_key = $redis->getKey('openid:'.$openid);
            $appid = config('wechatapp_id');
            //解密数据，以及验证签名
            $pc = new WXBizDataCrypt($appid,$session_key);
            $errCode = $pc->decryptData($data['encryptedData'],$data['iv'],$newData);
            //dump($errCode);die;
            if($errCode==0){
                //解密成功
                //更新用户信息
                $newData = json_decode($newData);
                $upData['user_name'] = $newData->nickName??$uid;
                $upData['user_icon'] = $newData->avatarUrl??'';
                $upData['user_unionid'] = $newData->unionId??'';
                $res = Db::name('users')->where(['user_id'=>$uid])->update($upData);
                if($res||$res==0){
                    $userInfo = self::read($uid);
                    return $userInfo;
                }
            }else{
                self::setError([
                    'status_code'=>$errCode,
                    'message'    =>'数据校验失败'
                ]);
                return false;
            }
        }catch (\Exception $e){
            throw new \think\Exception($e->getMessage(),$e->getCode());
        }


    }

    public static function read($uid)
    {
        $validate = validate('app\users\validate\User');
        if(!$validate->check(['user_id'=>$uid])){
            self::setError([
                'status_code'=>4103,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        $where['user_id'] = $uid;
        $where['is_del']  = 0;
        $userInfo = Db::name('users')->where($where)->find();
        if(!empty($userInfo)){
            return $userInfo;
        }else{
            self::setError([
                'status_code'=>404,
                'message'    =>'用户不存在'
            ]);
            return false;
        }
    }
    /*
     * 检测微信登陆
     * @params array $data 发送的参数
     */
    public static function checkWx($data)
    {
        $validate = validate('app\users\validate\WxLogin');
        if(!$validate->check($data)){
            self::setError([
                'status_code'=>4105,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        $appid = config('wechatapp_id');
        //解密数据，以及验证签名
        $pc = new WXBizDataCrypt($appid,'session_key');

        $user = new User();
        $where['user_openid'] = $data['openid'];
        $where['user_unionid'] = $data['unionid'];
        $res = $user->where($where)->find();
        if(!empty($res)){
            //更新数据
            $result = $res->getData();
            $payload['requesterID'] = $result['user_id'];
            $payload['identity']    = 'yezhu';
            $payload['exp']         = time()+604800;
            $result['token']        = JWT::encode($payload,config('jwt-key'));
            $result['identity']     = 'yezhu';
            return $result;
        }else{
            //创建用户id ,业务1
            $user_data['user_id'] = Snowflake::generateParticle(1);
            $user_data['user_openid'] = $data['openid'];
            $user_data['user_unionid']= $data['unionid'];
            $user_data['user_name']   = $data['nickname']??$user_data['user_id'];
            $user_data['user_icon']   = $data['user_icon']??'';
            $user = new User();
            $res = $user->save($user_data);
            if($res){
                $user_info = self::read($user_data['user_id']);
                if($user_info){
                    $payload['requesterID'] = $user_info['user_id'];
                    $payload['identity']    = 'yezhu';
                    $payload['exp']         = time()+604800;
                    $user_info['token']        = JWT::encode($payload,config('jwt-key'));
                    $user_info['identity']     = 'yezhu';
                    return $user_info;
                }
            }else{
                self::setError([
                    'status_code'=>500,
                    'message'    =>'网络请求错误，请稍后重试'
                ]);
                return false;
            }
        }
    }
}