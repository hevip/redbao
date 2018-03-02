<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:22
 */
namespace app\common\service;
use think\Controller;

class LoadConfigService extends Controller
{
    /**
     *  获取支付配置信息
     */
    public static function getConfig($num)
    {
        if ($num == 0) {
            $config = [
                'app_cert_pem'      => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pem' . DIRECTORY_SEPARATOR .  'weixin' . DIRECTORY_SEPARATOR . 'apiclient_cert.pem',
                'app_key_pem'       => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pem' . DIRECTORY_SEPARATOR .  'weixin' . DIRECTORY_SEPARATOR . 'apiclient_key.pem',
                'wx_cacert_pem'     => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pem' . DIRECTORY_SEPARATOR .  'weixin' . DIRECTORY_SEPARATOR . 'rootca.pem',
            ];
        }else(
            $config = [
                'app_cert_pem'      => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pem' . DIRECTORY_SEPARATOR .  'weixin'. $num . DIRECTORY_SEPARATOR . 'apiclient_cert.pem',
                'app_key_pem'       => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pem' . DIRECTORY_SEPARATOR .  'weixin'. $num . DIRECTORY_SEPARATOR . 'apiclient_key.pem',
                'wx_cacert_pem'     => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pem' . DIRECTORY_SEPARATOR .  'weixin'. $num . DIRECTORY_SEPARATOR . 'rootca.pem',
            ]
        );

        return $config;
    }
}