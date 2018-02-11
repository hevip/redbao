<?php
namespace app\userlist\service;


use app\common\service\BaseService;
use think\Db;

class UserlistService extends BaseService
{
    /*
     * 用户列表
     */
    public static function user($page,$user)
    {
        if(empty($user)){
            $res = Db::name('users')
                ->where('is_del', 0)->page($page, 10)->order('user_id desc')->select();
            if($res){
                $data['total'] = Db::name('users')->where('is_del',0)->count();
                $data['list'] = $res;
                return $data;
            }else {
                self::setError(['status_code' => 500, 'message' => '服务器忙，请稍候在试']);
                return false;
            }
        }else{
            $validate = validate('userlist');
            if(!$validate->check($user)){
                self::setError(['status_code'=>4004,'message'=>$validate->getError()]);
                return false;
            }else{
                $res = Db::name('users')
                    ->where('user_name','like','%'.$user['user_name'].'%')
                    ->where('is_del',0)
                    ->page($page,10)
                    ->order('user_id desc')
                    ->select();
                if ($res) {
                    $data['total'] = Db::name('users')->where('is_del',0)->where('user_name','like','%'.$user['user_name'].'%')->count();
                    $data['list'] = $res;
                    return $data;
                }elseif(empty($res)){
                    self::setError(['status_code' => 500, 'message' => '没有找到相关数据']);
                    return false;
                }else{
                    self::setError(['status_code' => 500, 'message' => '服务器忙，请稍候在试']);
                    return false;
                }
            }
        }
    }
}