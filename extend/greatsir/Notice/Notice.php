<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-9-5
 * Time: 上午9:12
 */
namespace greatsir\Notice;

interface Notice
{
    public function setChannel();
    public function setMessage($content,$type);
    public function setPersons();
}