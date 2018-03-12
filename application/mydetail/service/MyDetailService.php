<?php
namespace app\mydetail\service;


use app\common\service\BaseService;
use think\Db;

class MyDetailService extends BaseService
{
    public static $name = [];
    //发包明细
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

    //退款明细
    public static function refund($uid)
    {
//        dump($uid);exit;
        $res = Db::name('refund')->where('user_id',$uid)->field('refund_money,refund_time')->order('refund_time desc')->limit(10)->select();
        if($res){
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
//    public static function send_red_list($page)
//    {
//        $validate = validate('MyDetail');
//        if(!$validate->check($page)){
//            self::setError(['status_code'=>4004,'message'=>$validate->getError()]);
//            return false;
//        }
//        if(!empty($page['name'])){
//            $uid = Db::name('users')->where('user_name','like','%'.$page['name'].'%')->field('user_id')->find();
//            if($uid){
//                $res = Db::name('send')->where('user_id',$uid['user_id'])->select();
//                if($res){
//                    $info['total'] = Db::name('send')->where('user_id',$uid['user_id'])->count();
//                    $info['list'] = $res;
//                    return $info;
//                }else{
//                    self::setError(['status_code' => 500, 'message' => '没找到相关数据s']);
//                    return false;
//                }
//            }else{
//                self::setError(['status_code' => 500, 'message' => '没找到相关数据u']);
//                return false;
//            }
//        }
//
//        $res = Db::name('send')->page($page['page'],10)->order('create_time desc')->select();
//        foreach($res as $k=>$v){
//            $res[$k]['user_name'] = Db::name('users')->where('user_id',$v['user_id'])->value('user_name');
//            $res[$k]['create_time'] = date('y-m-d H:i:s',$v['create_time']);
//            $res[$k]['end_time'] = date('y-m-d H:i:s',$v['end_time']);
//        }
////        var_dump($res);exit;
//        if($res){
//            $info['total'] = Db::name('send')->count();
//            $info['list'] = $res;
//            return $info;
//        }elseif(empty($res)){
//            self::setError(['status_code' => 500, 'message' => '暂无数据']);
//            return false;
//        }else{
//            self::setError(['status_code' => 500, 'message' => '服务器忙，请稍候再试']);
//            return false;
//        }
//    }
    //收到红包列表

    public static function send_red_list($data)
    {
        $validate = validate('MyDetail');
        if(!$validate->check($data)){
            self::setError(['status_code'=>4004,'message'=>$validate->getError()]);
            return false;
        }
        //页数
        $page = $data['page'];
        if(empty($page)|| $page <= 1){
            $start_page = 0;
        }else{
            $start_page = ($page-1)*10;
        }

        //搜索名称
        if (!empty($data['user_name'])){
            $user_name = Db::name('users')->where('user_name','like',$data['user_name'])->field('user_id')->find();
            if (!empty($user_name)) {
//                self::$name = $user_name['user_id'];
                $showData['total'] = Db::name('send')->where('user_id',$user_name['user_id'])->count();
                $showData['list'] = Db::name('send')
                    ->alias('s')
                    ->join('wj_users u','s.user_id = u.user_id')
                    ->order('create_time desc')
                    ->where('s.user_id',$user_name['user_id'])
                    ->limit($start_page,10)
                    ->field('u.user_name,s.user_id,s.red_id,s.se_money,s.se_number,s.pay_money,s.voice,s.is_pay,s.type,s.content,s.qr_url,s.create_time')
                    ->select();
                return $showData;
            }else{
                self::setError([
                    'status_code'=>500,
                    'message'    =>"User name does not exist."
                ]);
                return false;
            }
        }
        //是否搜索时间
//        if (!empty($data['start_time']) && !empty($data['end_time'])){
//            $showData['total'] = Db::name('send')->where('create_time','between',[$data['start_time'],$data['end_time']])->count();
//            $showData['list'] = Db::name('send')
//                ->alias('s')
//                ->join('wj_users u','s.user_id = u.user_id')
//                ->where('create_time','between',[$data['start_time'],$data['end_time']])
//                ->where('user_id',self::$name['user_id'])
//                ->order('create_time desc')
//                ->limit($start_page,10)
//                ->field('u.user_name,s.red_id,s.user_id,se_money,se_number,pay_money,voice,is_pay,type,content,qr_url,s.create_time')
//                ->select();
////            var_dump($showData['list']);exit;
//            if (empty($showData)) {
//                self::setError([
//                    'status_code'=>500,
//                    'message'    =>"没有找到相关数据"
//                ]);
//                return false;
//            }
//            var_dump($showData);exit;
//            return $showData;
//        }

        //不搜索名称
        $showData['total'] = Db::name('send')->count();
        $showData['list'] = Db::name('send')
            ->alias('s')
            ->join('wj_users u','s.user_id = u.user_id')
            ->order('create_time desc')
            ->limit($start_page,10)
            ->field('u.user_name,s.red_id,s.user_id,s.se_money,s.se_number,s.pay_money,s.voice,s.is_pay,s.type,s.content,s.qr_url,s.create_time')
            ->select();
//        var_dump($showData);exit;
        return $showData;
    }



    public static function received_red_list($data)
    {
        $validate = validate('MyDetail');
        if(!$validate->check($data)){
            self::setError(['status_code'=>4004,'message'=>$validate->getError()]);
            return false;
        }
        //页数
        $page = $data['page'];
        if(empty($page)|| $page <= 1){
            $start_page = 0;
        }else{
            $start_page = ($page-1)*10;
        }

        //搜索名称
        if (!empty($data['user_name'])){
            $user_name = Db::name('users')->where('user_name','like',$data['user_name'])->field('user_id')->find();
            if (!empty($user_name)) {
//                self::$name = $user_name['user_id'];
                $showData['total'] = Db::name('received')->where('user_id',$user_name['user_id'])->count();
                $showData['list'] = Db::name('received')
                    ->alias('r')
                    ->join('wj_users u','r.user_id = u.user_id')
                    ->order('create_time desc')
                    ->where('r.user_id',$user_name['user_id'])
                    ->limit($start_page,10)
                    ->field('u.user_name,r.user_id,r.red_id,r.re_money,r.voice_url,r.red_num,r.is_success,r.create_time')
                    ->select();
                return $showData;
            }else{
                self::setError([
                    'status_code'=>500,
                    'message'    =>"User name does not exist."
                ]);
                return false;
            }
        }
        //不搜索名称
        $showData['total'] = Db::name('received')->count();
        $showData['list'] = Db::name('received')
            ->alias('r')
            ->join('wj_users u','r.user_id = u.user_id')
            ->order('create_time desc')
            ->limit($start_page,10)
            ->field('u.user_name,r.user_id,r.red_id,r.re_money,r.voice_url,r.red_num,r.is_success,r.create_time')
            ->select();
        return $showData;

//        $res = Db::name('received')->page($page['page'],10)->order('create_time desc')->select();
//        foreach($res as $k=>$v){
//            $res[$k]['user_name'] = Db::name('users')->where('user_id',$v['user_id'])->value('user_name');
//            $res[$k]['create_time'] = date('y-m-d H:i:s',$v['create_time']);
//        }
    }
}