<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 11:41
 */
namespace app\admin\controller;

use think\Db;

class Ajax extends Base{
    function index(){

    }

    function setboolattribute(){
        return '111';
    }
	
	function check_value(){
		$field = input('get.field');
		$table = input('get.table');
		if($table && $field){
			$field_value = $this->request->get($field);
			
			$info = Db::table($table)->where($field, $field_value)->find();
			
			if($info){
				echo 'false';
			}else{
				echo 'true';
			}
		}else{
			echo 'true';
		}		
	}
}