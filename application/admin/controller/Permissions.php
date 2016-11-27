<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 11:37
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Validate;

class Permissions extends Controller{

    function index(){
		return view('index');
    }

    function data(){
		// $permissions = Db::table('permissions')->fetchClass('\think\Collection')->select();
		$permissions = new \app\admin\model\Permissions();
		$permissions = $permissions->select();
		$perm_parr = array();
		foreach($permissions as $row){
			$perm_parr[$row->parent_id][] = $row;
		}
		echo '[' . $this->loop_parent($perm_parr, 0, 0, 0, '') . ']';
    }

	function loop_parent($perm_parr, $parent_id, $curloop, $curid, $html){
		if(isset($perm_parr[$parent_id]) && count($perm_parr[$parent_id])>0){
		  
			foreach($perm_parr[$parent_id] as $row){
				if(isset($perm_parr[$row->id]) && count($perm_parr[$row->id])>0){
					$html .= "{id:" . $row->id . ",name:'" . $row->permName . "', permKey:'" . $row->permKey . "', children:[";
					$html = $this->loop_parent($perm_parr, $row->id, $curloop + 1, $curid, $html) . ']},';
					
				}else{
					  $html .= "{id:" . $row->id . ",name:'" . $row->permName . "', permKey:'" . $row->permKey . "'},";
				}
			}								  
		}else{
			// $html .= ']},';
		}
		return $html;
	}

    function add(){
		
		$info = [
			'id' => 0,
			'parent_id' => input('get.parent_id', 0),
			'permName' => '',
			'permKey' => ''
		];
		$this->assign('info', $info);
		
		return view('edit');
    }

    function edit(){
		
		$id = input('get.id');
		
		if($id){
			
			$info = Db::table('permissions')->where('id', $id)->find();
			
			$this->assign('info', $info);
			
			
			
			return view('edit');
		}else{
			return '';
		}
		
		
		
    }

    function save(){
		$id = intval(input('post.id'));
		$validate = new Validate([
			'permName' => 'require',
			'permKey' => 'require',
		]);
		
		$data = [
			'parent_id' => input('post.parent_id'),
			'permName' => input('post.permName'),
			'permKey' => input('post.permKey'),
		];
		
		if(!$validate->check($data)){
			return ['success' => false, 'msg' => $validate->getError()];
		}else{
			if($id === 0){
				
				$new_perm_id = Db::table('permissions')->insertGetId($data);
				// 添加新权限总后台帐号自动授权
				Db::table('role_perms')->insert([
					'roleID' => 5,
					'permID' => $new_perm_id,
					'value' => 1
				]);
				// 更新权限缓存
				cxp_update_cache();
				
				return ['success' => true, 'msg' => '添加权限成功'];
			}else{
				Db::table('permissions')->where('id', $id)->update($data);
				
				cxp_update_cache();
				return ['success' => true, 'msg' => '修改权限成功'];
			}
		}
    }

    function delete(){

    }
}