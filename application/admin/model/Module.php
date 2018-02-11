<?php
/**
 * 功能模块－模型
 * User: greatsir
 * Date: 17-6-30
 * Time: 上午9:09
 */
namespace app\admin\model;
use app\common\model\SystemModule;
use traits\model\SoftDelete;
class Module extends SystemModule
{
    use SoftDelete;
    protected $deleteTime = 'is_del';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'dateline';
    protected $updateTime = false;
    // 定义全局的查询范围 未删除
    protected function base($query)
    {
        $query->where('is_del','=',0)->order('orderby', 'asc');
    }
}