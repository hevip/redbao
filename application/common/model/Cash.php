<?php
namespace app\common\model;

use think\Db;
use app\common\model\Base;

class Cash extends Base
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'wj_cash_log';

    public static function saveCashLog($uid,$data,$new_balance)
    {
        Db::startTrans();
        try{
            $add_result = self::insert($data);
            $return_data = Db::name('users')->where('user_id',$uid)->update(['user_balance' => $new_balance]);
            // 提交事务
            Db::commit();
            return $return_data;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }


    }

    public static function upPayOrder($data)
    {
        $up_result = self::where('send',$data['order_sn'])->update(['is_pay' => 1]);
        return  [
            'status_code' =>$up_result
        ];
    }
}