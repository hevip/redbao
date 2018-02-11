<?php
/**
 * Created by PhpStorm.
 * User: greatsir
 * Date: 17-7-13
 * Time: 下午3:39
 */

namespace greatsir;


class Tree
{

    function make_tree($list,$pk='id',$pid='pid',$child='_child',$root=0){
        $tree=array();
        $packData=array();
        foreach ($list as  $data) {
            $packData[$data[$pk]] = $data;
        }
        foreach ($packData as $key =>$val){
            if($val[$pid]==$root){//代表跟节点
                $tree[]=& $packData[$key];
            }else{
                //找到其父类
                $packData[$val[$pid]][$child][]=& $packData[$key];
            }
        }
        return $tree;
    }

}