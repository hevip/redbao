<?php
namespace app\message\service;

use app\common\service\BaseService;
use app\users\service\UserService;
use think\Db;
use think\Log;

class MessageService extends BaseService
{
    public static function message($uid,$status)
    {
        $validate = validate('app\message\validate\Message');
        if(!$validate->check($status)){
            self::setError([
                'status_code'=>4105,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        if($status['status'] == 1){
            $res = Db::name('users')->where('user_id',$uid)->update(['is_push'=>1]);
            if($res){
                $info['message'] = '设置成功1';
                return $info;
            }else{
                self::setError(['status_code' => 500, 'message' => '设置失败1']);
                return false;
            }
        }elseif($status['status'] == 0)
        {
            $res = Db::name('users')->where('user_id',$uid)->setField('is_push',0);
            if($res){
                $info['message'] = '设置成功0';
                return $info;
            }else{
                self::setError(['status_code' => 500, 'message' => '设置失败0']);
                return false;
            }
        }else{
            self::setError(['status_code' => 500, 'message' => '参数有误']);
            return false;
        }
    }

    //微信上传临时素材
    public static function Img()
    {
        $img = "C:\\Users\\Administrator\\Desktop\\2.jpg";
//        var_dump($img['tmp_name']);exit;
        $ACCESS_TOKEN = UserService::ac_token();
        $upload = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$ACCESS_TOKEN.'&type=image';
//        $image = array('media'=>'@'.$img);
        $image = array('media'=>new \CURLFile($img,'image/jpg'));
        $res = UserService::https_request($upload,$image);
        $res = json_decode($res, true);
//        dump($res);exit;
        return $res;
    }

    public static function serviceMessage($uid,$info)
    {
       if(!is_numeric($info['red_id'])){
            self::setError(['status_code' => 500, 'message' => 'red_id参数有误']);
            return false;
        }
        $openid = Db::name('users')->where('user_id',$uid)->value('user_openid');
//        if($user['is_push'] == 0) {
//            self::setError(['status_code' => 500, 'message' => '未开启消息推送']);
//            return false;
//        }
        $ACCESS_TOKEN = UserService::ac_token();
        //发送小程序卡片
        $data = array(
            'touser' => $openid,
//            'touser' => 'o9ZC35bbKoGTWZLt2xGzm5NPbp6E',
            'msgtype' => 'miniprogrampage',
            'miniprogrampage' => [
                'title' => $info['title'],
                'pagepath'=>'/pages/recive/recive?red_id='.$info['red_id'],
                'thumb_media_id'=>'KE1pMOikROGzYYcj9pKYXYLA7mg2Taw96L06j-YTLTymKB_7ECO_0-N3c6TTyaYt'
            ]
            );
//发送文本
//        $appid = config('wechatapp_id');
//        $data = array(
//            'touser' => $openid,
//            'msgtype' => 'text',
//            'text' => ['content' => "<a href='http://www.qq.com' data-miniprogram-appid='".$appid."'data-miniprogram-path='pages/recive/recive?red_id=".$info['red_id'].">点击抢红包</a>"]
//        );
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$ACCESS_TOKEN;
        $res = UserService::https_request($url,$json_data);
//        $res = self::Http($url,$data,'json');
        Log::write('客服消息内容：'.json_encode($data));
        $res = json_decode($res, true);
        if ($res['errcode'] == 0 && $res['errmsg'] == "ok") {
            return ['message' => '发送成功'];
        } else {
            self::setError([
                'status_code' => $res['errcode'],
                'message' => $res['errmsg']
            ]);
            return false;
        }
    }
    public static function template($info,$uid)
    {
        $ACCESS_TOKEN =UserService::ac_token();
        $res = Db::name('send')->where('red_id',$info['red_id'])->find();
        Db::name('send')->where('red_id',$info['red_id'])->setField('is_pay',1);
        $openid = Db::name('users')->where('user_id',$uid)->value('user_openid');
        if($res['type'] == 2){
            $data = array(
                'touser' => $openid,
                'template_id' => "SSxUVgOKZW85YydzlwTT4h7wsS_Lwydgv-u5ESMS_Ng",
                'form_id'=>$info['form_id'],
                'page'=>'pages/recive/recive?red_id='.$info['red_id'],
                'data'=>[
                    'keyword1'=>['value'=>$res['content'],'color'=>'#5C81FF'],
                    'keyword2'=>['value'=>date('m-d H:i'),'color'=>'#5C81FF'],
                    'keyword3'=>['value'=>'您的口令红包已经创建成功，赶快点击分享给小伙伴','color'=>'#5C81FF']
                ]
               );
        }else{
            $data = array(
                'touser' =>$openid,
                'template_id' => "YHa9WVQfj_0TkNCVDCartMQ50lGA0IjT4K-GJ56IAwE",
                'form_id'=>$info['form_id'],
                'page'=>'pages/recive/recive?red_id='.$info['red_id'],
                'data'=>[
                    'keyword1'=>['value'=>'真心寄语','color'=>'#5C81FF'],
                    'keyword2'=>['value'=>date('m-d H:i'),'color'=>'#5C81FF'],
                ]
            );
        }
//        $json_data = json_encode($data);
                //https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=ACCESS_TOKEN
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=" . $ACCESS_TOKEN;
        $res = self::Http($url,$data,'json');
        Log::write('模板推送内容：'.json_encode($data));
        Log::write('模板消息推送结果：'.$res);
        $res = json_decode($res, true);

        if ($res['errcode'] == 0 && $res['errmsg'] == "ok"){
            return ['message'=>'发送成功'];
        }else{
            self::setError([
                'status_code'=>$res['errcode'],
                'message'    =>$res['errmsg']
            ]);
            return false;
        }
    }

    public  static function Http($url,$data,$type="http"){
        $curl = curl_init();
        if ($type == "json"){
            $headers = array("Content-type: application/json;charset=UTF-8");
            $data=json_encode($data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


    public static function word()
    {
        $res = Db::name('words')->where('pid',0)->select();
//        var_dump($res);exit;
        foreach($res as $k=>$v){
            $res[$k]['content'] = Db::name('words')->where('pid',$v['id'])->select();
        }
//        var_dump($res);exit;
        if($res){
            return $res;
        }elseif(empty($res))
        {
            return ['msg'=>'暂无数据'];
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙！']);
            return false;
        }
    }

    //添加口令
    public static function postWord($info)
    {
        $validate = validate('app\message\validate\Word');
        if(!$validate->check($info)){
            self::setError([
                'status_code'=>4105,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        $res = Db::name('words')->where('title',$info['title'])->value('id');
        if($res){
            $content = ['title'=>$info['content'],'pid'=>$res,'create_time'=>time()];
            $res = Db::name('words')->insert($content);
            if($res){
                return ['msg'=>'添加成功'];
            }else{
                self::setError(['status_code' => 500, 'message' => '添加失败']);
                return false;
            }
        }else{
            $title = ['title'=>$info['title'],'pid'=>0,'create_time'=>time()];
            $id = Db::name('words')->insertGetId($title);
            $content = ['title'=>$info['content'],'pid'=>$id,'create_time'=>time()];
            $res = Db::name('words')->insert($content);
            if($res){
                return ['msg'=>'添加成功'];
            }else{
                self::setError(['status_code' => 500, 'message' => '添加失败']);
                return false;
            }
        }

    }

    //删除口令
    public static function del_word($info)
    {
        if(!is_numeric($info['id'])){
            self::setError(['status_code' => 500, 'message' => 'id参数非法']);
            return false;
        }
        $id = Db::name('words')->where('pid',$info['id'])->find();
        if($id){
            self::setError(['status_code' => 500, 'message' => '请先删除下面的子类！']);
            return false;
        }else{
            $res = Db::name('words')->where('id',$info['id'])->delete();
            if($res){
                return ['msg'=>'删除成功'];
            }else{
                self::setError(['status_code' => 500, 'message' => '服务器忙！']);
                return false;
            }
        }

    }
}
