<?php
namespace app\common\model;

use app\common\model\Base;

class ArticleContent extends Base
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'article_content';
                                
    /*public function article()
    {
    	return $this->belongsTo('Article','article_id','article_id');
    }*/

    /*protected $auto = ['clientip'];
    protected function setClientipAttr()
    {
        return '127.0.0.1';
    }*/
}