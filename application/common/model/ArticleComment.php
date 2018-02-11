<?php
namespace app\common\model;

use traits\model\SoftDelete;
use think\Request; 
class ArticleComment extends \app\common\model\Base
{	
    //初始化
	protected function initialize()
    {
        parent::initialize();
    }

    //1：表名
    protected $name = 'article_comments';

    //2：主键
    protected $pk = 'comment_id';

    //3：软删除
    use SoftDelete;
    protected $deleteTime = 'is_del';

    //4：基础查询
    protected function base($query)
    {
        $query->where('is_del','=',0)->order('create_time','desc');
    }

    //5：新增和修改的时间
    //protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';

    //6：添加修改器
    public function setContentAttr($value)
    {
        return $value;
    }

    //7:获取器
    public function getIsDelAttr($value)
    {
		if($value === 0){
			return false;
		}else{
			return true;
		}
    }

    //8：数据完成自动存入的数据
    // protected $insert = [];
    // protected $update = [];
    // protected $auto = [];
    // protected  function setXXXAttr()
    
    //9：过滤保存字段（allowFiled）
    
    //10：保存save的验证规则()
    
    //10: 定义一对一关联
    
    //11: 定义一对多关联
    
    //12: 定义相对关联
    public function article()
    {
        return $this->belongsTo('article');
    }
    
    
}