<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-9-5
 * Time: 上午10:00
 */
namespace greatsir\Notice;

abstract class SocketNotice
{
    abstract public function setMessage($content);
    abstract public function setPersons();
    abstract public function setChannel($host,$port);
}