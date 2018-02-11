<?php
namespace app\common\model;

use think\Db;
use app\common\model\Base;

class PayOrders extends Base
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'wj_send';

    public static function savePayOrder($data)
    {

        $add_result = self::insert($data);

        $return_data = self::getLastInsID();

        return [
            'data' =>$return_data
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