<?php 
namespace app\common\behavior;
use app\common\service\AdminService;


class AutoLastInfo
{
    public function loginBehavior(&$params)
    {	
        $AdminService = new AdminService();
        $AdminService->autoLastInfo($params);
    }
    
}