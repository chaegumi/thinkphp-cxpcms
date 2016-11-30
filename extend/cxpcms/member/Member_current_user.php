<?php 
/**
 * Member_Current_User
 *
 * @author		Chaegumi
 * @copyright	Copyright (c) 2013 chaegumi
 * @email		chaegumi@qq.com
 * @filesource
 */
namespace cxpcms\member;

use think\Session;
use think\Db;
use think\Request;


class Member_Current_User{

	private static $user;
	
	private static $ci;
	
	function __construct(){
		
	}
	
	public static function user(){
		if(Session::get('member_userid')){
			
			$user1 = Db::table('users')->alias('U')->join('user_profile P', 'U.id=P.user_id')->where('U.id', Session::get('member_userid'))->find();
			if($user1){
				$my_acl=new \cxpcms\member\Member_acl($user1['id']);
				// 用户角色
				$userRoles = $my_acl->getUserRoles();
				$user1['userRoles'] = $userRoles;
				// 用户权限
				$userPerms = $my_acl->getPermArr('mini');
				$user1['userPerms'] = $userPerms;
				return $user1;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}
	
	public static function login($username, $password){
		$u = Db::table('users')->where('username', $username)->whereOr('email', $username)->find();

		if($u){
			if($u['status']){
				if(password_verify($password, $u['password'])){	
					$ip = Request::instance()->ip();
					$data = array(
						'cur_login_time' => date('Y-m-d H:i:s'),
						'cur_login_ip' => $ip,
						'cur_login_area' => '', // convert ip to area
						'last_login_ip' => $u['cur_login_ip'],
						'last_login_area' => $u['cur_login_area'],
						'last_login_time' => $u['cur_login_time'],
						'login_times' => ($u['login_times'] + 1)
					);
					Db::table('users')->where('id', $u['id'])->update($data);
					// dump($u);
					Session::set('member_userid', $u['id']);
					Session::set('IsAuthorized', TRUE);
					Session::set('member_companyid', $u['company_id']);
					
					
					// session_write_close();
					self::$user = $u;
					
					// return TRUE;
					return $u;
				}else{
					Session::flash('perr', 'Error Password');
					// session_write_close();
					return FALSE;
				}				
			}else{
				Session::flash('perr', 'User Status Disable');
				// session_write_close();
				return FALSE;
			}
		}else{
			Session::flash('perr', 'User Do not Exist');
			// session_write_close();
			return FALSE;
		}
	}
	
	public static function logout(){
		Session::delete('member_userid');
		Session::flush();
		session_unset();
		session_destroy();
		return TRUE;
	}
	
	public static function hasPermission($permKey){
		$member_acl = new \cxpcms\member\Member_acl();
		return $ember_acl->hasPermission($permKey);
	}
	
	public function __clone(){
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}

}
// end file