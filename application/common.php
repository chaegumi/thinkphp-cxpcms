<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function permissions_select($parent_id, $id){
	$permissions = \think\Db::table('permissions')->select();
	$perm_parr = array();
	foreach($permissions as $row){
		$perm_parr[$row['parent_id']][] = $row;
	}
	loop_parent_permissions($perm_parr, 0, 0, $parent_id, $id);
}
// 循环上级类别
function loop_parent_permissions($perm_parr, $parent_id, $curloop, $curid, $id){
	if(isset($perm_parr[$parent_id]) && count($perm_parr[$parent_id])>0){
	  if($id == ''){
		  foreach($perm_parr[$parent_id] as $row){
			echo '<option value="' . $row['id'] . '" ' . ($row['id'] == $curid ? 'selected="selected"' : '') . '>' . str_repeat(' - ', $curloop) . $row['permName'] . '</option>';
			loop_parent_permissions($perm_parr, $row['id'], $curloop + 1, $curid, $id);
		  }								  
	  }else{
		  foreach($perm_parr[$parent_id] as $row){
			if($row['id'] == $id) continue;
			echo '<option value="' . $row['id'] . '" ' . ($row['id'] == $curid ? 'selected="selected"' : '') . '>' . str_repeat(' - ', $curloop) . $row['permName'] . '</option>';
			loop_parent_permissions($perm_parr, $row['id'], $curloop + 1, $curid, $id);
		  }								  
	  }

	}
}

function cxp_update_cache(){
	
}


// 检查权限
if(!function_exists('check_permission')){
	function check_permission($permKey, $json = TRUE){
		$user = $CI->load->get_var('user');
		$perms = $user->userPerms;
		if(isset($perms[$permKey]) && $perms[$permKey]){
			
		}else{
			if($json){
				return array('success' => FALSE, 'msg' => '您没有权限操作:' . $permKey);
			}else{
				// set_status_header(500);
				$data['errorString'] = '500';
				// $content = $CI->load->view($CI->admin_theme . 'member/500', $data, TRUE);
				// $CI->output->set_output($content);
				// $CI->output->_display();
				exit;
			}
		}
	}
}

if(!function_exists('hasPermission')){
	function hasPermission($permKey){
		$user = $CI->load->get_var('user');
		$perms = $user->userPerms;
		if(isset($perms[$permKey]) && $perms[$permKey]){
			return TRUE;
		}else{
			return FALSE;
		}
	}
}

// 添加权限
function addPermission($permName, $permKey, $parent_id, $permType = 0, $rel_id = 0){
	$CI = &get_instance();
	$data = array(
		'permName' => $permName,
		'permKey' => $permKey,
		'parent_id' => $parent_id,
		'permType' => $permType,
		'rel_id' => $rel_id
	);
	$CI->db->insert('permissions', $data);
	$new_id = $CI->db->insert_id();
	return $new_id;
}