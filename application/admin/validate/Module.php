<?php

/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-10
 * Time: 上午11:41
 */
namespace app\admin\validate;

use think\Validate;
class Module extends Validate
{
    protected $rule = [

        'title' =>'require',
        'parent_id'=> 'require',
        'ctl'   => 'require',
        'act'=> 'require',
        'visible' => 'require|boolean'

    ];
    protected $message= [
        'title.require' => '功能名称不能为空',
        'ctl.require'   => '控制器不能为空',
        'act.require'=> '方法不能为空',
        'parent_id'     => 'parent_id不能为空',
        'visible.require'=> 'visible不能为空',
        'visible.boolean' => 'visible的值只能为布尔值',
        'visible.between'=> 'visible的值只能取１和０之间'
    ];

}