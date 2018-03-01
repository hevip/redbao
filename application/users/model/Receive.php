<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/23
 * Time: 下午2:35
 */

namespace app\users\model;

use think\Model;
class Receive extends Model
{
    protected $name='received';

    public function userInfo()
    {
        return $this->hasOne('app\users\model\User','user_id','user_id')->field('user_id,user_name,user_icon');
    }
    public function redInfo()
    {
        return $this->hasOne('app\red\model\Red','red_id','red_id')->field('content,red_id,type');
    }
}