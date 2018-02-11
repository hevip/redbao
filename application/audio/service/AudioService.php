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
            $content = substr($str,0,strpos($str, '，'));
            Log::write('裁剪后的结果：'.$content);
            return $content;
        }else{
            return false;
        }
    }
    /*
     * 发送资源的id,本地检测对应的文本，然后判断文本和红包的结果
     */
    public static function getSpeechRes($data,$uid)
    {

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
            self::setError([
                'status_code'=>4104,
                'message'    => '您已经领取过了'
            ]);
            return false;
        }
        //获取识别的内容
        $speech = Db::name('speech')->where(['persistentid'=>$data['persistentid']])->find();
        $redInfo = Db::name('send')->where(['red_id'=>$data['red_id']])->find();
        if(empty($redInfo)){
            self::setError([
                'status_code'=>4105,
                'message'    > '红包未找到'
            ]);
            return false;
        }
        $content2 = $redInfo['content_pinyin'];
        if($speech['content']==$content2&&!empty($speech['content'])&&!empty($content2)) {

            //$money =1.2;//从redis里面取出来
            $redis = RedisClient::getHandle(0);
            $money = $redis->popList('red_money:'.$data['red_id']);
            if(!$money){
                self::setError([
                    'status_code'=>4106,
                    'message'    =>'已经被领取完了'
                ]);
                return false;
            }
            Db::startTrans();
            try{
                $receData['user_id'] = $uid;
                $receData['re_money']= $money;
                $receData['voice_url']= $speech['voice_url'];
                $receData['red_no'] = $data['red_id'];
                $receData['is_success']=1;
                $receData['create_time']= time();
                Db::name('received')->insert($receData);//插入领取表
                Db::name('send')->where(['red_id'=>$data['red_id'],'version'=>$redInfo['version']])->setInc('receice',1);
                Db::name('send')->where(['red_id'=>$data['red_id'],'version'=>$redInfo['version']])->setInc('version',1);
                Db::name('users')->where([
                    'user_id'=>$uid
                ])->setInc('user_balance',$money);
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
        return ['result'=>$res,'money'=>isset($money)?:''];



    }
    /*
     * 根据红包id随机获取红包金额
     * @params string $red_id
     * @return float $money
     */
    public static function getRedMoney($red_id)
    {
        //随机红包算法

    }


}