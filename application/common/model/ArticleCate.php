<?php
namespace app\common\model;
use traits\model\SoftDelete;

class ArticleCate extends \app\common\model\Base
{	
	use SoftDelete;
	protected $deleteTime = 'is_del';

	protected $autoWriteTimestamp = true;
	protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 设置当前模型对应的完整数据表名称
    protected $name = 'article_cate';
    protected $pk = 'cat_id';

    protected $insert  = ['level'];

    // 定义全局的查询范围 未删除 倒序
    protected function base($query)
    {
        $query->where("is_del","=",0)->where("hidden","=",0);
    }

    protected function setLevelAttr()
    {
        $data = $this->where('cat_id' ,'=', $this->getAttr('parent_id'))->value('level');
        return (int)$data+1;
    }

    public function getIsDelAttr($value)
    {
        $is_del = [0=>'否',1=>'是'];
        return isset($is_del[$value])?$is_del[$value]:'is_del';
    }

    public function getHiddenAttr($value)
    {
    	$hidden = [0=>'不隐藏',1=>'隐藏'];
    	return isset($hidden[$value])?$hidden[$value]:"hidden";
    }

    protected function initialize()
    {
        parent::initialize();
    }
}