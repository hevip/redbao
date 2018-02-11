<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-6-28
 * Time: 下午3:19
 * 异常处理类
 */
namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\Response;
class Json extends Handle
{

    public function render(Exception $e)
    {

        return Response::create([
            'status'=>'failed',
            'error'=>[
                'status_code'=>'500',
                'message'=>$e->getMessage()
            ]
        ],'json');
        //TODO::开发者对异常的操作
        //可以在此交由系统处理
        //return parent::render($e);
    }

}