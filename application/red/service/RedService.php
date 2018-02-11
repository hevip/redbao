<?php
namespace app\Red\service;


use app\common\service\BaseService;
use think\Db;

class RedService extends BaseService
{
    public static function red_list($red)
    {
        $validate = validate('app\red\validate\Red');
        if(!$validate->check($red)){
            self::setError([
                'status_code'=>4105,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        $arr = array();
        $num = Db::name('send')->where('red_id',$red['red_id'])->field('se_money,se_number,receive')->find();
        if($num){
            $res = Db::name('received')->where('red_id',$red['red_id'])
                ->field('user_id,re_money,voice_url,create_time')->select();
            if($res){
                foreach($res as $k=>$v){
                    $arr[] = Db::name('users')->where('user_id',$v['user_id'])->field('user_name,user_icon')->find();
                }
                $info['receive_info'] = $res;
                $info['user_info'] = $arr;
                $info['num'] = $num;
                return $info;
            }else{
                $info['num'] = $num;
                $info['message'] = '红包还没被领取过';
                return $info;
            }
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙，请稍候在试']);
            return false;
        }
    }

}