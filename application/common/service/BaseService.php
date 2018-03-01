<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-6-28
 * Time: 下午1:03
 */
namespace app\common\service;
use think\Response;
class BaseService
{
    private static $error = [
        'status_code'=>4000,
        'message'    => 'request params are wrong'
    ];
    public static function setError(Array $error)
    {
        self::$error = $error;
    }
    public static function getError()
    {
        return self::$error;
    }
}