<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/9
 * Time: 上午11:36
 */

namespace app\audio\controller;


use app\audio\service\AudioService;
use app\audio\service\QiniuService;
use app\common\controller\Api;
use greatsir\RedisClient;
use think\Request;
use think\Response;
use think\Controller;

class Qiniu extends Controller
{
    /*
     * 获取上传token
     */
    public function getUploadToekn()
    {
        $token = AudioService::getUploadToken();
        if($token){
            return Response::create([
                'uptoken'  => $token
            ],'json');
            //return $this->responseSuccess($token);
        }else{
            return Response::create([
                'error'  => '获取uptoken失败'
            ],'json');
            //return $this->responseError(AudioService::getError());
        }
    }
    /*
     * 七牛上传转换回调
     */
    public function upCallback()
    {
        QiniuService::callback();
    }
}