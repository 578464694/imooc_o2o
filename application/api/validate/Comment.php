<?php
namespace app\api\validate;
use think\Validate;

class Comment extends Validate
{
    protected $rule = [
        ['content','require|length:1,500','请填写用户名|评论长度在1-500'],
        ['comment_class','require|in:0,1,2','请选择总体评价|总体评价不合法'],
        ['deal_id','require|number','餐品ID不能为空|餐品ID不合法'],
    ];

    protected $scene = [
        'comment' => ['content','comment_class'],
        'comment_power' => ['deal_id'],
    ];
}