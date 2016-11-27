<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 11:11
 */
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class Index extends Base{
	
	function __construct(){
		parent::__construct();
	}
	
    function index(){
        return view('index');
    }

    function dashboard(){
        return view('dashboard');
    }
}