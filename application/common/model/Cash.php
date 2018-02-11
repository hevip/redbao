<?php
namespace app\common\model;

use think\Db;
use app\common\model\Base;

class Cash extends Base
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'wj_cash_log';

    public static function saveCashLog($data)
    {

        $add_result = self::insert($data);

        return [
            'status_code' =>$add_result
        ];
    }

    public static function upPayOrder($data)
    {

        $up_result = self::where('send',$data['order_sn'])->update(['is_pay' => 1]);

        return  [
            'status_code' =>$up_result
        ];
    }
}