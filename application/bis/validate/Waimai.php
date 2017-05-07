<?php
namespace app\bis\validate;
use think\Validate;

class Waimai extends Validate
{
    protected $rule = [
        ['send_type','require|in:0,1,2,3','外卖状态不能为空|外卖状态不合法']
    ];
}