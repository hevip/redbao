<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-9-5
 * Time: 上午9:45
 */
namespace greatsir\Notice;

use greatsir\Notice\Notice;
abstract class SmsNotice implements Notice
{
    abstract public function setMessage($content,$type);
    abstract public function setPersons();
    abstract public function setChannel();

    /**
     * 处理http请求
     */
    public function http()
    {

    }
}