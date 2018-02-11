<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-9-6
 * Time: 上午10:04
 */
namespace app\commpont\validate;

use think\Validate;
use greatsir\RedisClient;
use app\member\validate\Member;
class Sms extends Validate
{
    protected $rule=[
        'mobile' => 'require',
    ];

    protected $message=[
        'mobile.require'=> '手机号不能为空',
    ];
    //注册场景
    protected $scene = [
        'register'  =>  ['mobile'=>'checkMobile:'],
    ];

    /**
     *
     */
    protected function checkMobile($value)
    {
        if(!isset($value)){
            return '手机号不能为空';
        }
        $v_member = new Member();
        $res = $v_member->checkUnique($value);
        if($res){
            //设置
            return '手机号已经注册';
        }
        return true;

    }
}