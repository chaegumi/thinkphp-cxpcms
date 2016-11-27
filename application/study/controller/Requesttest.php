<?php
/**
 * Created by PhpStorm.
 * User: Chaegumi
 * Date: 2016/11/25
 * Time: 11:09
 */
namespace app\study\controller;

use think\Controller;
use think\Request;

class Requesttest extends Controller{
    function index(){
        // $request = Request::instance();
        $request = $this->request;

        echo 'domain：' . $request->domain() . '<br/>';

        echo 'file:' . $request->baseFile() . '<br/>';

        echo 'url:' . $request->url() . '<br/>';

        echo 'url with domain:' . $request->url(true) . '<br/>';

        echo 'url without domain:' . $request->baseUrl() . '<br/>';

        echo 'root:' . $request->root() . '<br/>';

        echo 'root with domain:' . $request->root(true) . '<br/>';

        echo 'pathinfo:' . $request->pathinfo() . '<br/>';

        echo 'pathinfo:' . $request->path() . '<br/>';

        echo 'ext:' . $request->ext() . '<br/>';

        echo '当前模块名称是' . $request->module() . '<br/>';
        echo '当前控制器名称是' . $request->controller() . '<br/>';
        echo '当前操作名称是' . $request->action() . '<br/>';

        echo '请求方法：' . $request->method() . '<br/>';
        echo '资源类型：' . $request->type() . '<br/>';

        echo '访问IP' . $request->ip() . '<br/>';
        echo '是否Ajax请求' . var_export($request->isAjax(), true) . '<br/>';

        echo '请求参数';
        dump($request->param());
        echo '请求参数：仅包含name';
        dump($request->only(['name']));
        echo '请求参数：排除name';
        dump($request->except(['name']));

        echo '路由信息';
        dump($request->route());
        echo '调度信息';
        dump($request->dispatch());

        dump($request->has('id', 'get'));

        dump(input('?get.id'));

        dump($request->isGet());

        dump($request->isPost());

        dump($request->isPut());

        dump($request->isDelete());

        dump($request->isAjax());

        dump($request->isPjax());

        dump($request->isMobile());

        dump($request->isHead());

        dump($request->isPatch());

        dump($request->isOptions());

        dump($request->isCli());

        dump($request->isCgi());

        $info = $request->header();
        dump($info);

    }
}