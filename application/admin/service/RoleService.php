<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-11
 * Time: 上午10:05
 */

namespace app\admin\service;

use app\admin\model\Role;
use app\common\service\BaseService;
use app\common\model\RoleModule;
use think\Db;

class RoleService extends BaseService
{
    /**
     * 获取角色列表
     * @param $page
     * @return bool
     */
    public static function _list($page)
    {
        $role = new Role();
        $rolelist = $role->page($page,50)->where('is_del = 0 ')->select()->toArray();
        if($rolelist){
            $data['total'] = $role->where('is_del = 0 ')->count();
            $data['list']  = $rolelist;
            return $data;
        }else{
            return false;
        }

    }
    /**
     * 创建角色
     * @param $data
     * @return array|bool
     */
    public static function create($data,$auth)
    {
        $validate = validate('Role');
        if(!$validate->check($data)){
            self::setError([
                'status_code'=>4015,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        //保存数据
        $role = new Role();
        $r_data['role_name'] = $data['role_name'];
        $r_data['role_desc'] = $data['role_desc'];
        $r_data['createrId'] = $auth['admin_id'];
        if($role->save($r_data)){
            $role_id = $role->role_id;
            //向角色－权限关联表插入数据
            $rm_data = [];
            foreach ($data['mod_ids'] as $v) {
                $item = ['role_id' => $role_id, 'mod_id' => $v['mod_id']];
                array_push($rm_data, $item);
            }
            if(Db::name('role_module')->insertAll($rm_data)){
                return array(
                    'role_id'=>$role_id
                );
            }
        }else{
            self::setError([
                'status_code'=>500,
                'message'    =>$role->getError()
            ]);
            return false;
        }

    }

    /**
     * 删除角色
     * @param $ids
     * @return bool
     */
    public static function delete($ids)
    {
        if(isset($data['ids'])){
            $roleInfo = self::read($data['ids']);
            if(!$roleInfo){
                self::setError([
                    'status_code'=> 4013,
                    'message'    => 'id not find in roles'
                ]);
                return false;
            }else{
                $role = new Role();
                if($res = $role::destroy($data['ids'])){
                    //更新缓存,异步处理
                    return $res;
                }
            }
        }else{
            self::setError([
                'status_code'=> 4012,
                'message'    => 'missing parmas ids'
            ]);
            return false;
        }
    }
    public static function update($id,$data)
    {
        $roleInfo = self::read($id);
        if($roleInfo){
            if(isset($data['mod_ids'])){
                $rm_data = [];
                foreach ($data['mod_ids'] as $v){
                    $item = [];
                    $item['role_id'] = $id;
                    $item['mod_id']  = $v['mod_id'];
                    array_push($rm_data,$item);
                }
            }
            Db::startTrans();
            try{
                Db::name('role')->where('role_id',$id)->strict(false)->field('role_name,role_desc')->update($data);
                if(!empty($rm_data)){
                    Db::name('role_module')->insertAll($rm_data,true);
                }

                // 提交事务
                Db::commit();
                return $id;
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                self::setError([
                   'status_code'=>500,
                    'message'   =>$e->getMessage()
                ]);
                return false;
            }
        }else{
            self::setError([
                'status_code'=>4013,
                'message'    => 'id not find in roles'
            ]);
            return false;
        }

    }

    /**
     * 获取某角色信息
     * @param $id
     * @return array|bool
     */
    public static function read($id)
    {
        $module = new Role();
        if(is_array($id))
        {
            $ids = '';
            foreach ($id as $v){
                $ids.= (int)$v.',';
            }
            $ids = substr($ids, 0, -1);
            $count = $module->where('is_del','=','0')->where('role_id','in',"$ids")->count();
            if($count==count($id)){
                $items = $module->with('modules')->where("is_del",'=','0')->where('role_id','in',"$ids")->select()->toArray();
            }else {
                self::setError([
                    'status_code'=>4018,
                    'message'    => 'role_id不存在'
                ]);
                return false;
            }
        }else{
            $count = $module->where('is_del','=','0')->where("role_id = {$id}")->count();
            if($count>0){
                $items  = $module->with('modules')->where('is_del','=','0')->where("role_id = {$id}")->find()->toArray();
            }else{
                self::setError([
                    'status_code'=>4018,
                    'message'    =>'role_id不存在'
                ]);
                return false;
            }
        }
        return $items;
    }

    /**
     * 查询角色
     * @param $condition
     * @return array|bool
     */
    public static function search($condition)
    {
        if(is_array($condition)){
            $role = new Role();
            $list = $role->where($condition)->select()->toArray();
            return $list;
        }else{
            self::setError([
                'status_code'=>4019,
                'message'    => '查询角色参数错误'
            ]);
            return false;
        }

    }
}