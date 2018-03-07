<?php
namespace app\message\service;

use app\common\service\BaseService;
use app\users\service\UserService;
use think\Db;
use think\Log;

class MessageService extends BaseService
{
    //是否推送
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
        $ACCESS_TOKEN = UserService::ac_token();
        $upload = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$ACCESS_TOKEN.'&type=image';
//        $image = array('media'=>'@'.$img);
        $image = array('media'=>new \CURLFile($img,'image/jpg'));
        $res = UserService::https_request($upload,$image);
        $res = json_decode($res, true);
//        dump($res);exit;0
        return $res;
    }

    //小程序卡片
    public static function serviceMessage($uid,$info)
    {
       if(!is_numeric($info['red_id'])){
            self::setError(['status_code' => 500, 'message' => 'red_id参数有误']);
            return false;
        }
        $openid = Db::name('users')->where('user_id',$uid)->value('user_openid');
        $ACCESS_TOKEN = UserService::ac_token();
        //发送小程序卡片
        $data = array(
            'touser' => $openid,
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

    //模板消息
    public static function template($info,$uid)
    {
        $ACCESS_TOKEN =UserService::ac_token();
        $res = Db::name('send')->where('red_id',$info['red_id'])->find();
        Db::name('send')->where('red_id',$info['red_id'])->setField('is_pay',1);
        $openid = Db::name('users')->where('user_id',$uid)->value('user_openid');
        //后续逻辑发布
        $template_id = Db::name('template')->where(['red_type'=>$res['type']])->value('template_id');
        if($res['type'] == 2){

            $data = array(
                'touser' => $openid,
                'template_id' => $template_id,
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
                'template_id' => $template_id,
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

    //CURL请求
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

    public static function words($info)
    {
        if(!is_numeric($info['page']))
        {
            self::setError(['status_code' => 500, 'message' => 'page参数错误！']);
            return false;
        }
        //页数
        $page = $info['page'];
        if(empty($page)|| $page <= 1){
            $start_page = 0;
        }else{
            $start_page = ($page-1)*10;
        }
        //搜索名称
        if(!empty($info['title'])){
            $validate = validate('app\message\validate\Word');
            if(!$validate->check($info)){
                self::setError([
                    'status_code'=>4105,
                    'message'    =>$validate->getError()
                ]);
                return false;
            }
            $res = Db::name('words')->where('pid',0)->where('title','like',$info['title'])->select();
            foreach($res as $k=>$v){
                $res[$k]['content'] = Db::name('words')->where('pid',$v['id'])->limit($start_page,10)->select();
            }
            if($res){
                $result['total'] = Db::name('words')->where('title','like',$info['title'])->count();
                $result['res'] = $res;
                return $result;
            }elseif(empty($res))
            {
                return ['msg'=>'暂无数据'];
            }else{
                self::setError(['status_code' => 500, 'message' => '服务器忙！']);
                return false;
            }
        }

            $res = Db::name('words')->where('pid',0)->select();
            foreach($res as $k=>$v){
                $res[$k]['content'] = Db::name('words')->where('pid',$v['id'])->limit($start_page,10)->select();
            }
            if($res){
                $result['total'] = Db::name('words')->count();
                $result['res'] = $res;
                return $result;
            }elseif(empty($res))
            {
                return ['msg'=>'没有找到相关数据'];
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

    //删除或修改口令
    public static function del_word($info)
    {
        if(!is_numeric($info['id'])){
            self::setError(['status_code' => 500, 'message' => 'id参数非法']);
            return false;
        }
        if($info['type'] != 0 && $info['type'] != 1){
            self::setError(['status_code' => 500, 'message' => 'type参数非法']);
            return false;
        }
        if($info['type'] == 0){
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
        }else{
            $validate = validate('app\message\validate\Word');
            if(!$validate->check($info)){
                self::setError([
                    'status_code'=>4105,
                    'message'    =>$validate->getError()
                ]);
                return false;
            }
            $res = Db::name('words')->where('id',$info['id'])->setField('title',$info['content']);
            if($res){
                return ['msg'=>'修改成功'];
            }else{
                self::setError(['status_code' => 500, 'message' => '服务器忙！']);
                return false;
            }
        }
    }

    //修改弹幕
    public static function up_barrage($info)
    {
        $data['content'] = $info['content'];
        $data['color'] = $info['color'];
        $data['speed'] = $info['speed'];
        $data['create_time'] = time();
        $res = Db::name('notes')->where('id',$info['id'])->update($data);
        if($res){
            return ['msg'=>' 修改成功！'];
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙！']);
            return false;
        }

    }

    //弹幕开关
    public static function is_play($info)
    {
        if (!is_numeric($info['id'])) {
            self::setError(['status_code' => 500, 'message' => '参数有误！']);
            return false;
        }
        if($info['is_play'] != 0  &&  $info['is_play'] != 1){
            self::setError(['status_code' => 500, 'message' => '参数有误！']);
            return false;
        }
        $res = Db::name('notes')->where('id', $info['id'])->setField('is_play', $info['is_play']);
        if ($res) {
            if($info['is_play'] == 0){
                return ['msg'=>'弹幕已开启'];
            }else{
                return ['msg'=>'弹幕已关闭'];
                }
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙！']);
            return false;
        }
    }
}
