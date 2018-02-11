<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-6-28
 * Time: 下午1:16
 */
namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;
class Base extends Model
{
	 public static $pageLimit = 20;
    //use SoftDelete;
    //protected $deleteTime = 'is_del';
    // 自动写入创建和更新的时间戳字段
    //protected $autoWriteTimestamp = true;
	//组装查询语句
    public function createSearch()
    {

    }

    public function getColoumByOther($where,$coloum=null)
    {   
        $data = $this->where($where)->select();
        $data = $this->objToArr($data);
        if(empty($coloum))return $data;
        $result = [];
        foreach ($data as $key => $value) {
            if(is_string($coloum))$result[] = $value[$coloum];
            if(isset($coloum['key']) && isset($coloum['value']))
                $result[$value[$coloum['key']]] = $value[$coloum['value']];
        }
        if(empty($result))return '数据不存在';
        return $result;
    }

    public  function objToArr($array)
    {   
        $result2=[];
        foreach ($array as $key => $value) {
            $result2[$key]=$value->toArray();
        }
        return $result2;
    }
}