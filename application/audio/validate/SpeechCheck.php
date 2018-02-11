<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/9
 * Time: 下午2:26
 */

namespace app\audio\validate;


use think\Validate;

class SpeechCheck extends Validate
{
    protected $rule=[
        'persistentid'=>'require',
        'red_id'      => 'require'
    ];
}