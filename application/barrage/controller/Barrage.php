<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/3/5
 * Time: 下午3:05
 */
namespace app\barrage\controller;

use GuzzleHttp\Client;
use think\Controller;
use think\Request;

class Barrage extends Controller
{
    public function test()
    {
        $data = Request::instance()->post();
        $id = $data['id'];
        $url = 'http://localhost:8081/AppController/notice?uid='.$id;
        $client = new Client();
        $response = $client->get($url);
        $res =$response->getBody()->getContents();
        echo '发送成功';
    }
}