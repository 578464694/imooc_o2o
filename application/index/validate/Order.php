<?php
namespace app\index\validate;
use think\Validate;

class Order extends Validate
{
    protected $rule = [
        ['use_type','require|in:1,2','请选择类型|类型不合法'],
        ['id','require|number','ID不能为空|ID不合法'],
    ];
}