<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 2017/12/6
 * Time: 上午11:49
 */

namespace greatsir;


class Snowflake
{
    /*
       * id生成器
       */
    const EPOCH = 1479533469598;
    const max12bit = 4095;
    const max41bit = 1099511627775;

    static $machineId = null;
    static $serviceId = null;

    public static function machineId($mId = 0) {
        self::$machineId = $mId;
    }



    public static function generateParticle($sid) {
        /*
        * Time - 42 bits
        */
        $time = floor(microtime(true) * 1000);

        /*
        * Substract custom epoch from current time
        */
        $time -= self::EPOCH;

        /*
        * Create a base and add time to it
        */
        $base = decbin(self::max41bit + $time);

        /*
        * Configured machine id - 10 bits - up to 1024 machines
        */
        if(!self::$machineId) {
            $machineid = self::$machineId;
        } else {
            $machineid = str_pad(decbin(self::$machineId), 6, "0", STR_PAD_LEFT);
        }

        /*
      * 4 bites业务id up to 16
      */
        $service = str_pad(decbin($sid),4,'0',STR_PAD_LEFT);

        /*
        * sequence number - 12 bits - up to 4096 random numbers per machine
        */
        $random = str_pad(decbin(mt_rand(0, self::max12bit)), 12, "0", STR_PAD_LEFT);

        /*
        * Pack
        */
        $base = $base.$machineid.$service.$random;

        /*
        * Return unique time id no
        */

        return bindec($base);
    }

    public static function timeFromParticle($particle) {
        /*
        * Return time
        */
        return bindec(substr(decbin($particle),0,41)) - self::max41bit + self::EPOCH;
    }
}