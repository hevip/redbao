<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-11
 * Time: 下午3:21
 */

namespace app\admin\validate;

use think\Validate;
class Rule extends Validate
{
    protected $rule = [

        'rules' =>'require',


    ];
    protected $message= [
        'rules.require' => '规则说明不能为空',
    ];
}