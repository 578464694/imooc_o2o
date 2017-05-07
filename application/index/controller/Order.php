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
        if(!validate('Order')->check(input('get.')))
        {
            $this->error(validate('Order')->getError());
        }

        $id = input('get.id',0,'intval');

        $dealCount = input('get.deal_count',0,'intval');
        $totalPrice = input('get.total_price',0.0);
        $use_type = input('get.use_type',1,'intval');
        $address = "";
        $address = input('get.address','');

        if($use_type == 2) {
            $lnglat = \Map::getLngLat($address);
            if (empty($lnglat) || $lnglat['status'] != 0) {
                $this->error('无法获取数据，或者匹配的地址不精准 ');
            }
        }


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
            'use_type' => $use_type,
            'address' => $address,
            'bis_id' => $deal->bis_id,
        ];

        try{
            $orderId = model('Order')->add($data);
        }
        catch (\Exception $e)
        {
            $this->error($e->getMessage());
        }

        if($use_type == 1) {    // 堂食做伪支付
            $this->redirect('pay/index', ['order_id' => $orderId]);  // 跳转支付页面
        }

        if($use_type == 2)
        {
            $this->success('您的订单已提交，请等待卖家处理',url('index/index'));
        }
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