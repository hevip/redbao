<?php
namespace app\red\service;

use app\common\service\BaseService;
use app\red\model\Red;
use greatsir\RedisClient;
use think\Db;
use think\Log;

class RedService extends BaseService
{
    public static function adRed()
    {
        //
        $res = Db::name('advertisement')->where(['is_show'=>1])->limit(3)->select();
        return $res;
    }
    public static function getAllAd($page=1)
    {
        $res = Db::name('advertisement')->page($page,20)->select();
        $count = Db::name('advertisement')->count();
        $data['count'] = $count;
        $data['list']  = $res;
        return $data;
    }
    public static function setAdRed($data)
    {
        $redis = RedisClient::getHandle(0);
        if(isset($data['id'])){
            //更新
            try{
                if($data['is_show']==1){
                    $redis->add_set('adv_reds',$data['red_id']);
                }else{
                    $redis->del_set('adv_reds',$data['red_id']);
                }
                $res = Db::name('advertisement')->where(['id'=>$data['id']])->update($data);
                return ['up_time'=>time()];
            }catch (\Exception $e){
                throw new \think\Exception($e->getMessage(),$e->getCode());
                self::setError(['status_code'=>500,'message'=>'网络请求错误']);
                return false;
            }

        }else{
            //添加
            try{
                Db::name('advertisement')->insert($data);
                $redis->add_set('adv_reds',$data['red_id']);
                return ['up_time'=>time()];
            }catch (\Exception $e){
                throw new \think\Exception($e->getMessage(),$e->getCode());
                self::setError(['status_code'=>500,'message'=>'网络请求错误']);
                return false;
            }


        }
    }
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
    /*
     *
     */
    public static function read($red_id,$uid)
    {

       $redModel = new Red();
       $redInfo = $redModel->with('getUserInfo')->where(['red_id'=>$red_id])->find();
       if(!empty($redInfo)){
           //查看用户是否已经领取
           $redis = RedisClient::getHandle(0);

           if($redis->in_set('red_package:'.$red_id,$uid))
           {
               $redInfo['is_revice'] = true;
               $redInfo['re_money']= Db::name('received')->where([
                   'red_id'=>$red_id,
                   'user_id'=>$uid
               ])->value('re_money');
           }else{
               $redInfo['is_revice'] = false;
               $redInfo['re_money'] = 0;
           }
           return $redInfo;
       }else{
           self::setError([
               'status_code'=>404,
               'message'     =>'请求内容不存在'
           ]);
           return false;
       }
    }
    /*
     * 领取红包
     */
    public static function getMoney($data,$uid)
    {
        //dump($data);die;
        $userInfo = Db::name('users')->where(['user_id'=>$uid])->find();
        /*
         * 判断是否已经抢过了
         */
        $redis = RedisClient::getHandle(0);

        if($redis->in_set('red_package:'.$data['red_id'],$uid))
        {
            /*self::setError([
                'status_code'=>4104,
                'message'    => '您已经领取过了'
            ]);
            return false;*/
            return [
                'result'=>false,
                'message'=>'您已经领取过了'
            ];

        }

        //获取识别的内容
        //$speech = Db::name('speech')->where(['persistentid'=>$data['persistentid']])->find();
        $redInfo = Db::name('send')->where(['red_id'=>$data['red_id'],'is_pay'=>1,'type'=>1])->find();
        if(empty($redInfo)){
            self::setError([
                'status_code'=>4105,
                'message'    => '红包未找到'
            ]);
            return false;
        }
        //$money =1.2;//从redis里面取出来
        $redis = RedisClient::getHandle(0);
        $money = $redis->popList('red_money:'.$data['red_id']);
        if(!$money){
            /*self::setError([
                'status_code'=>4106,
                'message'    =>'已经被领取完了'
            ]);
            return false;*/
            return [
                'result'=>false,
                'message'=>'已经被领取完了'
            ];
        }
        Db::startTrans();
        try{
            $receData['user_id'] = $uid;
            $receData['re_money']= $money;
            $receData['voice_url']= '';
            $receData['red_id'] = $data['red_id'];
            $receData['is_success']=1;
            $receData['create_time']= time();
            Db::name('received')->insert($receData);//插入领取表
            Db::name('send')->where(['red_id'=>$data['red_id'],'version'=>$redInfo['version']])->setInc('receive',1);
            Db::name('send')->where(['red_id'=>$data['red_id'],'version'=>$redInfo['version']])->setInc('version',1);
            /*Db::name('users')->where([
                'user_id'=>$uid
            ])->setInc('user_balance',$money);*/
            $user_balance = bcadd($userInfo['user_balance'],$money,2);
            Log::write('最新余额:'.$user_balance);
            Db::name('users')->where([
                'user_id'=>$uid
            ])->setField('user_balance',$user_balance);
            $redis->add_set('red_package:'.$data['red_id'],$uid);//加入到集合里面去
            $res = true;
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            throw new \think\Exception($e->getMessage(),$e->getCode());
            return false;
        }


        //返回头像，昵称，音频，以及当前时间
        return [
            'result'=>$res,
            'money'=>isset($money)?$money:'',
            'user_icon'=>$userInfo['user_icon'],
            'user_name'=>$userInfo['user_name'],
            'time'=>date('m-d h:i'),
            'voice_url'=>''
        ];



    }


}