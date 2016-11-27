<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 11:31
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Validate;

class Users extends Base{
    function index(){
        return view('index');
    }

    function data(){
        $response = new \stdClass;
        $response->draw = $this->request->post('draw');

        $search = $this->request->post('search/a');
		
        $keyword = '';
        if($search) $keyword = $search['value'];
        // $this->session->set_userdata('search', $search);
        // $perpage = 10;


        if($keyword){
            $ccount = Db::table('users')->alias('A')->where('A.username', $keyword)->whereOr('A.email', $keyword)->count('id');
        }else{
            $ccount = Db::table('users')->alias('A')->count('id');
        }

        $response->recordsTotal = $ccount;

        // $offset = $response->draw * $perpage;

        if($keyword){
            $results = Db::table('users')->alias('A')->where('A.username', $keyword)->whereOr('A.email', $keyword)->order('A.id desc')->limit($this->request->post('length'), $this->request->post('start'))->select();
        }else{
            $results = Db::table('users')->alias('A')->limit($this->request->post('length'), $this->request->post('start'))->select();
        }

        $response->recordsFiltered = $response->recordsTotal;

        $response->data = array();
        foreach($results as $row){
            $data = array();
            $data['id'] = $row['id'];
            $data['username'] = $row['username'];
            $data['status'] = $row['status'];
            $data['email'] = $row['email'];
            $data['reg_time'] = $row['reg_time'];
            $data['login_times'] = $row['login_times'];
            $data['last_login_time'] = $row['last_login_time'];
            $data['issys'] = $row['issys'];
            $response->data[] = $data;
        }

        return $response;
    }

    function add(){
		
		$roles = Db::table('roles')->select();
		$this->assign('roles',$roles);
		$userRoles = array();
		$this->assign('userRoles', $userRoles);
		
		$perms = array();
		
		$info = [
			'id' => 0,
			'username' => '',
			'email' => '',
			'password' => '',
			'roles' => $roles
		];
		$this->assign('info', $info);
		
        return view('edit');
    }

    function edit(){
		$id = intval(input('get.id'));
		
		$roles = Db::table('roles')->select();
		$this->assign('roles',$roles);
		
		$info = Db::table('users')->where('id', $id)->find();

		$this->assign('info', $info);
		
		$member_acl1 = new \cxpcms\member\Member_acl($id);
		$perms = $member_acl1->getPermArr();
		
		$this->assign('perms', $perms);
		$userRoles = $member_acl1->getUserRoles();
		$this->assign('userRoles', $userRoles);
		
        return view('edit');
    }

    function save(){
		$id = intval($this->request->post('id'));
		
		$validate = new Validate([
			'username' => 'require'
		]);
		
		$data = [
			'username' => trim($this->request->post('username')),
			'password' => trim($this->request->post('password')),
			'email' => trim(input('post.email')),
			'status' => intval(input('post.status'))
		];
		
		if(!$validate->check($data)){
			return ['success' => false, 'msg' => $validate->getError()];
		}else{
			if($id === 0){
				$new_user_id = Db::table('users')->insertGetId([
					'username' => $data['username'],
					'password' => password_hash($data['password'], PASSWORD_BCRYPT),
					'email' => $data['email'],
					'status' => $data['status']
				]);
				
				Db::table('user_profile')->insert([
					'user_id' => $new_user_id,
					'title' => '',
					'photo' => '',
					'gender' => '男'
				]);
				
				// 用户角色
				$rolesarr = input('post.roles/a');
				if($rolesarr){
					$sql = 'insert into user_roles(userID, roleID) values';
					$tstr = '';
					foreach($rolesarr as $v){
						$tstr .= '(' . $new_user_id . ', ' . $v . '),';
					}
					if($tstr != ''){
						$sql .= rtrim($tstr, ',');
						Db::query($sql);
					}
				}
				
				cxp_update_cache();
				
				return ['success' => true, 'msg' => '添加帐号成功'];
				
			}else{
				
				Db::table('users')->where('id', $id)->update([
					'username' => $data['username'],
					'email' => $data['email'],
					'status' => $data['status']
				]);
				
				// 用户角色
				$rolesarr = input('post.roles/a');
				if($rolesarr){
					Db::table('user_roles')->where('userID', $id)->delete();
					
					$sql = 'insert into user_roles(userID, roleID) values';
					$tstr = '';
					foreach($rolesarr as $v){
						$tstr .= '(' . $id . ', ' . $v . '),';
					}
					if($tstr != ''){
						$sql .= rtrim($tstr, ',');
						Db::query($sql);
					}
				}
				
				cxp_update_cache();
				
				return ['success' => true, 'msg' => '修改帐号成功'];
			}
		}
		
    }

    function delete(){
		$id = intval($this->request->get('id'));
		
		if($id){
			Db::transaction(function(){
				Db::table('user_perms')->where('userID', $id)->delete();
				
				Db::table('user_roles')->where('userID', $id)->delete();
				
				Db::table('user_profile')->where('user_id', $id)->delete();
				
				Db::table('users')->where('id', $id)->delete();					
			});

			
			return ['success' => true, 'msg' => '删除帐号成功'];
		}else{
		
		}
    }

    function edit_password(){
		if($this->request->isPost()){
			$validate = new Validate([
				'password' => 'require',
			]);
			$data = [
				'password' => trim(input('post.password')),
			];
			
			if(!$validate->check($data)){
				return ['success' => FALSE, 'msg' => validation_errors()];
			}else{
				$user_id = intval($this->request->post('user_id'));
				
				Db::table('users')->where('id', $user_id)->update([
					'password' => password_hash($data['password'], PASSWORD_BCRYPT)
 				]);
				
				return ['success' => true, 'msg' => '修改密码成功'];
			}
		}else{
			$user_id = intval($this->request->get('user_id'));
			$this->assign('user_id', $user_id);
			
			return view('edit_password');
		}
    }

    function set_perms(){
		if($this->request->isPost()){
			foreach (input('post.') as $k => $v)
			{
				if (substr($k,0,5) == "perm_")
				{
					$permID = str_replace("perm_","",$k);
					if ($v == 'x')
					{
						$strSQL = "DELETE FROM `user_perms` WHERE `userID` = ? AND `permID` = ?";
						Db::query($strSQL,array(input('post.user_id'),floatval($permID)));
					} else {
						$strSQL = "REPLACE INTO `user_perms` SET `userID` = ?, `permID` = ?, `value` = ?";
						Db::query($strSQL,array(input('post.user_id'),floatval($permID),$v));
					}
				}
			}
			cxp_update_cache();
			return array('success' => TRUE, 'msg' => '调整权限成功');			
		}else{
			$user_id = intval($this->request->get('user_id'));
			
			$info = Db::table('users')->where('id', $user_id)->find();
			
			if($info){
				$this->assign('info', $info);
				
				$this->assign('user_id', $user_id);
				
				$my_acl = new \cxpcms\member\Member_acl($user_id);
				
				$this->assign('my_acl', $my_acl);
				
				$rPerms = $my_acl->getPermArr();
				
				$this->assign('rPerms', $rPerms);
				
				return view('set_perms');
				
			}else{
			
			}
			
		}
    }

	function perm_data(){
		$permissions = new \app\admin\model\Permissions();
		$permissions = $permissions->select();
		$perm_parr = array();
		foreach($permissions as $row){
			$perm_parr[$row->parent_id][] = $row;
		}
	  
	    $user_id = intval($this->request->post('user_id'));
	 
		$my_acl=new \cxpcms\member\Member_acl($user_id);
		// $this->template_data['my_acl'] = $my_acl;
		$rPerms = $my_acl->getPermArr();
		// $this->template_data['rPerms'] = $rPerms;
		echo '[' . $this->loop_parent($perm_parr, 125, 0, 0, '', $rPerms) . ']';
	}
	
	function loop_parent($perm_parr, $parent_id, $curloop, $curid, $html, $rPerms){
		if(isset($perm_parr[$parent_id]) && count($perm_parr[$parent_id])>0){
		  
			  foreach($perm_parr[$parent_id] as $row){
				  
				$permKey = $row->permKey;
				$selhtml = '';
				$selhtml .= "<select name=\"perm_" . $row->id . "\">";
				$selhtml .= "<option value=\"1\"";
				if (isset($rPerms[$permKey]) && ($rPerms[$permKey]['value'] === '1' || $rPerms[$permKey]['value'] === true) && $rPerms[$permKey]['inheritted'] != true) { $selhtml .= " selected=\"selected\""; }
				$selhtml .= ">允许</option>";
				$selhtml .= "<option value=\"0\"";
				if(isset($rPerms[$permKey])){if ($rPerms[$permKey]['value'] === false && $rPerms[$permKey]['inheritted'] != true) { $selhtml .= " selected=\"selected\""; }}
				$selhtml .= ">拒绝</option>";
				$selhtml .= "<option value=\"x\"";
				$iVal = '';
				if(isset($rPerms[$permKey])){
					if ($rPerms[$permKey]['inheritted'] == true || !array_key_exists($permKey,$rPerms))
					{
						$selhtml .= " selected=\"selected\"";
						if ($rPerms[$permKey]['value'] === true )
						{
							$iVal = '(允许)';
						} else {
							$iVal = '(拒绝)';
						}
					}
				}else{
					$selhtml .= " selected=\"selected\"";
					$iVal = '(拒绝)';
				}
				$selhtml .= ">继承 $iVal</option>";
                $selhtml .= "</select>";
				  
				  if(isset($perm_parr[$row->id]) && count($perm_parr[$row->id])>0){
					$html .= "{id:" . $row->id . ",name:'" . $row->permName . "', select:'" . $selhtml . "', children:[";
					$html = $this->loop_parent($perm_parr, $row->id, $curloop + 1, $curid, $html, $rPerms) . ']},';
					
				  }else{
					  $html .= "{id:" . $row->id . ",name:'" . $row->permName . "', select:'" . $selhtml . "'},";
				  }
			  }								  
		}else{
			// $html .= ']},';
		}
		return $html;
	}
	// 设置用户状态
	function set_status(){
		$id = intval($this->request->post('id'));
		$tbfieldvalue = $this->request->post('tbfieldvalue');
		Db::table('users')->where('id', $id)->update([
			'status' => $tbfieldvalue
		]);
		
		echo 'success';
	}	
}