<?php
namespace app\common\validate;

use think\Validate;
class Admin extends Validate
{
    protected $rule = [       
            "account" => 'alphaDash|max:15',
            "password" => 'alphaDash|max:32',   
            "nickname" => 'chsDash|max:50',   
            "last_login" => 'number|max:20', 
            "last_ip" => 'ip',
            "is_del" => 'number|max:1', 
    ];

    protected $scene = [
    		"last_login_info" => [
    			"last_login" => 'require|number|max:20', 
            	"last_ip" => 'require|ip',
    		],
    ];
}