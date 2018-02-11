<?php
/**
 * 管理员service
 * User: greatsir
 * Date: 17-6-30
 * Time: 上午11:47
 */
namespace app\admin\service;
use app\common\service\BaseService;

use Firebase\JWT\JWT;
use think\Validate;
use think\Db;
use think\Debug;

class ProblemService extends BaseService
{
    /**
     * 问题列表显示
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

    /**
     * 问题列表添加 带id为更新，不带id为添加
     */
    public static function problemAdd($data)
    {
        //根据id存在选择更新或者添加
        if (!isset($data['id'])) {
            //添加
            $save_data = [
                'title'       => $data['title'],
                'content'     => $data['content'],
                'create_time' => time(),
            ];
            $result = Db::name('question')->insert($save_data);
        }else{

            $result = Db::name('question')->where('id',$data['id'])->update(['title'=>$data['title'],'content'=>$data['content']]);

        }

        if (!$result) {
            self::setError([
                'status_code' => '500',
                'message'     => 'Add or update failure'
            ]);
            return false;
        }else{
            return [
                'message'=> 'success'
            ];
        }
    }

    /**
     * 问题列表删除
     */
    public static function problemDelete($data)
    {
        //验证
        if (!is_numeric($data['id'])) {
            self::setError([
                'status_code' => '500',
                'message'     => 'The submission is not a number.'
            ]);
            return false;
        }

        $delete_result = Db::name('question')->where('id',$data['id'])->delete();

        if (!$delete_result) {
            self::setError([
                'status_code' => '500',
                'message'     => 'Delete failure'
            ]);
            return false;
        }else{
            return [
                'message'=> 'success'
            ];
        }
    }


}