<?php
namespace app\bis\controller;

use think\Controller;

/**
 * 商户登陆模块
 * Class Login
 * @package app\bis\controller
 */
class Login extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}