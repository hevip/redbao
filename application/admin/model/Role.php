<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-11
 * Time: 下午3:29
 */

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;
class Role extends Model
{
    use SoftDelete;
    protected $deleteTime = 'is_del';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = false;
    // 定义全局的查询范围 未删除
    protected function base($query)
    {
        $query->where('is_del','=',0);
    }
    public function modules()
    {
        return $this->belongsToMany('app\\common\\model\\SystemModule','app\\common\\model\\RoleModule','mod_id','role_id');
    }
}