<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/16
 * Time: 10:02
 */
namespace app\admin\validate;
use think\Validate;

/**
 *  商户信息校验
 * Class Bis
 * @package app\admin\validate
 */
class Admin extends Validate
{
    protected $rule = [
        ['id','require|number','名称表示不存在|名称标识须为数字'],
        ['status','number|in:-1,0,1,2','状态必须为数字|状态范围不合法'], //-1代表删除，0代表待审，1代表通过，2代表不通过
        ['username','require','请填写用户名'],
        ['password','require','请填写密码']
    ];

    protected $scene = [
        'login' => ['username','password'],
    ];
}