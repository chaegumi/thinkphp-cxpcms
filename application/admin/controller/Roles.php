<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 11:34
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Validate;

class Roles extends Controller{
    function index(){
		return view('index');
    }

	function data(){
		$response = new \stdClass;
		$response->draw = $this->request->post('draw');
		
		
		$ccount = Db::table('roles')->alias('A')->count('id');
		$response->recordsTotal = $ccount;
		
		
		$results = Db::table('roles')->alias('A')->limit($this->request->post('length'), $this->request->post('start'))->select();
		
		$response->recordsFiltered = $response->recordsTotal;
		
		$response->data = array();
		foreach($results as $row){
			$data = array();
			$data['id'] = $row['id'];
			$data['roleName'] = $row['roleName'];
			$data['issys'] = $row['issys'];
			
			$response->data[] = $data;
		}
		
		return $response;		
	}

    function add(){
		
		$action = 'add';
		$this->assign('action', $action);
		$info = [
			'id' => 0,
			'roleName' => '',
		];
		$this->assign('info', $info);
		
		return view('edit');
    }

	function perm_data(){
		$permissions = new \app\admin\model\Permissions();
		$permissions = $permissions->select();
		$perm_parr = array();
		foreach($permissions as $row){
			$perm_parr[$row->parent_id][] = $row;
		}
	  
	    $roleid = intval(input('post.roleid'));
		$my_acl=new \cxpcms\member\Member_acl();
		// $my_acl->getAllPerms();
		$rPerms = $my_acl->getRolePerms($roleid);
	    
	    echo '[' . $this->loop_parent($perm_parr, 0, 0, 0, '', $rPerms) . ']';
	}
	
	function loop_parent($perm_parr, $parent_id, $curloop, $curid, $html, $rPerms){
		if(isset($perm_parr[$parent_id]) && count($perm_parr[$parent_id])>0){
		  
		  foreach($perm_parr[$parent_id] as $row){
			  
			if(isset($rPerms[$row->permKey]['value'])){
				if ($rPerms[$row->permKey]['value'] === true) { 
					$chk = '1'; 
				}
			}
			if(isset($rPerms[$row->permKey]['value'])){
				if ($rPerms[$row->permKey]['value'] != true) { 
					$chk = '0'; 
				}
			}
			if (!array_key_exists($row->permKey,$rPerms)) { 
				$chk = 'x'; 
			}
			  
			  if(isset($perm_parr[$row->id]) && count($perm_parr[$row->id])>0){
				$html .= "{id:" . $row->id . ",name:'" . $row->permName . "', chk:'" . $chk . "', children:[";
				$html = $this->loop_parent($perm_parr, $row->id, $curloop + 1, $curid, $html, $rPerms) . ']},';
				
			  }else{
				  $html .= "{id:" . $row->id . ",name:'" . $row->permName . "', chk:'" . $chk . "'},";
			  }
		  }								  
		}else{
			// $html .= ']},';
		}
		return $html;
	}

    function edit(){
		
		$action = 'edit';
		$this->assign('action', $action);
		
		$info = Db::table('roles')->where('id', input('get.id'))->find();
		$this->assign('info', $info);
		
		return view('edit');
    }

    function save(){
		$id = intval(input('post.id'));
		$validate = new Validate([
			'roleName' => 'require'
		]);
		
		$data = [
			'roleName' => input('post.roleName'),
			'issys' => 1,
		];
		if(!$validate->check($data)){
			return ['success' => false, 'msg' => $validate->getError()];
		}else{
			if($id === 0){
				
				$new_role_id = Db::table('roles')->insertGetId($data);
				
				$this->save_role_perm($new_role_id);
				
				return ['success' => true, 'msg' => '添加角色成功'];
				
			}else{
				
				Db::table('roles')->where('id', $id)->update($data);
				
				$this->save_role_perm($id);
				
				cxp_update_cache();
				
				return ['success' => true, 'msg' => '修改角色成功'];
				
			}
		}	
    }

    function save_role_perm($id){
		$roleID = $id;
		
		foreach (input('post.') as $k => $v)
		{
			if (substr($k,0,5) == "perm_")
			{
				$permID = str_replace("perm_","",$k);
				if ($v == 'x')
				{
					$strSQL ="DELETE FROM `role_perms` WHERE `roleId` = ? AND `permId` = ?";
					Db::query($strSQL,array($roleID,$permID));
					continue;
				}
				$strSQL = "REPLACE INTO `role_perms` SET `roleId` = ?, `permId` = ?, `value` = ?";
				Db::query($strSQL,array($roleID,$permID,$v));
			}
		}	
    }

    function delete(){

    }
}