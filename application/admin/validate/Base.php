<?php
namespace app\admin\validate;
use think\Validate;

class Base extends Validate
{
    protected $rule = [
        ['status','require|in:-1,1','状态参数为空|状态不合法']
    ];

    protected $scene = [
        'status' => ['status']
    ];
}
