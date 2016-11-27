<?php
/**
 * Created by PhpStorm.
 * User: Chaegumi
 * Date: 2016/11/25
 * Time: 14:13
 */
namespace app\study\model;

use think\Model;

class Users extends Model{

    protected function initialize(){
        parent::initialize();

    }

    function getStatusAttr($value){
        $status = [-1 => '删除', '0' => '禁用', '1' => '正常', '2' => '待审核'];
        return $status[$value];
    }
}