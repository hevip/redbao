<?php
namespace app\pay\controller;

use think\Controller;
use app\common\service\PayBackService;
use think\Log;

class PayRefund extends Controller{

    public function refunds()
    {
        $list = payBackService::refunds();
    }
}