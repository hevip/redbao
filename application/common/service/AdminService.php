<?php
namespace app\common\service;

use app\common\model\Admin;
class AdminService extends BaseService
{

    public function autoLastInfo($info)
    {
        $adminModel = new Admin();
        return $adminModel->autoLastInfo($info);
    }
}