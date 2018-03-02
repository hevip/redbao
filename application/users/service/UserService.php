<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/1/26
 * Time: 下午4:22
 */
namespace app\users\service;


use app\common\service\BaseService;
use app\users\model\Receive;
use app\users\model\User;
use greatsir\RedisClient;
use greatsir\Snowflake;
use greatsir\wechat\WXBizDataCrypt;
use function GuzzleHttp\Psr7\str;
use Qiniu\Storage\UploadManager;
use think\Cache;
use think\Db;
use Firebase\JWT\JWT;
use think\Log;
use Qiniu\Auth;
class UserService extends BaseService
{
    /*
     * 获取用户信息
     */
    public static function getUserInfo($data,$uid)
    {
        $userInfo = self::read($uid);
        if(!$userInfo){
            return false;
        }
        $openid = $userInfo['user_openid'];
        try{
            //校验用户信息
            $redis = RedisClient::getHandle();
            $session_key = $redis->getKey('openid:'.$openid);
            $appid = config('wechatapp_id');
            //解密数据，以及验证签名
            $pc = new WXBizDataCrypt($appid,$session_key);
            $errCode = $pc->decryptData($data['encryptedData'],$data['iv'],$newData);
            //dump($errCode);die;
            if($errCode==0){
                //解密成功
                //更新用户信息
                $newData = json_decode($newData);
                $upData['user_name'] = $newData->nickName??$uid;
                $upData['user_icon'] = $newData->avatarUrl??'';
                $upData['user_unionid'] = $newData->unionId??'';
                $res = Db::name('users')->where(['user_id'=>$uid])->update($upData);
                if($res||$res==0){
                    $userInfo = self::read($uid);
                    return $userInfo;
                }
            }else{
                self::setError([
                    'status_code'=>$errCode,
                    'message'    =>'数据校验失败'
                ]);
                return false;
            }
        }catch (\Exception $e){
            throw new \think\Exception($e->getMessage(),$e->getCode());
        }


    }

    public static function read($uid)
    {
        $validate = validate('app\users\validate\User');
        if(!$validate->check(['user_id'=>$uid])){
            self::setError([
                'status_code'=>4103,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        $where['user_id'] = $uid;
        $where['is_del']  = 0;
        $userInfo = Db::name('users')->where($where)->find();
        if(!empty($userInfo)){
            return $userInfo;
        }else{
            self::setError([
                'status_code'=>404,
                'message'    =>'用户不存在'
            ]);
            return false;
        }
    }
    /*
     * 检测微信登陆
     * @params array $data 发送的参数
     */
    public static function checkWx($data)
    {
        $validate = validate('app\users\validate\WxLogin');
        if(!$validate->check($data)){
            self::setError([
                'status_code'=>4105,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        $appid = config('wechatapp_id');
        //解密数据，以及验证签名
        $pc = new WXBizDataCrypt($appid,'session_key');

        $user = new User();
        $where['user_openid'] = $data['openid'];
        $where['user_unionid'] = $data['unionid'];
        $res = $user->where($where)->find();
        if(!empty($res)){
            //更新数据
            $result = $res->getData();
            $payload['requesterID'] = $result['user_id'];
            $payload['identity']    = 'yezhu';
            $payload['exp']         = time()+604800;
            $result['token']        = JWT::encode($payload,config('jwt-key'));
            $result['identity']     = 'yezhu';
            return $result;
        }else{
            //创建用户id ,业务1
            $user_data['user_id'] = Snowflake::generateParticle(1);
            $user_data['user_openid'] = $data['openid'];
            $user_data['user_unionid']= $data['unionid'];
            $user_data['user_name']   = $data['nickname']??$user_data['user_id'];
            $user_data['user_icon']   = $data['user_icon']??'';
            $user = new User();
            $res = $user->save($user_data);
            if($res){
                $user_info = self::read($user_data['user_id']);
                if($user_info){
                    $payload['requesterID'] = $user_info['user_id'];
                    $payload['identity']    = 'yezhu';
                    $payload['exp']         = time()+604800;
                    $user_info['token']        = JWT::encode($payload,config('jwt-key'));
                    $user_info['identity']     = 'yezhu';
                    return $user_info;
                }
            }else{
                self::setError([
                    'status_code'=>500,
                    'message'    =>'网络请求错误，请稍后重试'
                ]);
                return false;
            }
        }
    }


    //我的记录头部  1.收到和 2.发出
    public static function record($type,$user_id){
        $user_msg = Db::name('users')->field('user_id,user_name,user_icon')->where('user_id',$user_id)->find();
        if(!$user_msg){
            self::setError([
                'status_code' => 4055,
                'message' =>'请输入正确的用户ID',
            ]);
            return false;
        
	    }
	$where['user_id']=$user_id;
        if($type == 1){
            $Db_name = 'received';
            $re = 're_money';
        }elseif($type == 2){
            $re = 'se_money';
            $Db_name = 'send';
	    $where['is_pay']=1;
        }else{
            self::setError([
                'status_code' => 4055,
                'message' =>'请选择正确的记录',
            ]);
            return false;
        }
        $record = Db::name($Db_name)->field($re)->where($where)->select();
        if($record){
            $res = 0;
            foreach($record as $k =>$v){

                $res = bcadd($res ,$v[$re],2);
            }
            $total['num'] = Db::name($Db_name)->where($where)->count();
            $total['money'] =$res;
        }else{
            $total['num'] = 0;
            $total['money'] = 0;
        }

        $total['user_name'] = $user_msg['user_name'];
        $total['user_icon'] = $user_msg['user_icon'];
        return $total;
    }


    // 我的记录底部 1.我收到的  2.我发出的
    public static function record_list($type,$user_id,$more){
        $user_id  = Db::name('users')->where('user_id',$user_id)->value('user_id');
        if(!$user_id){
            self::setError([
                'status_code' => 4055,
                'message' =>'请输入正确的用户ID',
            ]);
            return false;
        }
	    $where['user_id']= $user_id;
        if($type == 1){
            $Db_name = 'received';
            $re = 're_money';
            $field = 're_money,create_time,red_id';
            $list_name = 'send';
        }elseif($type == 2){
            $Db_name = 'send';
            $re = 'se_money';
            $field = 'content,se_money,create_time,se_number,red_id';
            $list_name = 'received';
	    $where['is_pay']=1;
        }else{
            self::setError([
                'status_code' => 4055,
                'message' =>'请选择正确的记录',
            ]);
            return false;
        }
        if(empty($more) || $more < 1){
            $limit = 0;
        }else{
            $limit = ($more-1) * 5;
        }
        $data = Db::name($Db_name)->where($where)->field($field)->limit($limit,5)->order('create_time desc')->select();
        foreach($data as $k =>$v){
            if($type == 1){
                $re = Db::name($list_name)->where('red_id',$v['red_id'])->find();
                $data[$k]['user_name'] = Db::name('users')->where('user_id',$re['user_id'])->value('user_name');
                $data[$k]['user_icon'] = Db::name('users')->where('user_id',$re['user_id'])->value('user_icon');
            }else{
                $data[$k]['get_num'] = Db::name($list_name)->where('red_id',$v['red_id'])->count();
            }
        }
        if(!$data){
            return '没有记录';
        }else{
            foreach($data as $k =>$v){
                $data[$k]['create_time'] = date('m-d H:i',$v['create_time']);
            }
            return $data;
        }

    }


    //红包详情头部（点击记录后） //1.我收到的  2.我发出的
    public static function red_details($type,$red_id,$user_id){
        $my_msg = Db::name('users')->field('user_name,user_icon,user_id')->where('user_id',$user_id)->find();
        $send_msg = Db::name('send')->where('red_id',$red_id)->find();
        if(!$send_msg){
            self::setError([
                'status_code' => 4055,
                'message' =>'没有该红包',
            ]);
            return false;
        }
        //1.听完声音领红包 2.口令红包  3.说答案领红包
        if($send_msg['type'] == 1){
            $field = 'voice';
            $data['re_type'] = '1';
        }elseif($send_msg['type'] == 2){
            $field ='content';
            $data['re_type'] = '2';
        }elseif($send_msg['type'] == 3){
            $field ='voice';
            $data['re_type'] = '3';
        }else{
            self::setError([
                'status_code' => 500,
                'message' =>'红包种类有误',
            ]);
            return false;
        }
        if($type == 1){
            $data['re_money'] = Db::name('received')->where(['red_id'=>$red_id,'user_id'=>$my_msg['user_id']])->value('re_money');
            $data['send_user_name'] = Db::name('users')->where('user_id',$send_msg['user_id'])->value('user_name');
            $data['send_user_icon'] = Db::name('users')->where('user_id',$send_msg['user_id'])->value('user_icon');
        }elseif($type ==2){
            $data['my_name'] = $my_msg['user_name'];
            $data['my_icon'] = $my_msg['user_icon'];
        }else{
            self::setError([
                'status_code' => 4055,
                'message' =>'请选择发出或收到',
            ]);
            return false;
        }

        $data[$field] = $send_msg[$field];
        $se_red = Db::name('send')->field('se_money,se_number')->where('red_id',$red_id)->find();
        $data['total_money'] = $se_red['se_money'];
        $data['total_number'] = $se_red['se_number'];
        $data['get_number'] = Db::name('received')->where('red_id',$red_id)->count();
        return $data;

    }

//红包详情记录列表
    public static function details_list($red_id,$page){

        /*$re_red  = Db::name('received')->where('red_id',$red_id)->select();
        if(empty($re_red)){
           $data=[];
        }else{
            foreach($re_red as $k =>$v){
                $user_msg = Db::name('users')->field('user_name,user_icon,user_id')->where('user_id',$v['user_id'])->find();
                $user_re  = Db::name('received')->order('create_time desc')->where(['red_id'=>$red_id,'user_id'=>$user_msg['user_id']])->find();
                $data[$k]['user_name'] = $user_msg['user_name'];
                $data[$k]['user_icon'] = $user_msg['user_icon'];
                $data[$k]['re_money'] = $user_re['re_money'];
                $data[$k]['create_time'] = $user_re['create_time'];
                $data[$k]['voice_url'] = $user_re['voice_url'];
            }
        }*/
        if(empty($page) || $page <1){
            $limit = 0;
        }else{
            $limit = $page*5;
        }
        if($page > 0){
            return [];
        }
        $receivedModel = new Receive();
        $data = $receivedModel->with('userInfo')->where('red_id',$red_id)->
        order('create_time desc')->limit(30)->select();
        foreach ($data as $k =>&$v){
            $v = $v->toArray();
            $v['create_time']= date('m-d H:i',strtotime($v['create_time']));
        }
        return $data;

    }





    public static function AD_QR($red_id=null){

        $ac_token = self::ac_token();
        if(!$red_id){
            $red_id='123';
        }
        $url = "https://api.weixin.qq.com/wxa/getwxacode?access_token=".$ac_token;//接口地址
        $data = [
            'path'=>'pages/recive/recive?red_id='.$red_id,
            //'page' =>'pages/recive/recive',
            'width'=>'50',

        ];

        $data = json_encode($data);
        $result = self::https_request($url,$data);//与接口建立会话
        Log::write('生成的二维码结果：'.json_encode($result));
        if($result){
            $qr=self::add_qrimg($result,$red_id);
            return $qr;
        }else{
            return false;
        }
    }












    //二维码
    public static function QR_code($red_id=null){
        $ac_token = self::ac_token();
        if(!$red_id){
            $red_id='123';
        }
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$ac_token;//接口地址
        $data = [
            'scene'=>$red_id,
            //'page' =>'pages/recive/recive',
            'width'=>'50',

        ];

        $data = json_encode($data);
        $result = self::https_request($url,$data);//与接口建立会话
        Log::write('生成的二维码结果：'.json_encode($result));
        if($result){
            $qr=self::add_qrimg($result,$red_id);
            return $qr;
        }else{
            return false;
        }

    }

    //二进制转换为图片
    public static function add_qrimg($result,$red_id){

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
        $upToken = $qiniu->uploadToken($bucket);
        $upload = new UploadManager();
        $key = time().$red_id;
        $re =  $upload->put($upToken,$key,$result);

        $filePath = config('qiniu_bucket_domain').DS.$re[0]['key'];
        //var_dump($re);die;
        return $filePath;


        /////////////////////////////////////////////////
        /*$imgDir = 'uploads'.DS.'QR'.DS;
        $filename="qr_red_".time().".jpg";//要生成的图片名字
        $jpg = $result;//得到二进制原始数据
        $file = fopen($imgDir.$filename,"w");//打开文件准备写入
        fwrite($file,$jpg);//写入
        fclose($file);//关闭
        $filePath = 'https://'.$_SERVER['HTTP_HOST'].DS.$imgDir.$filename;
        return $filePath;*/
        ////////////////////////////

    }

    //连接微信接口
    public static function https_request($url,$data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }



    //获取AC——token值
    public static function ac_token(){
        /*$ac_token = Cache::get('access_token');
        if(empty($ac_token)){
            $token = self::getAccess_token();
            Cache::set('access_token',$token,7000);
            $ac_token = Cache::get('access_token');
        }*/
        $redis = RedisClient::getHandle(0);
        $ac_token= $redis->getKey('wechat_access_token');
        if(!$ac_token){
            $ac_token = self::getAccess_token();
            $redis->setKey('wechat_access_token',$ac_token,7000);

        }
        return $ac_token;

    }

    public static function getAccess_token()
    {
        $app_id = config('wechatapp_id');
        $secret = config('wechatapp_secret');
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$app_id.'&secret='.$secret;
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $url);//与url建立对话
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //进行配置
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //进行配置
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//进行配置
        $output = curl_exec($ch);//执行对话，获取接口数据Access Token
        curl_close($ch);//关闭会话
        $jsoninfo = json_decode($output, true);//解码接口数据，将json格式字符串转换成php变量或数组。默认是变量，加true后是数组。
        Log::write('获取access_token的结果:'.$output);
        $access_token = $jsoninfo["access_token"];
        if($access_token){
            return $access_token;
        }else{
            self::setError([
                'status_code' => 500,
                'message' =>'服务器忙',
            ]);
            return false;
        }

    }






    /*
     * 获取我的6条声音
     */
    public static function getMyVoice($uid)
    {
        $model = new Receive();
        $where['user_id'] = $uid;
        $where['voice_url']=array('neq','');
        $res   = $model->with('redInfo')->where($where)->field('red_id,voice_url,create_time')->order('id desc')->limit(6)->select();
        //return $res;
        //dump($res);die;
        $data = [];
        if(!empty($res)){
            foreach ($res as $v){
                if($v['red_info']['type']==3){
                    $content = '向你扔来一个问答红包';
                }else{
                    $content =$v['red_info']['content'];
                }
                $item['voice_url']='http://'.$v['voice_url'];
                $item['red_id']   = $v['red_id'];
                $item['create_time']= $v['create_time'];
                $item['content']    = $content;
                array_push($data,$item);
            }
        }

        return $data;
    }



    //举报
    public static function report($content,$phone,$weixin,$red_id,$uid){
        if(empty($content)){
            self::setError([
                'status_code' => 4055,
                'message' =>'请输入举报内容',
            ]);
            return false;
        }
        $data['red_id'] = $red_id;
        $data['content'] = $content;
        $data['create_time'] = time();
        $data['user_id']  =  $uid;
        $data['user_name'] = Db::name('users')->where('user_id',$uid)->value('user_name');
        if(!empty($phone)){$data['phone'] = $phone;}
        if(!empty($weixin)){$data['weixin']= $weixin;}
        //return $data;
        $result = Db::name('report')->insert($data);
        if($result){
            return true;
        }else{
            self::setError([
                'status_code' => 500,
                'message' =>'服务器忙',
            ]);
            return false;
        }
    }




    //举报后台查看
    public static function report_list($page,$search){
        if(empty($page) || $page<1){
            $limit = 0;
        }else{
            $limit = ($page-1)*10;
        }
        $data = Db::name('report')->where('user_name','like','%'.$search['user_name'].'%')
            ->where('red_id','like','%'.$search['red_id'].'%')->order('create_time desc')->limit($limit,10)->select();

        $data['count']['count'] = Db::name('report')->where('user_name','like','%'.$search['user_name'].'%')
            ->where('red_id','like','%'.$search['red_id'].'%')->count();
        foreach ($data as $k){
            $datas[]=$k;
        }
        return $datas;


    }



    //查看被举报的红包详情
    public static function report_detail($red_id){
        $field ='red_id,user_id,se_money,se_number,voice,content,receive,create_time,order_sn,type';
        $red  = Db::name('send')->where('red_id',$red_id)->field($field)->find();
        if(!$red){
            self::setError([
                'status_code' => 4055,
                'message' =>'请输入正确的红包ID',
            ]);
            return false;
        }
        $red['user'] = Db::name('users')->where('user_id',$red['user_id'])->field('user_icon,user_name')->find();
        if($red['type'] ==1){
            $type = '语音红包';
        }elseif($red['type'] == 2){
            $type = '口令红包';
        }else{
            $type = '问答红包';
        }
        $red['type'] = $type;
        return $red;
    }



    //后台查看红包二维码
    public static function adQr_list($page){
        if(empty($page) || $page < 1){
            $limit = 0;
        }else{
            $limit = ($page-1) * 10;
        }
        $data = Db::name('red_qr')->where(['is_del'=>0])->limit($limit,10)->select();
        if($data){
            return $data;
        }else{
            self::setError([
                'status_code' => 500,
                'message' =>'没有数据',
            ]);
            return false;
        }
    }


    //后台添加 红包修改二维码
    public static function adQr_set($data){
        //$data = json_decode($data,true);
        $red = Db::name('send')->where('red_id',$data['red_id'])->find();
        if(empty($red)){$msg = '请输入正确的红包ID';}
        $alone = Db::name('red_qr')->where(['red_id'=>$data['red_id'],'is_del'=>0])->find();
        if(!empty($alone)){$msg = '存在相同红包ID';}
        if(empty($data['qr_address'])){$msg = '请输入二维码地址';}
        $data['create_time'] = time();
        if(isset($msg)){
            self::setError([
                'status_code' => 4055,
                'message' =>$msg,
            ]);
            return false;
        }
        if(empty($data['id'])){
             unset($data['id']);

             $res =  Db::name('red_qr')->insert($data);
        }else{

             $res = Db::name('red_qr')->where(['id'=>$data['id'],'is_del'=>0])->update($data);
        }
        if($res){
            return true;
        }else{
            self::setError([
                'status_code' => 500,
                'message' =>'服务器忙',
            ]);
            return false;
        }
    }



    public static function adQr_del($id){
        $map=[
            'id'=>$id,
            'is_del'=>0
        ];
        $res = Db::name('red_qr')->where($map)->find();
        if(empty($res)){
            self::setError([
                'status_code' => 4055,
                'message' =>'请输入正确的ID',
            ]);
            return false;
        }
        $del = Db::name('red_qr')->where($map)->setInc('is_del');

        if($del){
            return true;
        }else{
            self::setError([
                'status_code' => 500,
                'message' =>'请输入正确的ID',
            ]);
            return false;
        }

    }
}
