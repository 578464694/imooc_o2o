<?php
namespace app\index\controller;

use think\Controller;

class User extends Controller
{
    public function login()
    {
        return $this->fetch(); //对应view/user/login.html
    }

    public function register()
    {
        return $this->fetch();  //对应 view/user/register.html
    }
}
