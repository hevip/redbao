<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/8
 * Time: 下午2:54
 */
namespace app\audio\service;

use app\common\service\BaseService;
use baidu\AipSpeech;
use greatsir\RedisClient;
use \Qiniu\Auth;
use think\Db;
use think\Log;

class AudioService extends BaseService
{
    /*http://p3d2b4a3b.bkt.clouddn.com/7HzykXmU886Q1aTtv_GhDFpM1mo=/FnwNueKLe60wGKWLLDbLxFL0M9So
     * 获取上传的token
     */
    public static function getUploadToken()
    {
        try{
            $accessKey=config('qiniu_accesskey');
            $secretKey=config('qiniu_secretKey');
            $bucket   = config('qiniu_bucket');
            $qiniu = new Auth($accessKey,$secretKey);
            $noticeUrl=config('qiniu_notify');
            //上传策略
            $audioFormat='avthumb/wav/ab/16k';
            $policy = array(
                'persistentOps' => $audioFormat,
                'persistentPipeline' => "audio-pipe",
                'persistentNotifyUrl' => $noticeUrl,
            );
            $upToken = $qiniu->uploadToken($bucket,null,7200,$policy,true);
            return $upToken;
        }catch (\Exception $e){
            throw new \think\Exception($e->getMessage(),$e->getCode());
            return false;
        }
    }
    /*
     * 语音识别
     */
    public static function speech($data)
    {
        $validate = validate('app\audio\validate\Speech');
        if(!$validate->check($data)){
            self::setError([
                'status_code'=>4101,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        $app_id = config('baiduspeech_appid');//
        $api_key= config('baiduspeech_apikey');
        $secret_key = config('baiduspeech_secret');
        $client = new AipSpeech($app_id,$api_key,$secret_key);
        $audioFile = file_get_contents($data['audioUrl']);
        $res = $client->asr($audioFile,'pcm',16000,array('lan'=>'zh'));
        if($res['err_no']==0){
            //拿到逗号前面的字符，然后转换成拼音
            $str = $res['result'][0];
            Log::write('百度语音识别结果：'.$str);
            /*Log::write('百度语音识别结果：'.$str);
	        preg_match_all("/([\x{4e00}-\x{9fa5}]+)/u", $str, $match);
	        $content = $match[0][0];
            Log::write('裁剪后的结果：'.$content);*/
            $str = urlencode($str);
            $str=preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99)+/",'',$str);
            $str=urldecode($str);//将过滤后的关键字解码
            $content = implode('',self::chinanum($str));
            Log::write('裁剪后的结果：'.$content);
            return $content;

        }else{
            return false;
        }
    }
    public static function chinanum($num){
        $china=array('零','一','二','三','四','五','六','七','八','九');
        $arr=self::mb_str_split($num);//
        for($i=0;$i<count($arr);$i++){

            if(is_numeric($arr[$i])){
                $arr[$i] = $china[$arr[$i]];
            }
            //echo $china[$arr[$i]];

        }
        return $arr;
    }
    public static function mb_str_split($str)
    {
        return preg_split('/(?<!^)(?!$)/u', $str );
    }
    /*
     * 发送资源的id,本地检测对应的文本，然后判断文本和红包的结果
     */
    public static function getSpeechRes($data,$uid)
    {

        $userInfo = Db::name('users')->where(['user_id'=>$uid])->find();
        $validate = validate('app\audio\validate\SpeechCheck');
        if(!$validate->check($data)){
            self::setError([
                'status_code'=>4102,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
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
        $speech = Db::name('speech')->where(['persistentid'=>$data['persistentid']])->find();
        $redInfo = Db::name('send')->where(['red_id'=>$data['red_id'],'is_pay'=>1])->find();
        if(empty($redInfo)){
            self::setError([
                'status_code'=>4105,
                'message'    => '红包未找到'
            ]);
            return false;
        }
        $content2 = $redInfo['content_pinyin'];
        $res = self::judge($speech['content'],$content2);
        if($res&&!empty($speech['content'])&&!empty($content2)) {
            //判断当前红包是否是推荐广告id，是的话，查看当前的key的值是否是100的倍数，如果是则给，不是则不给

            //$money =1.2;//从redis里面取出来
            $redis = RedisClient::getHandle(0);
           /* $ids_str = $redis->getKey('ad_red_ids');
            if($ids_str){
                $ids = explode(',',$ids_str);
                if(in_array($data['red_id'],$ids)){
                    $key = $redis->keyIncr('ad_red_package:'.$data['red_id']);
                    if($key%100!=0){
                        //返回
                        return [
                            'result'=>false,
                            'money'=>isset($money)?$money:'',
                            'user_icon'=>$userInfo['user_icon'],
                            'user_name'=>$userInfo['user_name'],
                            'time'=>date('m-d h:i'),
                            'voice_url'=>$speech['voice_url']
                        ];
                    }
                }
            }*/
            if($redis->in_set('adv_reds',$data['red_id'])){
                $key = $redis->keyIncr('ad_red_package:'.$data['red_id']);
                if($key%100!=0){
                    //返回
                    return [
                        'result'=>false,
                        'money'=>isset($money)?$money:'',
                        'user_icon'=>$userInfo['user_icon'],
                        'user_name'=>$userInfo['user_name'],
                        'time'=>date('m-d h:i'),
                        'voice_url'=>$speech['voice_url']
                    ];
                }
            }



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
                $receData['voice_url']= $speech['voice_url'];
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
        }else{
            $res = false;
        }

        //返回头像，昵称，音频，以及当前时间
        return [
            'result'=>$res,
            'money'=>isset($money)?$money:'',
            'user_icon'=>$userInfo['user_icon'],
            'user_name'=>$userInfo['user_name'],
            'time'=>date('m-d h:i'),
            'voice_url'=>$speech['voice_url']
        ];



    }
    /*
     *
     */
   public static function judge($content_a,$content_b)
   {
       //判断两者相似度
       $a = explode(',',$content_a);
       $b = explode(',',$content_b);
       Log::write('输入的数组：'.$content_a);
       Log::write('红包的数组：'.$content_b);
       $rate = Db::name('backstage')->where(['id'=>10])->value('item');
       $rate = (float)$rate/100;
       $c  = array_intersect($a,$b);
       Log::write('识别的交集是：'.json_encode($c));
       $rateReal = count($c)/count($b);
       Log::write('通过率是：'.$rateReal);
       if($rateReal>=$rate){
           return true;
       }else{
           return false;
       }
   }


}
