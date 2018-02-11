<?php
namespace app\common\model;

use app\common\model\Base;

use traits\model\SoftDelete;
use think\Request;
use app\common\service\auth\AuthService;
use anu\SingleFactory;

class Property extends Base
{
	/**
	 *  问题；添加$insert 属性，不论该字段在save数据时，总是使用该属性
	 *  问题：在定义insert属性时，value值不能使用php函数
	 */
	
    
    use SoftDelete;
    protected $deleteTime = 'is_del';
    // 自动写入创建和更新的时间戳字段
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 设置当前模型对应的完整数据表名称
    protected $name = 'property';
    protected $pk = 'pro_id';
    //protected $auto = ['created_user_id'];
    
    // 定义全局的查询范围 未删除 倒序
    protected function base($query)
    {
    	$query->where('is_del','=',0)->order('create_time','desc');
    }

    // 获取数据的字段值后自动进行处理
    // 获取全部原始数据
    public function getIsDelAttr($value)
    {
    	$is_del = [0=>false,1=>true];
    	return isset($is_del[$value])?$is_del[$value]:'is_del';
    }

}