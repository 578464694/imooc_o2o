<?php
namespace app\bis\validate;

use think\Validate;

class Waimai extends Validate
{
    protected $rule = [
        ['send_type', 'require|in:0,1,2,3', '外卖状态不能为空|外卖状态不合法'],
        ['id', 'require|number', 'ID不能为空|ID不合法']
    ];

    protected $scene = [
        'sendType' => ['send_type', 'id'],
        'index' => ['send_type'],
    ];
}