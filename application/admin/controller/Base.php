<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 11:41
 */
namespace app\admin\controller;

use think\Controller;
use think\Session;

class Base extends Controller{
	
	protected $template_data = array();
	protected $user, $siteinfo, $site_id;
	protected $userSites = array();
	protected $node_contents_table_num = 10;
	
	function __construct(){
		parent::__construct();
		$Member_Current_User = new \cxpcms\member\Member_current_user();
		$this->user = $Member_Current_User::user();
		if($this->user){
			// 登录用户信息
			$this->assign('user', $this->user);
			
		}else{
			// 跳转到登录页面
			$this->redirect('admin/login/index');
		}
	}
	
}