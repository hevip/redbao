<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/22
 * Time: 下午5:03
 */

namespace app\message\model;


use think\Model;

class Message extends Model
{
    protected $name='send';

    public function getUserInfo()
    {
        return $this->hasOne('app\users\model\User','user_id','user_id')->field('user_id,user_name,user_icon');
    }
}