<?php
namespace app\commpont\controller;

use app\common\controller\Api;
use anu\SingleFactory;

use think\File;
use app\commpont\service\PicService;
use think\Response;
use think\Request;

class Pic extends \think\Controller
{
    /**
     * 图片上传接口
     */
    public function uploadPic()
    {   
        $PicService = new PicService();
        $result = $PicService->uploadPic();
        if($result){
            return Response::create([
                'status'=>'success',
                'data'  =>[
                    'img_url' =>Request::instance()->domain().'/uploads/'.$result
                ],
            ],'json');

        }else{
            return Response::create([
                'status'=>'failed',
                'error' =>[
                    'status_code' =>500,
                    'message'     =>$PicService::getError()
                ]
            ],'json');
        }
        //return $result;
        
    }

    public function uploadPics()
    {   
        $PicService = new PicService();
        $result = $PicService->uploadPics();
        return $result;
    }

    public function uploadForarticle()
    {
        $PicService = new PicService();
        $result     = $PicService->uploadPic();
        if($result){
            $callback = $_REQUEST["CKEditorFuncNum"];
            $request = Request::instance();
            $imageurl = $request->domain().'/uploads/'.$result;
            echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback,'".$imageurl."','');</script>";
        }
    }
    /**
     * 获得文章服务对象!
     */
    private function getPicService()
    {   
        return SingleFactory::getObject('app\commpont\service\PicService');
    }
}
