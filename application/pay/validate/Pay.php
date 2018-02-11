<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:30
 */

namespace app\pay\validate;

use think\Validate;

class Pay extends Validate
{

    protected $rule = [
//        'user_id' => 'require|number',
        'pay_money' => 'require|number',
    ];
    protected $msg = [
        'email' => '邮箱格式错误',
    ];
    protected $scene = [
        'pay_create'  =>  ['pay_money'],
        'pay_success' =>  ['']
    ];

}