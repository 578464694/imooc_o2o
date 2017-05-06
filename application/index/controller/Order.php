<?php
namespace app\index\controller;
class Order extends Base
{
    public function index()
    {
        $user = $this->getLoginUser();
        if(!$user)
        {
            $this->error('请先登录',url('user/login'));
        }
        $id = input('get.id',0,'intval');
        if(!$id)
        {
            $this->error('参数不合法');
        }
        $dealCount = input('get.deal_count',0,'intval');
        $totalPrice = input('get.total_price',0,'decimal');

        $deal = model('Deal')->find($id);
        if(!$deal || $deal->status != 1)
        {
            $this->error('商品不存在');
        }

        if(empty($_SERVER['HTTP_REFERER']))
        {
            $this->error('请求不合法');
        }

        $orderSn = setOrderSn();
        // 组装入库数据
        $data = [
            'out_trade_no' => $orderSn,
            'user_id' => $user->id,
            'username' => $user->username,
            'deal_id' => $id,
            'deal_count' => $dealCount,
            'total_price' => $totalPrice,
            'referer' => $_SERVER['HTTP_REFERER'],
        ];

        try{
            $orderId = model('Order')->add($data);
        }
        catch (\Exception $e)
        {
            $this->error($e->getMessage());
        }

        $this->redirect('pay/index',['order_id' => $orderId]);  // 跳转支付页面

    }

    public function confirm()
    {
        // 登陆校验
        $user = $this->getLoginUser();
        if(!$user)
        {
            $this->error('请先登录',url('user/login'));
        }
        $id = input('get.id',0,'intval');
        if(!$id)
        {
            $this->error('参数不合法');
        }
        $count = input('get.count',1,'intval');
        if($count > 100 || $count < 0)
        {
            $this->error('购买数量不合法');
        }
        $deal = model('Deal')->find($id);

        if(!$deal || $deal->status != 1)
        {
            $this->error('商品不存在');
        }
        $deal = $deal->toArray();

        return $this->fetch('',[
            'controller' => 'pay',
            'deal' => $deal,
            'count' => $count,
        ]);
    }
}