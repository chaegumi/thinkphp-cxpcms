<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 11:38
 */
namespace app\admin\controller;

use think\Controller;

class Logout extends Controller{
    function index(){
		\cxpcms\member\Member_current_user::logout();
		$this->redirect('admin/login/index');
    }
}