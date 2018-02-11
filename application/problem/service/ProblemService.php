<?php
/**
 * Created by PhpStorm.
 * Pay: greatsir
 * Date: 2018/1/26
 * Time: 下午4:22
 */
namespace app\pay\service;

use app\common\model\Cash;
use app\common\service\BaseService;
use think\Db;
use think\Loader;

class ProblemService extends BaseService
{
    /**
     * 常见问题列表
     */
     public static function problemList()
     {
        $problem_data = Db::name('question')->field('id,title,content')->select();

        if (empty($problem_data)) {
            self::setError([
                'status_code' => '500',
                'message'     => 'The result of the query is empty'
            ]);
            return false;
        }else{
            return $problem_data;
        }
     }

}