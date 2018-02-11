<?php
namespace app\common\model;

class AdminRole extends \app\common\model\Base
{


    protected $name = 'admin_role';

    protected $pk = 'role_id';

    protected function initialize()
    {
        parent::initialize();
    }

    use SoftDelete;
	  protected $deleteTime = 'is_del';

    protected function base($query)
    {
        $query->where("is_del","=",0)->order("create_time","desc");
    }

  	protected $autoWriteTimestamp = true;
  	protected $createTime = "create_time";
  	protected $updateTime = "update_time";


    public function setAdminIdAttr($value)
    {
        return $value;
    }


    public function getAdminRoleAttr($value)
    {
        return $value;
    }


                                
                                
                                
}