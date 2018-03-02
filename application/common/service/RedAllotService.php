<?php
namespace app\common\service;

use think\Controller;
use think\Log;

/**
 * @author: helei
 * @createTime: 2016-07-20 18:31
 * @description:
 */

/**
 * 客户端需要继承该接口，并实现这个方法，在其中实现对应的业务逻辑
 * Class TestNotify
 * anthor helei
 */

class RedAllotService extends Controller
{
    public static function getRedArray($money,$num,$type)
    {
        if ($type) {
            //生成随机红包并加入redis
            $total = $money;
            $min=0.01;//每个人最少能收到0.01元
            $money_arr=array(); //存入随机红包金额结果

            for ($i=1;$i<$num;$i++)
            {
                $safe_total=($total-($num-$i)*$min)/($num-$i);//随机安全上限
                $money= mt_rand($min*100,$safe_total*100)/100;
                $total=$total-$money;
                $money_arr[]= $money;
            }
            $money_arr[] = round($total,2);

        }else{
            $newMoney = bcdiv($money,$num,2);
            for ($i=1;$i<$num;$i++)
            {
                $money_arr[]= $newMoney;
            }
            $money_arr[] = round($newMoney,2);
        }
        return $money_arr;
    }
}