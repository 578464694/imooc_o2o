<?php
namespace app\index\controller;
class Pay extends Base
{
    public function index()
    {
        $user = $this->getLoginUser();
        if(!$user || !$user->id)
        {
            $this->error('未登录');
        }
        $order_id = input('get.order_id',0,'intval');
        if(!$order_id)
        {
            $this->error("订单错误");
        }

        $order = model('Order')->find($order_id);
        if($order->user_id != $user->id)
        {
            $this->error('不是你的订单');
        }
        $coupons = [
            'sn' => $order->out_trade_no,
            'password' => mt_rand(10000, 99999),
            'user_id' => $user->id,
            'deal_id' => $order->deal_id,
            'order_id' => $order->id,
        ];
        try {
            $pay = model('Coupons')->add($coupons);
        } catch (\Exception $e) {
            $this->error('订单处理失败');
        }
        // 发送邮件
        // TODO
        $this->success('订单处理成功',url('index/index'));
    }
}