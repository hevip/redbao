<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:22
 */
namespace app\pay\service;

use app\common\model\Cash;
use app\common\service\BaseService;
use think\Db;
use think\Loader;

class CashService extends BaseService
{
    /**
     * 微信提现申请
     */
    public static function cash_apply($uid,$data)
    {
        //验证提交的金额
        if (!is_numeric($data['cash_money']) || $data['cash_money'] < 1) {
            self::setError([
                'status_code'=> '500',
                'message'    => 'The money is not a number',
            ]);
        }

        //提现订单号
        $cash_no = 'WJTX'.date('YmdHis').rand(1000,9999);

        //数据准备
        $save_data = [
            'user_id'      => $uid,
            'cash_money'   => $data['cash_money'],
            'server_money' => $data['cash_money']*0.02,
            'cash_no'      => $cash_no,
            'create_time'  => time(),
        ];

        //创建订单
        $cashModel = new Cash();
        $saveCode = $cashModel->saveCashLog($save_data);

        if (!$saveCode['status_code']) {
            self::setError([
                'status_code' => '500',
                'message'     => 'Storage data failure'
            ]);
            return false;
        }else{
            return [
                'code' => 1
            ];
        }
    }

}