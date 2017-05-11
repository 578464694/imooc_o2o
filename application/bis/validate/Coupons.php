<?php
namespace app\bis\validate;
use think\Validate;

class Coupons extends Validate
{
    protected $rule = [
        ['id','require|number','ID不能为空|ID应为数字'],
        ['status','in:0,1,2,3','状态不合法'],
        ['coupons_id','require|regex:\d{19}','请填写优惠券号|优惠券号格式不正确'],
    ];

    protected $scene = [
        'status' => ['id', 'status'],
        'queryCoupons' => ['coupons_id'],
    ];
}