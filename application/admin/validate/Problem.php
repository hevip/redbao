<?php
namespace app\admin\validate;

use think\Validate;
class Problem extends Validate
{
    protected $rule = [
        "account"  => 'require',
        "password" => 'require',
    ];
    protected $message = [
        'title.require' => '标题不能为空',
        'content.require' => '内容不能为空',
    ];

    protected $scence = [
        'problem_add' => [
            "title"  => 'require',
            "content" => 'require',
        ],
    ];
}