<?php
namespace app\admin\model;

use think\Request;

class Admin extends \app\common\model\Admin
{	
	public function addNewAccount($data)
	{	
		$result = $this->allowField(['account','password','nickname'])
			 ->validate('Admin.save')->save($data);	
		$error = $this->getError();
		if($error)return $error;
		return $this->getData();
	}

	public function updateNewAccount($id,$data)
	{
		if(empty($id))return '请指定要修改的用户';
		$result = $this->allowField(['nickname'])
			->validate('Admin.update')
			->save($data,[$this->getPk() => $id]);
		if($result === 0)return '该用户不存在！';
		$error = $this->getError();
		if($error)return $error;
		return $this->getData();
	}

    public function deleteAdmin($id)
    {	
    	if(empty($id))return '请指定删除用户';
    	$result = Admin::destroy($id);
    	if($result === 1)return '删除成功！';
    	if($result === 0)return '该用户不存在！';

    }
    public function roles()
    {
        return $this->belongsToMany('app\\common\\model\\Role','app\\common\\model\\AdminRole');
    }
    public function orgs()
    {
        return $this->belongsToMany('app\\common\\model\\Org','app\\common\\model\\AdminOrg','org_id','admin_id');
    }
}
