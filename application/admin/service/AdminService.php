<?php
/**
 * 管理员service
 * User: greatsir
 * Date: 17-6-30
 * Time: 上午11:47
 */
namespace app\admin\service;

use app\common\service\BaseService;
use app\admin\model\Admin;
use Firebase\JWT\JWT;
use think\Validate;
use think\Db;
use think\Debug;
class AdminService extends BaseService
{
    /**
     * 添加管理员
     * @param $data
     * @return array|bool
     */
    public static function create($data)
    {
        $validate = validate('Admin');
        if(!$validate->check($data)){
            self::setError([
                'status_code'=>4011,
                'message'    =>$validate->getError()
            ]);
            return false;
        }
        Db::startTrans();
        try{
            $data['password'] = md5($data['password']);
            $admin_id = Db::name('admin')->strict(false)->field('account,password,nickname')->insert($data,false,true);
            if(isset($data['roles'])){
                $ar_data = [];
                foreach ($data['roles'] as $key=>$v){
                    $item = [];
                    $item['admin_id'] = $admin_id;
                    $item['role_id']  = $v;
                    array_push($ar_data,$item);
                }
            }
            if(isset($data['orgs'])){
                $ao_data = [];
                foreach ($data['orgs'] as $key=>$v){
                    $item = [];
                    $item['admin_id'] = $admin_id;
                    $item['org_id']   = $v;
                    array_push($ao_data,$item);
                }

            }
            if(!empty($ar_data)){

                Db::name('admin_role')->insertAll($ar_data);
            }
            if(!empty($ao_data)){
                Db::name('admin_org')->insertAll($ao_data);
            }

            // 提交事务
            Db::commit();
            return $admin_id;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::setError([
                'status_code'=>500,
                'message'   =>$e->getMessage()
            ]);
            return false;
        }

    }
    //删除
    public static function delete($data)
    {
        //
        if(isset($data['ids'])){
            $adminInfo = self::read($data['ids']);
            if(!$adminInfo){
                self::setError([
                    'status_code'=> 4013,
                    'message'    => 'id not find in admins'
                ]);
                return false;
            }else{
                $admin = new Admin();
                if($res = $admin::destroy($data['ids'])){
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
    //新增和修改
    public function save($data,$id=null)
    {      
        $adminModel = $this->getAdminModel();
        if($id === null){
            return $adminModel->addNewAccount($data);
        }else{
            return $adminModel->updateNewAccount($data['admin_id'],$data);
        }
    }

    //管理员列表
    public static function _list($page)
    {
        if(Validate::is($page,'number')){
            $adminModel = new Admin();
            $list =$adminModel->page($page,50)->select()->toArray();
            if($list){
                $data['total'] = $adminModel->count();
                $data['list']  = $list;
                return $data;
            }else{
                self::setError([
                    'status_code'=>500,
                    'message'    =>$adminModel->getError()
                ]);
                return false;
            }
        }else{
            self::setError([
                'status_code'=> 4010,
                'message'    => 'the type of page is wrong'
            ]);
            return false;
        }
    }

    /**
     * 检查登录
     */
    public function checklogin($map)
    {

        $admin = new Admin();

        $map['is_del'] = 0;
        $res = $admin->field('admin_id,account,nickname')->where($map)->find();

        if($res){
            //更新数据
            $result = $res->getData();
            $payload['requesterID'] = $result['admin_id'];
            $payload['identity']    = 'admin';
            $payload['exp']         = time()+604800;
            $result['token']        = JWT::encode($payload,config('jwt-key'));
            $result['identity']     = 'admin';
            return $result;
        }else{
            return false;
        }
    }
    /**
     * 获取用户权限列表
     */
    public function getModuleByAdmin($admin_id)
    {
        $sql = "select * from yj_system_module where mod_id in(select rm.mod_id from yj_role_module as rm left join yj_admin_role as ar on rm.role_id = ar.role_id where ar.admin_id=8)";
        $admin =Admin::get($admin_id);
        return $admin->modules;
    }

    private function getAdminModel()
    {
        return new Admin();
    }

    public static function read($id)
    {
        $module = new Admin();
        if(is_array($id))
        {
            $ids = '';
            foreach ($id as $v){
                $ids.= (int)$v.',';
            }
            $ids = substr($ids, 0, -1);
            $count = $module->where('is_del','=','0')->where('admin_id','in',"$ids")->count();
            if($count==count($id)){
                $items = $module->with('roles,orgs')->where("is_del",'=','0')->where('admin_id','in',"$ids")->field('admin_id,account,nickname')->select()->toArray();
            }else {
                self::setError([
                    'status_code'=>4016,
                    'message'    => 'admin_id不存在'
                ]);
                return false;
            }
        }else{
            $count = $module->where('is_del','=','0')->where("admin_id = {$id}")->count();
            if($count>0){
                $items  = $module->with('roles,orgs')->where('is_del','=','0')->where("admin_id = {$id}")->field('admin_id,account,nickname')->find()->toArray();
            }else{
                self::setError([
                    'status_code'=>4016,
                    'message'    =>'admin_id不存在'
                ]);
                return false;
            }
        }
        return $items;
    }

    /**
     * 更新管理员
     * @param $id
     * @param $data
     * @return bool|false|int
     */
    public static function update($id,$data){
        $admin = new Admin();
        $adminInfo = self::read($id);
        if($adminInfo){
            if(isset($data['roles'])){
                $ar_data = [];
                foreach ($data['roles'] as $key=>$v){
                    $item = [];
                    $item['admin_id'] = $id;
                    $item['role_id']  = $v;
                    array_push($ar_data,$item);
                }
            }
            if(isset($data['orgs'])){
                $ao_data = [];
                foreach ($data['orgs'] as $key=>$v){
                    $item = [];
                    $item['admin_id'] = $id;
                    $item['org_id']   = $v;
                    array_push($ao_data,$item);
                }
            }
            Db::startTrans();
            try{
                Db::name('admin')->where('admin_id',$id)->strict(false)->field('account,nickname')->update($data);
                if(!empty($ar_data)){
                    Db::name('admin_role')->where('admin_id',$id)->delete();
                    Db::name('admin_role')->insertAll($ar_data);
                }
                if(!empty($ao_data)){
                    Db::name('admin_org')->where('admin_id',$id)->delete();
                    Db::name('admin_org')->insertAll($ao_data);
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
                'status_code'=>4016,
                'message'    =>'admin_id not find'
            ]);
            return false;
        }
    }
}