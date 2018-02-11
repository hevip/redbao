<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-11
 * Time: 下午3:21
 */

namespace app\Userlist\validate;

use think\Validate;
class Userlist extends Validate
{
    protected $rule = [
        'user_name' =>'chsDash'
    ];
    protected $message= [
        'user_name.chsDash' => '搜索名称不能包含特殊字符',

    ];

}