<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/9
 * Time: 上午10:00
 */
namespace app\audio\validate;

use think\Validate;

class Speech extends Validate
{
    protected $rule=[
        //处理结果id
        'audioUrl'=>'require',
    ];
}