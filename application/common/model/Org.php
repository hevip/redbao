<?php
namespace app\common\model;

use think\Model;
class Org extends Model
{
    protected $name = 'org';
    protected $pk   = 'org_id';

    // public function article()
    // {
    //     return $this->belongsTo('YjArticle');
    // }
}