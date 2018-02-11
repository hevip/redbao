<?php
/**
 * 功能模块控制器
 */
namespace app\admin\controller;

use PhpParser\Node\Expr\AssignOp\Mod;
use think\Request;
use app\common\controller\Api;
use app\admin\service\ModuleService;
class Module extends Api
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $tree = ModuleService::tree($this->auth);
        if(!$tree){
            return $this->responseNotFound();
        }else{
            return $this->responseSuccess($tree);
        }
    }

    /**
     * 创建资源表
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        $data = Request::instance()->post();
        $id = ModuleService::createModule($data);
        if($id){
            return $this->responseSuccess($id);
        }else{
            return $this->responseError(4007,ModuleService::getError());
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
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
        $result = ModuleService::read($id);
        if($result){
            return $this->responseSuccess($result);
        }else{
            return $this->responseError(ModuleService::getError());
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
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
        $data = $request->put();
        $result = ModuleService::update($id,$data);
        if($result){
            return $this->responseSuccess([
                'mod_id'=>$id
            ]);
        }else{
            return $this->responseError(ModuleService::getError());
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete()
    {
        if(isset(Request::instance()->delete()['ids'])){
            $ids = Request::instance()->delete()['ids'];
            return ModuleService::delete($ids,$this->auth);
        }else{
            return $this->responseError(4008,'missing param ids');
        }
    }
    /**
     * 测试数据
     */
    public function testJson()
    {
        $tree = ModuleService::tree($this->auth);
        dump($tree);
        //ModuleService::getChildren();
    }
}
