<?php
/**
 * 管理员service
 * User: greatsir
 * Date: 17-6-30
 * Time: 上午11:47
 */
namespace app\admin\service;
use app\common\service\BaseService;
use app\admin\model\Rule;


use Firebase\JWT\JWT;
use greatsir\RedisClient;
use think\Validate;
use think\Db;
use think\Debug;

class ConfigureService extends BaseService
{
    //配置项列表
    public static function configureList()
    {
        $data = Db::name('backstage')->select();
        if (empty($data)) {
            self::setError([
                'status_code'=> 500,
                'message'    => 'Query data is empty',
            ]);
        }else{
            return $data;
        }
    }

    //增加或更新
    public static function addConfigure($data)
    {
        //验证post值
        $validate = validate('Configure');

        if(!$validate->check($data)){
            self::setError([
                'status_code'=>4015,
                'message'    =>$validate->getError()
            ]);
            return false;
        }

        $sdata = [
            'setitem' => $data['setitem'],
            'item'    => $data['item']
        ];

        if (empty($data['id'])) {
            //新增
            $data_result = Db::name('backstage')->insert($sdata);

        }else{
            //更新
            if($data['id']==6){
                $redis = RedisClient::getHandle(0);
                $redis->setKey('ad_red_ids',$data['item']);
            }

            $data_result = Db::name('backstage')->where('id',$data['id'])->update($sdata);
        }



        if($data_result){
            return [
                'status_code' => 1,
                'message'     => 'success'
            ];
        }else{
            self::setError([
                'status_code'=> 500,
                'message'    => 'Update or increase failure'
            ]);
            return false;
        }
    }

    //删除
    public static function delConfigure($data)
    {
        //验证数字
        if (!is_numeric($data['id'])) {
            self::setError([
                'status_code'=> 4015,
                'message'    => 'The submission is not a number.',
            ]);
            return false;
        }
        $data_result = Db::name('backstage')->where('id',$data['id'])->delete();

        if($data_result){
            return [
                'status_code' => 1,
                'message'     => 'success'
            ];
        }else{
            self::setError([
            'status_code'=> 500,
            'message'    => 'Delete failure'
            ]);
            return false;

        }
    }

    public static function getProportion()
    {
        $data_result = Db::name('backstage')->where('id',5)->find();
        if(!empty($data_result)){
            return [
                'status_code' => 1,
                'message'     => $data_result['item']
            ];
        }else{
            self::setError([
                'status_code'=> 500,
                'message'    => 'Delete failure'
            ]);
            return false;

        }
    }
}