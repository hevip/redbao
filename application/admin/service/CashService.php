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
        if(empty($page)|| $page < 1){
            $pages = 50;
        }else{
            if($page >9 ){
                $pages = 50;
            }else{
                $pages = 10+$page*5;
            }
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
        if (isset($data['name'])) {
            $user_name = Db::name('user')->where('user_name','like',$data['name'])->field('user_id')->find();
            if (!empty($user_name)) {
                $condition['user_id'] = $user_name['user_id'];
            }
        }
        //是否搜索时间
        if (isset($data['start_time']) and isset($data['end_time'])) {
            $showData = Db::name('cash_log')->where('create_time','between',$data['start_time'].','.$data['end_time'])->where($condition)->order('create_time desc')->limit($pages)->field('cash_id,user_id,cash_money,server_money,pay_no,trade_no')->select();
        }else{
            $showData = Db::name('cash_log')->where($condition)->order('create_time desc')->limit($pages)->field('cash_id,user_id,cash_money,server_money,pay_no,trade_no')->select();
        }

        if (empty($showData)) {
            self::setError([
                'status_code'=>500,
                'message'    =>"Query data is empty"
            ]);
            return false;
        }
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