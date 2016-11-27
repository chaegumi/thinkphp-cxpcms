<?php
/**
 * Created by PhpStorm.
 * User: Chaegumi
 * Date: 2016/11/25
 * Time: 13:45
 */
namespace app\study\controller;

use think\Db;

class Dbtest{
    function index(){

        //
        $user_table = Db::getTableInfo('users');
        dump($user_table);

        // find方法查询结果不存在，返回null
        $user = Db::table('users')->where('id', 3)->field(true)->find();

        dump($user);

        // $user3 = db('users')->where('id', 3)->find();
        // dump($user3);

        // select方法不存在，返回空数组
        $users = Db::table('users')->where('status', 1)->field(true)->select();
        dump($users);

        // 查询某个字段的值,不存在返回false
        $username = Db::table('users')->where('id', 3)->value('username');
        dump($username);
        // 某列，不存在，返回空数组
        $username = Db::table('users')->where('id', 3)->column('username', 'id');
        dump($username);
    }

    function insert(){
        // insert 方法成功返回添加成功的条数
        Db::table('users')->insert(['foo' => 'bar']);

        //
        Db::table('users')->getLastInsID();
        //
        Db::table('users')->insertGetId([]);

        // 添加多条数据
        Db::table('users')->insertAll([]);
    }

    function update(){
        // update 方法返回影响数据的条数，没修改任何数据返回 0
        Db::table('users')->where('id', 3)->update([]);

        // 更新某个字段
        Db::table('users')->where('id', 3)->setField('username', '');
    }
}