<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:20
 */
namespace app\pay\controller;

use think\Controller;

use Payment\Client\Notify;
use Payment\Common\PayException;
use app\common\service\TestNotify;
class PayBack extends Controller{

    /**
     * 支付成功回调
     **/
    public function pay_success()
    {
        //回调验证
        $wxConfig = config('wxpay');
        $callback = new TestNotify();
        $config = $wxConfig;
        $type = 'wx_charge';
        try {
            //$retData = Notify::getNotifyData($type, $config);// 获取第三方的原始数据，未进行签名检查
            $ret = Notify::run($type, $config, $callback);// 处理回调，内部进行了签名检查
            echo $ret;
        } catch (PayException $e) {
            echo $e->errorMessage();
            exit;
        }

    }
}