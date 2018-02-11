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
use think\Validate;
use think\Db;
use think\Debug;

class RuleService extends BaseService
{
    public static function addRule($uid,$data)
    {

        //验证post值
        $validate = validate('Rule');

        if(!$validate->check($data)){
            self::setError([
                'status_code'=>4015,
                'message'    =>$validate->getError()
            ]);
            return false;
        }

        $save_rule = Db::name('rule')->insert(['rule'=>$data['rules'],'status'=>1]);

        if ($save_rule) {
            return [
                'status_code' => 1
            ];
        }else{
            self::setError([
                'status_code'=> 500,
                'message'    => 'Storage data failure'
                ]
            );
            return false;
        }
    }

    public static function delRule($uid,$data)
    {
        //验证规则编号
        if (!is_numeric($data['id'])) {
            self::setError([
                'status_code'=>500,
                'message'    =>"Numbered is not a number"
            ]);
            return false;
        }

        //判断是否存在
        $isset_rule = Db::name('rule')->where('id',$data['id'])->find();

        if(empty($isset_rule)){
            self::setError([
                'status_code'=>500,
                'message'    =>"This ID does not exist"
            ]);
            return false;
        }

        $updata = Db::name('rule')->where('id',$data['id'])->delete();

        if ($updata) {
            return [
                'status_code' => 1,
                'message'     => true
            ];
        }else{
            self::setError(
                [
                    'status_code'=> 500,
                    'message'    => 'Stored data failed or exist'
                ]
            );
            return false;
        }

    }
}