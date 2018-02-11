<?php
/**
 * 认证接口
 * User: greatsir
 * Date: 17-6-30
 * Time: 下午1:55
 */
namespace app\common\service\auth;
interface authInterface
{
    public function check($identity);
    public function createToken();
}