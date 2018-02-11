<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-9-6
 * Time: 上午9:29
 */

namespace app\commpont\controller;

use app\common\controller\Api;
use app\commpont\service\SmsService;
use think\Request;

class Sms extends Api
{
    /**
     * 发送注册验证码
     */
    public function registerCode()
    {
        $data = Request::instance()->post();
        $result = SmsService::registerCode($data);
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(SmsService::getError());
        }
    }
}