<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-9-6
 * Time: 上午9:51
 */

namespace app\commpont\service;

use app\common\service\BaseService;
use greatsir\RedisClient;
class SmsService extends BaseService
{
    /**
     *发送短信
     */
    public static function registerCode($data)
    {
        $validate = validate('Sms');
        if(!$validate->scene('register')->check($data)){
            self::setError([
                'status_code'=>4040,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        //验证通过发送短信
        $code = rand(1000,9999);
        $redis = RedisClient::getHandle();
        $redis->setKey('register_code'.$data['mobile'],$code,300);
        return array('send_time'=>time());


    }
}