<?php
namespace app\index\validate;
use think\Validate;

/**
 * 用户验证
 * Class User
 * @package app\index\validate
 */
class User extends Validate
{
    protected $rule = [
        ['username','require|length:6,20|unique:user,username','请填写用户名|用户名长度在6-20|用户名已存在'],
//        'username' => ['unique:user,username'],
        ['password','require|length:6,20','请填写密码|密码长度在6-20'],
        ['email','require|email|unique:user','请填写邮箱|邮箱格式不正确|邮箱已存在'],
        ['mobile','require|regex:^1[34578]\d{9}$','请填写手机号|手机号格式不正确'],
        ['verifycode','require','请填写验证码'],
    ];

    protected $scene = [
        'register' => ['username','password','email','verifycode'],//注册验证

    ];


}