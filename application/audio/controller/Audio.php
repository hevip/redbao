<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/8
 * Time: 下午2:51
 */
namespace app\audio\controller;

use app\audio\service\AudioService;
use app\common\controller\Api;
use think\Request;
use think\Response;
use app\audio\service\QiniuService;
class Audio extends Api
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
            return $this->responseError(AudioService::getError());
        }
    }
    /*
     *语音识别
     */
    public function speech()
    {
        $data = Request::instance()->post();
        $uid = $this->auth['user_id']??'';
        $res  = AudioService::getSpeechRes($data,$uid);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(AudioService::getError());
        }
    }



    public function speechTest()
    {
        $data = Request::instance()->post();
        $uid = $this->auth['user_id']??'';
        $res = QiniuService::speechNew($data,$uid);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(AudioService::getError());
        }

    }

}