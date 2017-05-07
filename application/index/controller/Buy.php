<?php
namespace app\index\controller;

use think\Controller;

class Buy extends Base
{
    protected $user = null;

    public function _initialize()
    {
        $this->user = $this->getLoginUser();
        if (!$this->user || !$this->user->id) {
            $this->error('请登录', url('user/login'));
        }
        $this->assign('user',$this->user);
    }

    public function index()
    {
        return $this->fetch('', [

        ]);
    }

    public function coupons()
    {
        $coupons = model('Coupons')->where(['user_id' => $this->user->id])->select();
        foreach ($coupons as $coupon)
        {
            $deal = model('Deal')->where(['id' => $coupon->deal_id])->find();
            $coupon->deal_name = $deal->name;
        }
        return $this->fetch('',[
            'coupons' => $coupons,
        ]);
    }

    public function waimai()
    {
        $orders = model('Order')->where(['user_id' => $this->user->id,'use_type' => 2])->select();
        foreach ($orders as $order)
        {
            $deal = model('Deal')->where(['id' => $order->deal_id])->find();
            $order->deal_name = $deal->name;
        }
        return $this->fetch('',[
            'waimai' => $orders
        ]);
    }
}