<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-6-30
 * Time: 下午2:17
 */
namespace app\common\service\auth;

use app\admin\service\AdminService;
use app\member\service\MemberService;
use app\open\service\DeveloperService;
use think\Response;
use think\Hook;

class account implements authInterface
{
    public function check($identity)
    {
        // TODO: Implement check() method.
        if(input('?post.account')){
            $map['account'] = input('post.account');
        }else{
            return Response::create([
                'status'=>'failed',
                'error' =>[
                    'status_code'=> 4003,
                    'message'    => 'missing params account '
                ]
            ],'json');
        }
        if(input('?post.password')){
            $map['password'] = md5(input('post.password'));
        }else{
            return Response::create([
                'status'=>'failed',
                'error' =>[
                    'status_code' => 4004,
                    'message'     => 'missing params password'
                ]
            ],'json');
        }
        switch ($identity){
            case 'admin':

                $admin = new AdminService();
                $adminInfo = $admin->checklogin($map);
                if($adminInfo){
                    return Response::create([
                        'status' => 'success',
                        'data'   =>$adminInfo
                    ],'json');
                }else{
                    return Response::create([
                        'status' => 'failed',
                        'error'  => [
                            'status_code' => 4005,
                            'message'     => 'account or password is error'
                        ]
                    ],'json');
                }
                break;
            case 'yezhu':
                $member = new MemberService();
                $memberInfo = $member->checklogin($map);
                if($memberInfo){
                    //
                    $memberInfo['face'] = 'http://www.sinoyjlm.com/attachs/'.$memberInfo['face'];
                    return Response::create([
                        'status' => 'success',
                        'data'   =>$memberInfo
                    ],'json');
                }else{
                    return Response::create([
                        'status' => 'failed',
                        'error'  => [
                            'status_code' => 4005,
                            'message'     => 'account or password is error'
                        ]
                    ],'json');
                }
                break;
            case 'developer':
                $developer = new DeveloperService();
                $developerInfo = $developer->checklogin($map);
                if($developerInfo){
                    return Response::create([
                        'status' => 'sucess',
                        'data'   =>$developerInfo
                    ],'json');
                }else{
                    return Response::create([
                        'status'=>'failed',
                        'error' =>[
                            'status_code'=>4005,
                            'message'    =>'account or password is error'
                        ]

                    ]);
                }
                break;
            default:
                return Response::create([
                    'status' => 'failed',
                    'error'  => [
                        'status_code' => 4006,
                        'message'     => 'illegal identity'
                    ]
                ],'json');

        }


    }
    public function createToken()
    {
        // TODO: Implement createToken() method.
    }
}