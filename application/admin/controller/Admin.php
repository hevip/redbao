<?php
/**
 * 管理员模块－管理员控制器
 * User: greatsir
 * Date: 17-6-29
 * Time: 下午1:35
 */
namespace app\admin\controller;

use app\common\controller\Api;
use anu\SingleFactory;
use think\Request;
use app\admin\service\AdminService;
/**
 * 管理员restful API管理类
 * 1：新增管理员之所以将管理员增加功能添加到api控制器下面
 *    是为了限制了管理员只能由admin超级管理员添加
 *    static::save() | POST
 * 2：删除管理员：业务删除
 *    static::delete() | DELETE
 * 3: 修改管理员信息
 *    static::update() | PUT 
 * 5: 修改管理员密码
 *    static::resetPassword
 * 4: 查询根据account,admin_id,nick_name查询
 *    static::querys()
 *   
 */
class Admin extends Api
{   
    public function resetPassword(Request $request)
    {
        return '未开放';
    }

    public function querys(Request $request)
    {
        return '未开放';
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($page=1)
    {
        $list = AdminService::_list($page);
        if($list){
            return $this->responseSuccess($list);
        }else{
            return $this->responseError(AdminService::getError());
        }
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $data = Request::instance()->post();
        $res = AdminService::create($data);
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError(AdminService::getError());
        }

    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // return '未开放';
        $adminService = $this->getAdminService();
        $res = $adminService->save($request->only(['account','password','nickname','roles','orgs'],'post'));
        if($res){
            return $this->responseSuccess($res);
        }else{
            return $this->responseError($adminService::getError());
        }
    }
    public function update(Request $request,$id)
    {
        $data = $request->put();
        $result = AdminService::update($id,$data);
        if($result){
            return $this->responseSuccess([
                'admin_id'=>$id
            ]);
        }else{
            return $this->responseError(AdminService::getError());
        }
    }
    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $result = AdminService::read($id);
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(AdminService::getError());
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {   
        return '未开放';
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    /*public function update(Request $request, $id)
    {   
        $adminService = $this->getAdminService();
        return $adminService->save($request->only(['nickname','admin_id'],'put'),$id);
    }*/

    /**
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function delete()
    {
        $data = Request::instance()->delete();
        $res  = AdminService::delete($data);
        if($res){
            return $this->responseSuccess([
                'delete_number'=>$res
            ]);
        }else {
            return $this->responseError(AdminService::getError());
        }
    }

    private function getAdminService()
    {
        return SingleFactory::getObject("app\\admin\\service\\AdminService");
    }
}