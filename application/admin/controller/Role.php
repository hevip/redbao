<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-11
 * Time: 上午9:59
 */

namespace app\admin\controller;

use app\common\controller\Api;
use app\admin\service\RoleService;
use think\Request;

class Role extends Api
{
    /**
     * 角色列表
     * @param int $page
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function index($page=1)
    {
        $list = RoleService::_list($page);
        if(!$list){
            return $this->responseNotFound();
        }else{
            return $this->responseSuccess($list);
        }
    }

    /**
     * 创建角色
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function create()
    {
        $data = Request::instance()->post();
        $result = RoleService::create($data,$this->auth);
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(RoleService::getError());
        }
    }

    /**
     * 读取某角色信息
     * @param $id
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function read($id)
    {
        $result = RoleService::read($id);
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(RoleService::getError());
        }
    }

    /**
     * 更新角色
     * @param $id
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function update($id)
    {
        $data = Request::instance()->put();
        $result = RoleService::update($id,$data);
        if($result)
        {
            return $this->responseSuccess([
                'role_id'=>$id
            ]);
        }else{
            return $this->responseError(RoleService::getError());
        }
    }

    /**
     * 删除角色
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function delete()
    {
        $data   = Request::instance()->delete();
        $result = RoleService::delete($data);
        if($result){
            return $this->responseSuccess([
                'delete_number'=>$result
            ]);
        }else{
            return $this->responseError(RoleService::getError());
        }
    }


    public function search()
    {
        $data  = Request::instance()->post();
        $result = RoleService::search($data);
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(RoleService::getError());
        }
    }

}