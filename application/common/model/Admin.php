<?php
namespace app\common\model;
use traits\model\SoftDelete;
use think\Request;
use app\common\model\Base;
/**
 * 管理员管理类restful API
 * 
 */
class Admin extends Base
{	
    // 设置当前模型对应的完整数据表名称
    protected $name = 'admin';
    protected $pk = 'admin_id';

    use SoftDelete;
    protected $deleteTime = 'is_del';

    protected $type = [
        'last_login'=>'datetime'
    ];
    protected function base($query)
    {
        $query->where('is_del', '=', 0);
    }

    // 自动写入创建和更新的时间戳字段
	protected $autoWriteTimestamp = true;
	protected $createTime = 'create_time';
    protected $updateTime = '';


    //login_behavior自动填入上次登陆信息
    public function autoLastInfo($info)
    {   
        if($info['status'] === 'failed')return '缺少登陆人信息';
        $ip = Request::instance()->ip();     
        $data = ['last_ip' => sprintf('%u',ip2long($ip)),'last_login' => time()];
        $result = $this->allowField(['last_login','last_ip'])
             ->validate('Admin.last_login_info')
             ->save($data,[$this->getPk()=>$info['data']['admin_id']]);
        if(!empty($result))return $this->getData();
        return '上次登陆信息填入失败';
    }

    //修改器
    public function setPasswordAttr($value)
    {   
        return md5($value);
    }
    
}