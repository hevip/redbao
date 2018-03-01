<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/23
 * Time: 下午1:28
 */

namespace app\common\controller;


use think\Controller;
use greatsir\RedisClient;
use think\Response;
use app\admin\service\VersionService;
class Version extends Controller
{
    public function getVersion()
    {
        $redis = RedisClient::getHandle(0);
        $version = $redis->getKey('Sys_version');
        $version = (int)$version;
        return Response::create([
            'status'=>'success',
            'data'  => [
                'version'=>$version
            ]
        ],'json');
    }
    public function getVersionNew($id){
        $result = VersionService::getVersion($id);
        if($result){
            return Response::create([
                'status'=>'success',
                'data'  => [
                    'version'=>$result
                ]
            ],'json');
            //return $this->responseSuccess($result);
        }else{
            return Response::create([
                'status'=>'failed',
                'error'  =>  VersionService::getError()
            ],'json');
        }
    }
}