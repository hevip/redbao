<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2017/10/25
 * Time: 下午7:23
 */

namespace app\commpont\controller;

use app\commpont\service\QiniuService;
use think\Controller;
use think\Request;
use think\Response;

class Qiniu extends Controller
{
    public function getToken($index)
    {
        $res = QiniuService::getToken($index);
        if($res){
            return Response::create([
                'status'=>'success',
                'data'=>$res
            ],'json');
        }else{
            return Response::create([
                'status'=>'failed',
                'error' => QiniuService::getError()
            ],'json');
        }
    }

    /**
     * 获取私有下载地址
     */
    public function getDownloadUrl()
    {
        $data = Request::instance()->post();
        $res = QiniuService::getDownloadUrl($data['baseurl']);
        if($res){
            return Response::create([
                'status'=>'success',
                'data'  =>$res
            ],'json');
        }else{
            return Response::create([
                'status'=>'failed',
                'error'=>QiniuService::getError()
            ],'json');
        }
    }

}