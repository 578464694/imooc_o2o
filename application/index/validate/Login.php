<?php
namespace app\index\validate;
use think\Validate;

class Login extends Validate
{
    protected $rule = [
        ['username','require|length:6,20','请填写用户名|用户名长度在6-20'],
        ['password','require|length:6,20','请填写密码|密码长度在6-20'],
    ];

    protected $scene = [
        'login' => ['username','password'],
    ];
}