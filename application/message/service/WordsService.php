<?php
namespace app\message\service;

use app\common\service\BaseService;
use app\users\service\UserService;
use think\Db;
use think\Log;

class WordsService extends BaseService
{

    //前端查询口令
    public static function word()
    {
        $res = Db::name('words')->where('pid', 0)->select();
        foreach ($res as $k => $v) {
            $res[$k]['content'] = Db::name('words')->where('pid', $v['id'])->select();
        }
        if ($res) {
            return $res;
        } elseif (empty($res)) {
            return ['msg' => '暂无数据'];
        } else {
            self::setError(['status_code' => 500, 'message' => '服务器忙！']);
            return false;
        }
    }

    public static function barrage()
    {
        $res = Db::name('notes')->where('is_play',0)->order('create_time desc')->limit(2)->select();
        $data = [];
        $data['speed'] = 5;
        if(!empty($res)){

            $data['list'] = $res;
            return $data;
        }else{
            self::setError(['status_code' => 500, 'message' => '服务器忙！']);
            return false;
        }
    }
}
