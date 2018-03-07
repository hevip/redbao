<?php
namespace app\userlist\service;


use app\common\service\BaseService;
use think\Db;

class UserlistService extends BaseService
{
    /*
     * 用户列表
     */
    public static function user($user)
    {
        if(empty($user['user_name'])){
            $res = Db::name('users')->page($user['page'],10)->order('user_id desc')->select();
            if($res){
                $data['total'] = Db::name('users')->count();
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
                    ->page($user['page'],10)
                    ->order('user_id desc')
                    ->select();
                if ($res) {
                    $data['total'] = Db::name('users')->where('user_name','like','%'.$user['user_name'].'%')->count();
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

    public static function is_del($user)
    {
        if(!is_numeric($user['user_id'])){
            self::setError(['status_code' => 4001, 'message' => 'user_id参数不合法']);
            return false;
        }
        if($user['is_del'] != 0 && $user['is_del'] != 1){
            self::setError(['status_code' => 4001, 'message' => 'is_del参数不合法']);
            return false;
        }
        $res = Db::name('users')->where('user_id',$user['user_id'])->setField('is_del',$user['is_del']);
        if($res){
            if($user['is_del'] == 1){
                return ['msg'=>'该用户已被禁用'];
            }else{
                return ['msg'=>'该用户已开启'];
            }
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙，请稍候在试']);
            return false;
        }
    }
}