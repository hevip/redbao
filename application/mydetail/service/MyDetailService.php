<?php
namespace app\mydetail\service;


use app\common\service\BaseService;
use think\Db;

class MyDetailService extends BaseService
{
    public static function send($uid)
    {
        //发的红包明细
        $res = Db::name('send')
            ->where('user_id',$uid)
            ->where('is_pay',1)
            ->field('red_id,user_id,se_money,create_time')
            ->order('create_time desc')
            ->limit(10)
            ->select();
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
    public static function receive($uid)
    {
        $res = Db::name('received')->where('user_id',$uid)
            ->field('user_id,re_money,create_time')->order('create_time desc')->limit(10)->select();
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
    public static function cash($uid)
    {
        $res = Db::name('cash_log')->where('user_id',$uid)
            ->field('user_id,cash_money,create_time')->order('create_time desc')->limit(10)->select();
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
    //发出红包列表
    public static function send_red_list($page)
    {
        $validate = validate('MyDetail');
        if(!$validate->check($page)){
            self::setError(['status_code'=>4004,'message'=>$validate->getError()]);
            return false;
        }
        $res = Db::name('send')->page($page['page'],10)->order('create_time desc')->select();
        foreach($res as $k=>$v){
            $res[$k]['time'] = date('y-m-d H:i:s',$v['create_time']);
            $res[$k]['endtime'] = date('y-m-d H:i:s',$v['end_time']);
        }
        if($res){
            $info['total'] = Db::name('send')->count();
            $info['list'] = $res;
            return $info;
        }elseif(empty($res)){
            self::setError(['status_code' => 500, 'message' => '暂无数据']);
            return false;
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙，请稍候再试']);
            return false;
        }
    }
    //收到红包列表
    public static function received_red_list($page)
    {
        $validate = validate('MyDetail');
        if(!$validate->check($page)){
            self::setError(['status_code'=>4004,'message'=>$validate->getError()]);
            return false;
        }
        $res = Db::name('received')->page($page['page'],10)->order('create_time desc')->select();
        foreach($res as $k=>$v){
            $res[$k]['time'] = date('y-m-d H:i:s',$v['create_time']);
        }
        if($res){
            $info['total'] = Db::name('received')->count();
            $info['list'] = $res;
            return $info;
        }elseif(empty($res)){
            self::setError(['status_code' => 500, 'message' => '暂无数据']);
            return false;
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙，请稍候再试']);
            return false;
        }
    }
}