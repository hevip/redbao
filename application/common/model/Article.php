<?php
namespace app\common\model;

use app\common\model\Base;
use traits\model\SoftDelete;
use think\Request;
use app\common\service\auth\AuthService; 
use anu\SingleFactory;
class Article extends Base
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
    protected $name = 'article';                      
    protected $pk = 'article_id';
    //protected $auto = ['creater'];
    // 定义全局的查询范围 未删除 倒序
    protected function base($query)
    {
        $query->where('is_del','=',0)->order('create_time','desc');
    }
    protected function setCreatedUserIdAttr()
    {
        $auth = SingleFactory::overAllData('auth'); 
        return $auth['admin_id'];
    }
    // 获取数据的字段值后自动进行处理
    // 获取全部原始数据
    public function getIsDelAttr($value)
    {
        $is_del = [0=>false,1=>true];
        return isset($is_del[$value])?$is_del[$value]:'is_del';
    }
    public function getAllowCommentAttr($value)
    {
    	$allow_comment = [0=>false,1=>true];
    	return isset($allow_comment[$value])?$allow_comment[$value]:'allow_comment';
    }
    public function getStatusAttr($value)
    {
    	$status = [0=>false,1=>true];
    	return isset($status[$value])?$status[$value]:"status";
    }
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
    public function articleOrg()
    {
        return $this->hasOne('app\\common\model\\Org','org_id','org_id')->field('org_id,org_name');
    }

    public function articleCate()
    {
        return $this->hasOne('ArticleCate','cat_id','cat_id')->field('cat_id,title');
    }
    public function artContent()
    {
        return $this->hasOne('article_content','article_id','article_id');
    }

    public function articleCreater()
    {
        return $this->hasOne('app\\common\\model\Admin','admin_id','creater')->field('admin_id,account,nickname');
    }

    // 定义外键的名称
    public function comments()
    {
        return $this->hasMany('Comment','art_id');
    }
}