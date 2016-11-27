<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 11:36
 */
namespace app\admin\controller;

use think\Db;
use think\Validate;

class Profile extends Base{
    function index(){
		
		if($this->request->isPost()){
			
			$title = trim(input('post.title'));
			$photo = trim(input('post.photo'));
			
			$gender = trim(input('post.gender'));
			
			$display_name = trim(input('post.display_name'));
			
			
			Db::table('users')->where('id', $this->user['id'])->update([
				'display_name'=> $display_name
			]);
			
			$row = Db::table('user_profile')->where('user_id', $this->user['id'])->find();
			
			if($row){
			
				Db::table('user_profile')->where('user_id', $this->user['id'])->update([
					'title' => $title,
					'photo' => $photo,
					'gender' => $gender
				]);
			}else{
				Db::table('user_profile')->insert([
					'user_id' => $this->user['id'],
					'title' => $title,
					'photo' => $photo,
					'gender' => $gender
				]);
			}
			
			
			return array('success' => TRUE, 'msg' => '更新个人资料成功');
		}else{
		
			return view('index');
		}
    }
	
	function update_password(){
		$validate = new Validate([
			'password' => 'require',
		]);
		
		$data = [
			'password' => input('post.password')
		];
		
		if(!$validate->check($data)){
			return array('success' => FALSE, 'msg' => validation_errors());
		}else{
			$user_id = $this->user['id'];
			
			Db::table('users')->where('id', $user_id)->update([
				'password' => password_hash($data['password'], PASSWORD_BCRYPT),
			]);
			
			return array('success' => TRUE, 'msg' => '修改密码成功');
		}
	}
	
	function login_log(){
		$response = new \stdClass;
		$response->draw = $this->request->post('draw');
		
		$ccount = Db::table('login_log')->alias('A')->where('A.user_id', $this->user['id'])->count('id');
		$response->recordsTotal = $ccount;
		
		
		$results = Db::table('login_log')->alias('A')->where('A.user_id', $this->user['id'])->order('A.id desc')->limit($this->request->post('start'), $this->request->post('length'))->select();
		
		$response->recordsFiltered = $response->recordsTotal;
		
		$response->data = array();
		foreach($results as $row){
			$data = array();
			// $data['id'] = $row->id;
			$data['login_time'] = $row['login_time'];
			$data['login_ip'] = $row['login_ip'];
			$data['login_area'] = $row['login_area'];
			$data['content'] = $row['content'];
			$data['login_type'] = $row['login_type'];
			$response->data[] = $data;
		}
		
		return $response;	
	}
	
	function operation_log(){
		$response = new \stdClass;
		$response->draw = $this->request->post('draw');
		
		$ccount = Db::table('operation_log')->alias('A')->where('A.user_id', $this->user['id'])->count('id');
		
		$response->recordsTotal = $ccount;
		
	
		$results = Db::table('operation_log')->alias('A')->where('A.user_id', $this->user['id'])->order('A.id desc')->limit($this->request->post('start'), $this->request->post('length'))->select();
		
		$response->recordsFiltered = $response->recordsTotal;
		
		$response->data = array();
		foreach($results as $row){
			$data = array();
			// $data['id'] = $row->id;
			$data['operation_time'] = $row['operation_time'];
			$data['operation_ip'] = $row['operation_ip'];
			$data['operation_area'] = $row['operation_area'];
			$data['operation_content'] = $row['operation_content'];
			$response->data[] = $data;
		}
		
		return $response;		
	}
	
	function bind(){
		$response = new \stdClass;
		$response->draw = $this->request->post('draw');
		
		$ccount = Db::table('users_oauth_account')->alias('A')->where('A.user_id', $this->user['id'])->count();
		$response->recordsTotal = $ccount;
		
		
		$results = Db::table('users_oauth_account')->alias('A')->where('A.user_id', $this->user['id'])->limit($this->request->post('start'), $this->request->post('length'))->select();
		
		$response->recordsFiltered = $response->recordsTotal;
		
		$response->data = array();
		
		foreach($results as $row){
			$data = array();
			$data['user_type'] = $row['user_type'];
			$data['rel_nickname'] = $row['rel_nickname'];
			$data['rel_user_id'] = $row['rel_user_id'];
			$response->data[] = $data;
		}
		
		return $response;		
	}
	
	function unbind(){
		
	}
}