<?php
namespace app\bis\controller;

use think\Controller;

/**
 * 商户注册模块
 * Class Register
 * @package app\bis\controller
 */
class Register extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}