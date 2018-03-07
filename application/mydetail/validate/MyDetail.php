<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-11
 * Time: 下午3:21
 */

namespace app\mydetail\validate;

use think\Validate;
class MyDetail extends Validate
{
    protected $rule = [
        'page' =>'require|number',
        'user_name'=>'chsAlphaNum'
    ];

}