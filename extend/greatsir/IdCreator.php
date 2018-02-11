<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2018/1/31
 * Time: 上午9:23
 */
/*
 * ID生成器
 * 64位
 * 标志位  时间戳  机器id  自增id
 *  1       41      10     12
 *   0     time    mid
 */
namespace greatsir;


class IdCreator
{
    private $startTime;
    /*
     * 设置开始时间
     */
    public function setStartTime($time)
    {
        $this->startTime = $time;
    }

    /*
     *获取下一个id
     */
    public function getNextId()
    {
        //
    }
}