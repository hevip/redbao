<?php 
namespace app\commpont\service;
use think\Request;
use think\Response;
class PicService extends \app\common\service\BaseService 
{	
	public static $uploadPath=ROOT_PATH . 'public' . DS . 'uploads';
	/**
	 *自定义一个图片等比缩放函数
	 *@param string $picname 被缩放图片名
	 *@param string $path 被缩放图片路径
	 *@param int $maxWidth 图片被缩放后的最大宽度
	 *@param int $maxHeight 图片被缩放后的最大高度
	 *@param string $pre 缩放后的图片名前缀，默认为"s_"
	 *@return boolen 返回布尔值表示成功与否。
	 */
	public static function imageResize($picname,$path,$maxWidth,$maxHeight,$pre="s_")
	{
	    $path = rtrim($path,"/")."/";
	    //1获取被缩放的图片信息
	    $info = getimagesize($path.$picname);
	    // $info = getimagesize("D:/phpStudy/WWW/yjlmv1/public/uploads/5383eb4dac14175dbf5b6fe0ad0072aa.jpg");
	    // var_dump($info);die;
	    //获取图片的宽和高
	    $width = $info[0];
	    $height = $info[1];
	    
	    //2根据图片类型，使用对应的函数创建画布源。
	    switch($info[2]){
	        case 1: //gif格式
	            $srcim = imagecreatefromgif($path.$picname);
	            break;
	        case 2: //jpeg格式
	            $srcim = imagecreatefromjpeg($path.$picname);
	            break;
	        case 3: //png格式
	            $srcim = imagecreatefrompng($path.$picname);
	            break;
	       default:
	            return false;
	            //die("无效的图片格式");
	            break;
	    }
	    //3. 计算缩放后的图片尺寸
	    if($maxWidth/$width<$maxHeight/$height){
	        $w = $maxWidth;
	        $h = ($maxWidth/$width)*$height;
	    }else{
	        $w = ($maxHeight/$height)*$width;
	        $h = $maxHeight;
	    }
	    //4. 创建目标画布
	    $dstim = imagecreatetruecolor($w,$h); 

	    //5. 开始绘画(进行图片缩放)
	    imagecopyresampled($dstim,$srcim,0,0,0,0,$w,$h,$width,$height);

	    //6. 输出图像另存为
	    switch($info[2]){
	        case 1: //gif格式
	            imagegif($dstim,$path.$pre.$picname);
	            break;
	        case 2: //jpeg格式
	            imagejpeg($dstim,$path.$pre.$picname);
	            break;
	        case 3: //png格式
	            imagepng($dstim,$path.$pre.$picname);
	            break;
	    }
	    

	    //7. 释放资源
	    imagedestroy($dstim);
	    imagedestroy($srcim);
	    return true;
	}

	public function uploadPic()
	{
		// 获取表单上传文件 例如上传了001.jpg
        $file = Request::instance()->file('upload');//name="image"
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>1000000,'ext'=>'jpg,jpeg,png,gif'])->move(self::$uploadPath);
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            // echo $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            // echo '/uploads/'.$info->getSaveName();die;
            // echo PicService::$uploadPath.'\\'.$info->getFilename();die;
            self::imageResize($info->getFilename(),self::$uploadPath . DS . date('Ymd'),'400','400','s_');
            self::imageResize($info->getFilename(),self::$uploadPath . DS . date('Ymd'),'800','800','m_');
            self::imageResize($info->getFilename(),self::$uploadPath . DS . date('Ymd'),'1000','1000','b_');
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            return $info->getSaveName();
            /*return $this->responceData(true,[
                		'code' => 200,
                		'message' => $info->getFilename(),
                	]);*/
        }else{
        	// var_dump($file->getError());die;
            self::setError([$file->getError()]);
            return false;
            // 上传失败获取错误信息
            /*return $this->responceData(false,[
            		'code' => 4008,
            		'message' => $file->getError(),
            	]);*/
        }
	}

	public function uploadPics()
	{
		// 获取表单上传文件
        $files = Request::instance()->file('image'); //name="image[]"
        foreach($files as $file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->validate(['size'=>1000000,'ext'=>'jpg,png,gif'])->move(self::$uploadPath);
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                // echo $info->getExtension(); 
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                $fileName .= $info->getFilename().'_'; 
            }else{
                // 上传失败获取错误信息
                return $this->responceData(false,[
                		'code' => 4008,
                		'message' => $file->getError(),
                	]);
            }    
        }
        return $this->responceData(true,[
                		'code' => 200,
                		'message' => $fileName,
                	]);
	}

	/**
	 *	返回返回对应的状态码 
	 */
	public function responceData($result,$message=[])
	{	
		if(empty($result)){
			return Response::create([
	           'status'=> 'error',
	           'error' =>[
	               'status_code'=> $message['code'],
	               'message'    => $message['message'],
	           ]
	       ],'json');
		}else{
			return Response::create([
	           'status'=> 'success',
	           'data' => [
	           	   'status_code'=> $message['code'],
	               'message'    => $message['message'],
	           ]
	       	],'json');
		}
	}
}
