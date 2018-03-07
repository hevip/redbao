<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/3/5
 * Time: 上午9:34
 */
namespace app\system\service;

use app\common\service\BaseService;
use app\system\model\Template;
use think\Db;

class TemplateService extends BaseService
{
    public static function setTemplate($data)
    {
        if(isset($data['id'])&&!empty($data['id'])){
            //更新
            $res = Db::name('template')->where(['id'=>$data['id']])->update($data);
        }else{
            //添加
            $res = Db::name('template')->insert($data);
        }
        if($res){
            return ['set_time'=>time()];
        }else{
            self::setError(['status_code'=>500,'message'=>'网络请求错误']);
            return false;
        }
    }
}