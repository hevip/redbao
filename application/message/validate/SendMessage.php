<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-11
 * Time: 下午3:21
 */

namespace app\message\validate;

use think\Validate;
class SendMessage extends Validate
{
    protected $rule = [
        'red_id' =>'require|number'
    ];

}