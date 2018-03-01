<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-11
 * Time: 下午3:21
 */

namespace app\admin\validate;

use think\Validate;
class Configure extends Validate
{
    protected $rule = [
        'id'      => 'number',
        'setitem' => 'require',
        'item'   => 'require',
    ];
    protected $message= [
        'id'             => 'id不是数字',
        'setitem'        => '配置项不能为空',
        'item'           => '配置内容不能为空'
    ];
}