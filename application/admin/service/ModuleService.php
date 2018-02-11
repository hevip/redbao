<?php
/**
 * 功能模块－service层
 * User: greatsir
 * Date: 17-6-30
 * Time: 上午9:05
 */
namespace app\admin\service;

use app\admin\model\Module;
use think\Response;
use greatsir\RedisClient;
use greatsir\Tree;
use app\common\service\BaseService;
use think\Db;
class ModuleService extends BaseService
{
    /**
     * 获取用户权限树
     */
    public static function tree($auth)
    {
        if($auth['account']=='admin'){
            //如果是超级管理员,获取所有的权限
            $redisClient = RedisClient::getHandle();
            if($treeStr = $redisClient->getKey('admin_module')){
                $res = unserialize($treeStr);
            }else{
                $module = new Module();
                $modules = $module->field('mod_id,module,level,ctl,act,title,visible,parent_id,orderby')->order('orderby asc')->select()->toArray();
                $tree = new Tree();
                $res  = $tree->make_tree($modules,'mod_id','parent_id','menu');
                $treeStr = serialize($res);
                $redisClient->setKey('admin_module',$treeStr);
            }
            return $res;

        }else{
            //获取非超级管理员的权限

            $modules = self::getModuleByAdmin($auth['admin_id']);
            $tree    = new Tree();
            $res     = $tree->make_tree($modules,'mod_id','parent_id','menu');

            return $res;

        }
    }

    /**
     * 删除功能模块
     * @param $id
     * @return Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public static function delete($id,$auth)
    {
        /*$module = new Module();
        if(is_array($id))
        {
            $ids = '';
            foreach ($id as $v){
                $ids.= (int)$v.',';
            }
            $ids = substr($ids, 0, -1);
            $items = $module->where("is_del",'0')->where('mod_id','in',"$ids")->select();

        }else{

            $items  = $module->where("mod_id = {$id}")->find();
        }*/
        $moduleInfo = self::read($id);
        if(!$moduleInfo){
            return Response::create([
                'status' => 'failed',
                'error'  => [
                    'status_code'=> 404,
                    'message'    => 'not defind'
                ]
            ],'json');
        }else{
            $module = new Module();
            if($res = $module::destroy($id)){
                //更新缓存,异步处理
                $redis = RedisClient::getHandle();
                $redis->clearKey('admin_module');
                return Response::create([
                    'status' => 'success',
                    'data'   => self::tree($auth)
                ],'json');
            }

        }

    }

    /**
     * 生成树型结构
     * @return array|bool
     */
    private static function createTree($items)
    {
        $tree = array();
        $modules = array();
        foreach ($items as $v)
        {
            $modules[$v['mod_id']] = $v;
        }

        foreach($modules as $k=>$v){
            if($v['module'] == 'top'){
                $tree[$k] = $v;
            }
        }
        foreach($modules as $k=>$v){
            if($v['module'] == 'menu'){
                $tree[$v['parent_id']]['menu'][$k] = $v;

            }
        }
        foreach($modules as $k=>$v){
            if($v['module'] == 'module'){
                $ppk = $modules[$v['parent_id']]['parent_id'];
                $tree[$ppk]['menu'][$v['parent_id']]['menu'][$k] = $v;
            }
        }
        return $tree;
        //return false;
    }

    /**
     * 创建功能模块
     * @param $data
     * @return array|bool
     */
    public static function createModule($data)
    {
        if(!isset($data['parent_id'])){
            self::setError('parent_id不能为空');
            return false;
        }
        if($data['parent_id']==0){
            $data['module'] = 'top';
            $data['level']  = 1;
        }else{

            $moduleInfo = self::read($data['parent_id']);
            if($moduleInfo)
            {
                $data['level'] = $moduleInfo['level']+1;
                if($data['level']==2){
                    $data['module'] = 'menu';
                }
                if($data['level']==3){
                    $data['module'] = 'module';
                    $validate = validate('Module');
                    if(!$validate->check($data)){
                        self::setError($validate->getError());
                        return false;
                    }
                }
            }else{
                self::setError([
                    'status_code'=>4014,
                    'message'    => 'parent_id　not find'
                ]);
                return false;
            }
        }
        $module = new Module();
        $result = $module->save($data);
        if($result){
            $redis = RedisClient::getHandle();
            $redis->clearKey('admin_module');
            return array(
                'mod_id'=>$module->mod_id
            );
        }else{
            throw new \think\exception\HttpException(500, '创建失败');
        }

    }

    /**
     * 获取模块详情
     * @param $id
     * @return mixed
     */
    public static function read($id)
    {
        $module = new Module();
        if(is_array($id))
        {
            $ids = '';
            foreach ($id as $v){
                $ids.= (int)$v.',';
            }
            $ids = substr($ids, 0, -1);
            $count = $module->where('is_del','=','0')->where('mod_id','in',"$ids")->count();
            if($count==count($id)){
                $items = $module->where("is_del",'=','0')->where('mod_id','in',"$ids")->select()->toArray();
            }else {
                self::setError([
                    'status_code'=>4020,
                    'message'    => 'mod_id不存在'
                ]);
                return false;
            }

            //$items = $module->where("is_del",'=','0')->where('mod_id','in',"$ids")->select()->toArray();

        }else{
            $count = $module->where('is_del','=','0')->where("mod_id = {$id}")->count();
            if($count>0){
                $items  = $module->where('is_del','=','0')->where("mod_id = {$id}")->find()->toArray();
            }else{
                self::setError([
                    'status_code'=>4020,
                    'message'    =>'mod_id不存在'
                ]);
                return false;
            }
            //$items  = $module->where('is_del','=','0')->where("mod_id = {$id}")->find()->toArray();
        }
        return $items;
    }

    /**
     * 更新模块
     * @param $id
     * @param $data
     * @return bool|false|int
     */
    public static function update($id,$data){
        $moduleInfo = self::read($id);
        if($moduleInfo){
            $module = new Module();
            $res    = $module->allowField(true)->save($data,['mod_id'=>$id]);
            if($res){
                //更新缓存
                $redis = RedisClient::getHandle();
                $redis->clearKey('admin_module');
                return $res;
            }else{
                self::setError([
                    'status_code'=>500,
                    'message'    => $module->getError()
                ]);
                return false;
            }
        }else{
            self::setError([
                'status_code'=>4020,
                'message'    => 'mod_id not find'
            ]);
            return false;
        }
    }
    private static function getModuleByAdmin($admin_id)
    {
        $sql = "select * from `yj_system_module` where mod_id in (select rm.mod_id from `yj_role_module` as rm left join yj_admin_role as ar on rm.role_id = ar.role_id where ar.admin_id={$admin_id})";
        $modules = Db::query($sql);
        return $modules;
    }
}