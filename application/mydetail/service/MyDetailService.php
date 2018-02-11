<?php
namespace app\mydetail\service;


use app\common\service\BaseService;
use think\Db;

class MyDetailService extends BaseService
{
    public static function send($user)
    {
        //发的红包明细
        $validate = validate('app\mydetail\validate\MyDetail');
        if(!$validate->check($user)){
            self::setError([
                'status_code'=>4105,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        $res = Db::name('send')->where('user_id',$user['user_id'])
            ->field('red_id,user_id,se_money,create_time')->select();
        if ($res) {
            return $res;
        }elseif(empty($res)){
            self::setError(['status_code' => 500, 'message' => '暂无数据']);
            return false;
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙，请稍候在试']);
            return false;
        }
    }

    //收的红包明细
    public static function receive($user)
    {
        $validate = validate('app\mydetail\validate\MyDetail');
        if(!$validate->check($user)){
            self::setError([
                'status_code'=>4105,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        $res = Db::name('received')->where('user_id',$user['user_id'])
            ->field('user_id,re_money,create_time')->select();
        if ($res) {
            return $res;
        }elseif(empty($res)){
            self::setError(['status_code' => 500, 'message' => '暂无数据']);
            return false;
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙，请稍候在试']);
            return false;
        }
    }

    //红包提现明细
    public static function cash($user)
    {
        $validate = validate('app\mydetail\validate\MyDetail');
        if(!$validate->check($user)){
            self::setError([
                'status_code'=>4105,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        $res = Db::name('cash_log')->where('user_id',$user['user_id'])
            ->field('user_id,cash_money,create_time')->select();
        if ($res) {
            return $res;
        }elseif(empty($res)){
            self::setError(['status_code' => 500, 'message' => '暂无数据']);
            return false;
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙，请稍候在试']);
            return false;
        }
    }
}