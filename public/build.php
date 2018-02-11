<?php

return [

    //暂时不支持一次性生成多个model
    'common'=>[
        // 'namespace' => 'app\common\model',
        '__dir__' => ['model'],
        'table_name'     =>   ['admin_role'],
        // 'model'     =>   ['AdminRole'],
        // 'modelName' => ['AdminRole2'],
        // 'extends' => '\app\common\model\Base',
        // 'initialize' => "protected function initialize()\n{\nparent::initialize2();\n}",
        // 'pk' => "role_id",
        
        // 'softdel' => false,
        // 'softdel' => "is_del2",
        
        // 'basequery' => false,
        // 'basequery' => [
        //     ['where' => "\"is_del\",\"=\",0"],
        //     ['order' => "\"create_time\",\"desc\""],
        //     ['limit' => "5"],
        // ],
        
        // 'create_update_time' => false,
        // 'create_update_time' => [
        //     // '固定属性' => '字段名'
        //     'createTime' => 'create_time',
        //     'updateTime' => 'update_time',
        // ],
        
        //修改器
        // 'setAttr' => false,
        // 'setAttr' => '',
        // 
        //获取器
        // 'getAttr' => false,
        // 'getAttr' => '',
        // 
        //数据自动完成
        // 'dataComplete' => false,
        // 'dataComplete' => '',




    ]



];
