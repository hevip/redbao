<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-11
 * Time: 下午3:21
 */

namespace app\red\validate;

use think\Validate;
class Red extends Validate
{
    protected $rule = [
        'red_id' =>'require|number'
    ];

}