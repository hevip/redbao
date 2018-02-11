<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-6-30
 * Time: 上午9:26
 */
namespace app\common\controller;

use think\Response;
class Api extends Middle
{
    protected $statusCode = 200;
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * 错误响应
    */
    public function responseError(Array $error)
    {
        return Response::create([
            'status'=>'failed',
            'error'=>[
            'status_code'=>$error['status_code'],
            'message'=>$error['message'],
                ]
        ],'json');
    }
    /**
     * 资源找不到
     */
    public function responseNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(404)->responseError([
            'status_code'=>404,
            'message'=>$message
        ]);
    }
    /**
     * 正确响应
     */
    public function responseSuccess($data)
    {
        return Response::create([
            'status'=>'success',
            'data'  => $data
        ],'json');
    }



}