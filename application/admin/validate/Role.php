<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-11
 * Time: 下午3:21
 */

namespace app\admin\validate;

use think\Validate;
class Role extends Validate
{
    protected $rule = [

        'role_name' =>'require|unique:role,role_name&is_del=1',
        'mod_ids'   =>'require|array'

    ];
    protected $message= [
        'role_name.require' => '角色名称不能为空',
        'role_name.unique'  =>'角色名称已经存在，请更换一个',
        'mod_ids.require'   => '权限不能为空',
        'mod_ids.array'     => '权限数据格式不对',

    ];
}