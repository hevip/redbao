<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2017/12/25
 * Time: 下午9:16
 */

return [
    'use_sandbox'       => false,// 是否使用 微信支付仿真测试系统
    'is_open'           => true,
    'app_id'            => 'wx839d0f26e93bccf9',  // 公众账号IDwxc5b0906ec933e00b
    'mch_id'            => '1493897202',// 商户id1494450052
    'md5_key'           => '4564365462sdfsd435254sdf5gn34534',// md5 秘钥
    'sign_type'         => 'MD5',// MD5  HMAC-SHA256
    'limit_pay'         => [
        //'no_credit',
    ],// 指定不能使用信用卡支付   不传入，则均可使用
    'fee_type'          => 'CNY',// 货币类型  当前仅支持该字段
    'notify_url'        => 'https://weiju02.gratsir.com/pay/success',
    'redirect_url'      => 'http://www.greatsir.com/pay/success',// 如果是h5支付，可以设置该值，返回到指定页面
    'return_raw'        => true,// 在处理回调时，是否直接返回原始数据，默认为true
    // 如果是服务商，请提供以下参数
//    'sub_appid' => '',//微信分配的子商户公众账号ID
//    'sub_mch_id' => '',// 微信支付分配的子商户号
//    'sub_openid' => '',// 用户在子商户appid下的唯一标识,\这个应该不用
    'app_cert_pem'      => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pem' . DIRECTORY_SEPARATOR .  'weixin' . DIRECTORY_SEPARATOR . 'apiclient_cert.pem',
    'app_key_pem'       => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pem' . DIRECTORY_SEPARATOR .  'weixin' . DIRECTORY_SEPARATOR . 'apiclient_key.pem',
    'wx_cacert_pem'     => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pem' . DIRECTORY_SEPARATOR .  'weixin' . DIRECTORY_SEPARATOR . 'rootca.pem',
];

