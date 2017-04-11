<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch(); //默认返回view/index/ 下面的index.html
    }
}
