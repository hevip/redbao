<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-8-1
 * Time: 上午10:16
 */

namespace app\common\model;

use think\Model;
class RoleModule extends Model
{
    protected $name = 'role_module';
    protected $pk   = ['role_id,mod_id'];
}