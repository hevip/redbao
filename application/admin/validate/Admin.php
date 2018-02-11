<?php
namespace app\admin\validate;

use think\Validate;
class Admin extends Validate
{
    protected $rule = [       
        'account' => 'require|unique:admin,account',
        'password' => 'require',
        'roles'    => 'require|array',
    ];
    protected $message = [
        'account.require' => '账号不能为空',
        'account.uniique' => '当前登录名已经存在',
        'password.require'=> '密码不能为空',
        'roles.require'   => '角色不能为空',
        'roles.array'     => '角色参数格式不对',
    ];

    protected $scence = [
    		'save' => [
    			"account"  => 'require|alphaDash|max:15|unique:admin,account',
	            "password" => 'require|alphaDash|max:32',   
	            "nickname" => 'require|chsDash|max:50',
                'roles'    => 'require|array|',
    		],
    		'update' => [
    			"nickname" => 'require|chsDash|max:50',   
    		],
    ];
}