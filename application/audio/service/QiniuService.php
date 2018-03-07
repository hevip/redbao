<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/2/9
 * Time: 下午12:51
 */

namespace app\audio\service;

use app\common\service\BaseService;
use greatsir\WebSocketClient;
use Overtrue\Pinyin\Pinyin;
use Qiniu\Auth;
use think\Db;
use think\Log;
use think\Request;
use WebSocket\Client;
use greatsir\RedisClient;
use GuzzleHttp\Client as GClient;
class QiniuService extends BaseService
{
    public static function callback()
    {
        try{
            /*$accessKey=config('qiniu_accesskey');
        $secretKey=config('qiniu_secretKey');
        $bucket   = config('qiniu_bucket');
        $auth = new Auth($accessKey, $secretKey);*/
            $q_domain = config('qiniu_bucket_domain');
            //获取回调的body信息
            $callbackData = Request::instance()->post();
            //$callbackBody = file_get_contents('php://input');
            $callbackBody = $callbackData;
            //回调的contentType
            //$contentType = 'application/x-www-form-urlencoded';
            //判断是否转换成功

            $items = $callbackBody['items'];
            $pres = $items[0];
            if($callbackBody['code']==0){
                //成功，请求百度的语音识别接口
                Log::write('文件地址:'.$q_domain.'/'.$pres['key']);
                $speech_res = AudioService::speech([
                    'audioUrl'=> $q_domain.'/'.$pres['key']
                ]);
                Log::write('识别结果:'.json_encode($speech_res,JSON_UNESCAPED_UNICODE));
                if($speech_res){
                    //识别成功

                    $pinyin = new Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
                    //带声调识别
                    //$content_pinyin = implode(',',$pinyin->convert($speech_res,PINYIN_ASCII));
                    //不带声调识别
                    $content_pinyin = implode(',',$pinyin->convert($speech_res));
                    $speechData['persistentid'] = $callbackBody['id'];
                    $speechData['voice_url'] = $q_domain.'/'.$callbackBody['inputKey'];
                    $speechData['content'] = $content_pinyin;
                    $speechData['create_time']=time();
                }else{
                    $speechData['persistentid'] = $callbackBody['id'];
                    $speechData['voice_url'] = $q_domain.'/'.$callbackBody['inputKey'];
                    $speechData['content'] = '';
                    $speechData['create_time']=time();
                }
            }else{
                $speechData['persistentid'] = $callbackBody['id'];
                $speechData['voice_url'] = $q_domain.'/'.$callbackBody['inputKey'];
                $speechData['content'] = '';
                $speechData['create_time']=time();

            }
            $count= Db::name('speech')->where(['persistentid'=>$callbackData['id']])->count();
            if($count==0){
                Db::name('speech')->insert($speechData);//插入到结果表
            }

            $resp = array('ret' => 'success');

            echo json_encode($resp);
            //连接socker服务器
	    //$redis = RedisClient::getHandle(0);
            //$redis->ppush('qiniuId',$callbackData['id']);
            /**原有方式**/
	        /*$client = new Client('ws://127.0.0.1:8083');
            $data = json_encode([
                'controller_name'=>'AppController',
                'method_name'=>'getNotice',
                'data'=>$callbackData['id']
            ]);
            $client->send($data);*/
            $url = 'http://localhost:8081/AppController/notice?uid='.$callbackData['id'];
            $client = new GClient();
            $response = $client->get($url);
            $res =$response->getBody()->getContents();
	    //echo $client->receive();
            //$client->close();
            /*//回调的签名信息，可以验证该回调是否来自七牛 屏蔽验证
            $header = Request::instance()->header();
            $authorization = $header['authorization'];

            //七牛回调的url，具体可以参考：http://developer.qiniu.com/docs/v6/api/reference/security/put-policy.html
            $url = config('qiniu_notify');

            $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);

            if ($isQiniuCallback) {
                Log::write('七牛回调内容是:',json_encode($callbackBody));
                //$resp = array('ret' => 'success');
            } else {
                //$resp = array('ret' => 'failed');
            }

            //echo json_encode($resp);*/
        }catch (\Exception $e){

            throw new \think\Exception($e->getMessage().' in '.$e->getFile().'行'.$e->getLine(),$e->getCode());
        }



    }


    public static function speechNew($data,$uid)
    {
        //格式转换
        //$sh = '';
        $wav = 'uploads/audio/'.$uid.'.wav';
        $voice = "http://".$data['voice_url'];
        $array = ['./change.sh',$voice,$wav];
        //$command = implode(' ',$array);
        //$command = strval($command);
        //$command = "./change.sh {$voice} {$wav}";
        $command = "/usr/local/ffmpeg/bin/ffmpeg -i {$voice} {$wav}";
        //echo $command.'---------';
        $command1 = $command;
        //$command = "./change.sh http://p3d2b4a3b.bkt.clouddn.com/tmp_03a47769705f89953064addc331aa2effe082e913f8484b5.mp3 74662915305970001.wav";
        //echo $command1;
        //echo getcwd();
        //Log::write('命令是：'.$command);
        exec( escapeshellcmd($command1), $output, $return_val);
        Log::write('命令执行结果是：'.json_encode($output));
        Log::write('命令行执行结果返回值：'.json_encode($return_val));
        if($return_val){
            $speech_res = false;
        }else{
            if(!file_exists($wav)){
                Log::write('没有生成文件');
            }
            $speech_res = AudioService::speech([
                'audioUrl'=> $wav
            ]);
        }


        //删除文件
        unlink($wav);
        Log::write('识别结果:'.json_encode($speech_res,JSON_UNESCAPED_UNICODE));
        if($speech_res){
            //识别成功
            Log::write('识别成功结果:'.json_encode($speech_res,JSON_UNESCAPED_UNICODE));
            $pinyin = new Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
            $content_pinyin = implode(',',$pinyin->convert($speech_res));
            Log::write('拼音结果是:'.$content_pinyin);
            $speechData['persistentid'] = $data['voice_id'];
            $speechData['voice_url'] = $data['voice_url'];
            //$speechData['voice_']
            $speechData['content'] = $content_pinyin;
            $speechData['create_time']=time();
        }else{
            $speechData['persistentid'] = $data['voice_id'];
            $speechData['voice_url'] = $data['voice_url'];
            $speechData['content'] = '';
            $speechData['create_time']=time();
        }
        /*if(file_exists($wav)){
            //Log::write('文件地址:'.$q_domain.'/'.$pres['key']);
            $speech_res = AudioService::speech([
                'audioUrl'=> $wav
            ]);
            //删除文件
            unlink($wav);
            Log::write('识别结果:'.json_encode($speech_res,JSON_UNESCAPED_UNICODE));
            if($speech_res){
                //识别成功

                $pinyin = new Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
                $content_pinyin = implode(',',$pinyin->convert($speech_res,PINYIN_ASCII));
                $speechData['persistentid'] = $data['voice_id'];
                $speechData['voice_url'] = $data['voice_url'];
                $speechData['content'] = $content_pinyin;
                $speechData['create_time']=time();
            }else{
                $speechData['persistentid'] = $data['voice_id'];
                $speechData['voice_url'] = $data['voice_url'];
                $speechData['content'] = '';
                $speechData['create_time']=time();
            }
        }else{
            //格式转换失败
            $speechData['persistentid'] = $data['voice_id'];
            $speechData['voice_url'] = $data['voice_url'];
            $speechData['content'] = '';
            $speechData['create_time']=time();
        }*/
        Db::name('speech')->insert($speechData);
        $params['persistentid'] = $data['voice_id'];
        $params['red_id']       = $data['red_id'];
        $params['duration']     = $data['duration'];
        return AudioService::getSpeechRes($params,$uid);

    }
}
