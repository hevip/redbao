<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用调试模式
    'app_debug'              =>true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'common',
    // 禁止访问模块
    'deny_module_list'       => [''],
    // 默认控制器名
    'default_controller'     => 'index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL','REQUEST_URI'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => true,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => true,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'=>'',
    //'exception_handle'       => '\\app\\common\\exception\\Json',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache' =>  [
        // 使用复合缓存类型
        'type'  =>  'complex',
        // 默认使用的缓存
        'default'   =>  [
            // 驱动方式
            'type'   => 'File',
            // 缓存保存目录
            'path'   => CACHE_PATH,
        ],
        // 文件缓存
        'file'   =>  [
            // 驱动方式
            'type'   => 'file',
            // 设置不同的缓存保存目录
            'path'   => RUNTIME_PATH . 'file/',
        ],
        // redis缓存
        'redis'   =>  [
            // 驱动方式
            'type'   => 'redis',
            // 服务器地址
            'host'       => '127.0.0.1',
        ],
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    //jwt秘钥配置
    'jwt-key'=>'weijuweiju',
    //客户端公钥
    'client_rsa_public_key'=>'-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQChv5KAZjYf680lzWBsKVXYTWG5
cQ7cjOCM+wbaE73LbzCfej1/ADzhFS/RLJiV4NeEfwNL2uOnLBoe+wGIjimIrw4t
VhUMVHwz9rubyxGjLakcRYA2mzi4O5/oZoUj+EzLlMJxPL6KkpHoEOp1y/lEh5wS
pD/jNc5QPPW92aMA1QIDAQAB
-----END PUBLIC KEY-----',
    //server端解密私钥
    'rsa_private_key'=>'-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQDjSETE5bs4LCBWI3JT0oB+/HKVbwEpR5adoTrtl+CsmS65k3mK
v5etXO+EBzRHIsR6pe7SsW0TbpBrMdO6sQgPC94KlZZN98fW3VurRcAwK/vbCOcN
Q3z76dnZ/NokQ2ot8yRp8/wft7PhNeHEWdN4dUc7kfB9lMfc4HrSRf8rwQIDAQAB
AoGBALQ0RqI+69Q72F+ztyAS1OLaUhd9bdRG+Hp+rXRYaUEwK0XkUgfapO5Fs3ph
I+gK/vGnwtW2657umP8FALUTTCjzCXXI9fUOx0WNhLeQ2BMFdb/tRS5ZG85fLV36
zIkARtGmGRrLcZTRfgQrIPk8c5PDLYWWGVvFQdkNbgzoU5AJAkEA+9McJHe655Uj
gXcmkcXJrbWtUsu4Xnxv9Ng2zQUFI45EswjSLUo203cSHlRgmYoYN58tvLFAb9e8
KZ3J5E+0swJBAOcM/JlcnvZmfPv4CtlI4gv4r1jPS4/eTxF+ZRmcU+yxAo8lznCa
trF6kkqJrFLrlK/AgZydehqdEoTpTGrJn7sCQQCIbWG0vAzNA7Y9oICLvty5OFDT
Jz0WK1I4Ep71yX90ONItMF01Xhx/yeVN+yZuaCsgjyMnM9wV+4mb/jjvcOMTAkEA
kJERpCvN0sKBxW6H6zitJ2xs2pap4tToiDubqpCj+l8vCL5REyfa0+WrjzPhPOjm
WamL76Cg+/H32m3XQKHzbQJARM1gu3nHL8RcuiLj36UMO7kJMzwF23yBAldA7dot
z63kAcF4xNmXNNkxDzPTacocahfnGB4DjkOFITUht6AG3Q==
-----END RSA PRIVATE KEY-----',
    'wechatapp_id'=>'wxf1a1b8b557b1ea45',//'wx839d0f26e93bccf9',//wx839d0f26e93bccf9
    'wechatapp_secret'=>'1c7e4749aebab0394ec1a82b96db84a9',//'bab6759c823b7be3996c349f80f44d5e',
    'qiniu_accesskey'=>'RzIZMgHgdxsAzCDddJ3wJYv7qb7FcdFEyx0sHdaa',
    'qiniu_secretKey'=>'sbmuFnx4krD9yoH2bEzQ5Ol4ALuW4fVHIjxSrHkT',
    'qiniu_bucket'=>'redenvelopes',//'weiju',
    'qiniu_notify'=>'https://api.4s21u.cn/qiniu/upCallback',
    'qiniu_bucket_domain'=>'http://qnbn.2962xm.cn',
    'baiduspeech_appid'=>'10813685',
    'baiduspeech_apikey'=>'FCddY0emtn2BICrQSO6vk85Z',
    'baiduspeech_secret'=>'4c4676389eddbaf62b3ee66621c5dcef'
    //z0.5a7d302eb94653190090a180
];
