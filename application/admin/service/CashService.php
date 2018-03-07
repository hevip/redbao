<?php
/**
 * 管理员service
 * User: greatsir
 * Date: 17-6-30
 * Time: 上午11:47
 */
namespace app\admin\service;
use app\common\service\BaseService;

use Firebase\JWT\JWT;
use think\Validate;
use think\Db;
use think\Debug;

class CashService extends BaseService
{
    public static function cashList($data)
    {
        //页数
        $page = $data['page'];
        if(empty($page)|| $page <= 1){
            $start_page = 0;
        }else{
            $start_page = ($page-1)*10;
        }

        $condition = '';
        //判断未提现、已提现
        switch ($data['is_success']) {
            case 1:
                $condition = [
                    'is_success' => 1
                ];
                break;
            case 0:
                $condition = [
                    'is_success' => 0
                ];
                break;
            default:
                self::setError([
                    'status_code'=>500,
                    'message'    =>"Unlawful submission"
                ]);
                return false;
        }

        //是否搜索姓名
        if (!empty($data['user_name'])) {
            $user_name = Db::name('user')->where('user_name','like',$data['user_name'])->field('user_id')->find();
            if (!empty($user_name)) {
                $condition['user_id'] = $user_name['user_id'];
            }else{
                self::setError([
                    'status_code'=>500,
                    'message'    =>"User name does not exist."
                ]);
                return false;
            }
        }

        //是否搜索时间
        if (isset($data['start_time']) and isset($data['end_time'])) {
            //总条数
            $total = Db::name('cash_log')->where('create_time','between',$data['start_time'].','.$data['end_time'])->where($condition)->count();
            $showData = Db::name('cash_log')->alias('c')->join('wj_users u','c.user_id = u.user_id')->where('create_time','between',$data['start_time'].','.$data['end_time'])->where($condition)->order('create_time desc')->limit($start_page,10)->field('u.user_name,cash_id,c.user_id,cash_money,server_money,pay_no,trade_no,c.create_time')->select();
        }else{
            //总条数
            $total = Db::name('cash_log')->where($condition)->count();
            $showData = Db::name('cash_log')->alias('c')->join('wj_users u','c.user_id = u.user_id')->where($condition)->order('create_time desc')->limit($start_page,10)->field('u.user_name,cash_id,c.user_id,cash_money,server_money,pay_no,trade_no,create_time,is_success,c.create_time')->select();
        }

        if (empty($showData)) {
            self::setError([
                'status_code'=>500,
                'message'    =>"Query data is empty"
            ]);
            return false;
        }

        $showData['total'] = $total;
        return $showData;
    }

    public static function upStatus($data)
    {

        //验证post值
        if (!is_numeric($data['user_id']) and !is_numeric($data['cash_id'])) {
            self::setError([
                'status_code'=> 500,
                'message'    => 'The submission of id is not a number'
            ]);
        }

        $save_rule = Db::name('cash_log')->where(['user_id'=>$data['user_id'],'cash_id'=>$data['cash_id']])->update(['is_success' => 1]);

        if ($save_rule) {
            return [
                'status_code' => 1
            ];
        }else{
            self::setError([
                    'status_code'=> 500,
                    'message'    => 'Update failure'
                ]
            );
            return false;
        }
    }

}