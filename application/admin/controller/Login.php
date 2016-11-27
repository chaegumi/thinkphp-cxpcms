<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 11:40
 */
namespace app\admin\controller;

use think\Controller;
use think\Session;


class Login extends Controller{
    
	function __construct(){
		parent::__construct();
		
		Session::set('session0', '000');
	}
	
	
	function index(){
		
		
		if($this->request->isPost()){
			$securimage = new \Securimage();
			if ($securimage->check(input('post.captcha_code')) === false) {
				return array('success' => FALSE, 'msg' => '错误的验证码');
			}else{
				$username = trim(input('post.username'));
				$password = trim(input('post.password'));
				
				if($user = \cxpcms\member\Member_current_user::login($username, $password)){
					
					
					return array('success' => TRUE, 'msg' => '登录成功');
					
				}else{
					return array('success' => FALSE, 'msg' => '登录失败1');
				}
				
			}
		}else{
			return view('login');
		}
    }

    function findpassword(){
        return view('findpassword');
    }
	
	function status(){
		if(Session::get('member_userid') > 0){
			echo input('get.callback') . '(' . json_encode(array('success' => TRUE, 'status' => 1)) . ')';
		}else{
			echo input('get.callback') . '(' . json_encode(array('success' => TRUE, 'status' => 0)) . ')';
		}
	}
}