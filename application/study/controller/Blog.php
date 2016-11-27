<?php
/**
 * Created by PhpStorm.
 * User: Chaegumi
 * Date: 2016/11/25
 * Time: 11:56
 */

namespace app\study\controller;

class Blog{

    function index(){

    }

    function read($id){

    }

    function archive($year, $month){
        return 'year=' . $year . '&month=' . $month;
    }
}