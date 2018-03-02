<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/3/1
 * Time: 下午2:44
 */

namespace app\admin\service;


use app\common\service\BaseService;
use think\Db;
class VersionService extends BaseService
{
    /*
     *后台添加删除
     */
    public static function setVersion($id,$name,$appid,$app_version)
    {
        
        $data = [
            'name'=>$name,
            'appid'=>$appid,
            'app_version'=>$app_version,
        ];
        $msg=null;
        if(empty($data['name'])){$msg='请输入版本名称';}
        if(empty($data['appid'])){$msg='请输入appid';}
        if(!empty($msg)){
            self::setError([
                'status_code'=>4055,
                'message'    =>$msg,
            ]);
            return false;
        }
        if(!empty($id)){
            //更新
            $map = [
                'id'=>$id,
            ];

            $result = Db::name('app_version')->where($map)->find();
            if(!$result) {
                self::setError([
                    'status_code' => 4055,
                    'message' => '请输入正确的版本号ID',
                ]);
                return false;
            }
            $set = Db::name('app_version')->where($map)->update($data);
        }else{
            //添加
            $set = Db::name('app_version')->insert($data);
        }
        if($set){
            return true;
        }else{
            self::setError([
                'status_code'=>500,
                'message'    =>'服务器忙,请稍后再试1',
            ]);
            return false;
        }
    }


    //前台查看
    public static function getVersion($id)
    {
        $map = [
            'id'=>$id
        ];
        $version = Db::name('app_version')->where($map)->value('app_version');
        if($version!=0&&empty($version)){
            self::setError([
                'status_code'=>4055,
                'message'    =>'无此ID的版本号',
            ]);
            return false;
        }
        return $version;
    }


    //后台查看
    public static function versionList()
    {
        $data = Db::name('app_version')->select();
        if(!$data){
            self::setError([
                'status_code'=>4055,
                'message'    =>'没有任何版本',
            ]);
            return false;
        }

        return $data;
    }
}