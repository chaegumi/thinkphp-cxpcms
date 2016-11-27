<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 11:41
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Validate;

class Calendar extends Base{
	
	function __construct(){
		parent::__construct();
	}
	
    function index(){
		return view('index');
    }
	
	function data(){
		$start = input('get.start');
		$end = input('get.end');
		if(!$start || !$end){
			return array('success' => FALSE, 'msg' => '非法参数');
		}
		
		
		$results = Db::table('events')->where('start', '>=', $start)->where('end', '<=', $end)->where('user_id', $this->user['id'])->select();
		
		
		$events = array();
		foreach($results as $row){
			if($row['allDay']){
				$row['allDay'] = TRUE;
			}else{
				$row['allDay'] = FALSE;
			}
			$events[] = $row;
		}
		
		return $events;		
	}
	
	function add(){
		
		$info = [
			'id' => 0,
			'title' => '',
			'start' => input('get.start'),
			'end' => input('get.end'),
			'url' => '',
			'backgroundColor' => '#3c8dbc',
			'borderColor' => '#3c8dbc',
			'allDay' => 0
		];
		$this->assign('info', $info);
		
		return view('events_edit');
	}
	
	function edit(){
		
		$info = Db::table('events')->where('id', input('get.id'))->find();
		
		$this->assign('info', $info);
		
		return view('events_edit');
	}
	
	function save(){
		$id = intval($this->request->post('id'));
		
		$validate = new Validate([
			'title' => 'require',
		]);
		
		$data = [
			'user_id' =>$this->user['id'],
			'title' => trim($this->request->post('title')),
			'start' => $this->request->post('start'),
			'end' => $this->request->post('end'),
			'backgroundColor' => trim($this->request->post('backgroundColor')),
			'borderColor' => trim($this->request->post('backgroundColor')),
			'allDay' => $this->request->post('allDay'),
			'addtime' => $_SERVER['REQUEST_TIME']
		];
		
		if(!$validate->check($data)){
			return ['success' => false, 'msg' => $validate->getError()];
		}else{
			if($id === 0){
				Db::table('events')->insert($data);
				
				return ['success' => true, 'msg' => '添加事件成功'];
			}else{
				
				Db::table('events')->where('id', $id)->where('user_id', $this->user['id'])->update($data);
				
				return ['success'=> true, 'msg' => '修改事件成功'];
				
			}
		}
	}
	
	function delete(){
		$id = intval($this->request->get('id'));
		
		Db::table('events')->where('id', $id)->where('user_id', $this->user['id'])->delete();
		
		return ['success' => true, 'msg' => '删除事件成功'];
	}
}