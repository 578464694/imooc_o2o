<?php
namespace app\admin\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch(); //返回 view/index/index.html
    }

    public function test(){
    	return 'wangxiaoming';
    }

    public function welcome()
    {
        return "欢迎来到主后台模块";
    }

}
