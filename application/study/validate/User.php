<?php
/**
 * Created by PhpStorm.
 * User: Chaegumi
 * Date: 2016/11/25
 * Time: 15:11
 */
namespace app\study\validate;

use think\Validate;

class User extends Validate{

    protected $rule = [
        'username' => 'require',
        'email' => 'email'
    ];

    protected $message = [
        'username.require' => '名称必须',
        'email' => '邮箱格式错误'
    ];

    // 自定义验证规则
    protected function checkName($value, $rule, $data){
        return $rule == $value ? true : '名称错误';
    }

}