<?php
namespace app\index\controller;
class Order extends Base
{
    public function index()
    {
        $user = $this->getLoginUser();
        if(!$user) // 登陆校验
        {
            $this->error('请先登录',url('user/login'));
        }
        if(!validate('Order')->check(input('get.'))) //订单数据校验
        {
            $this->error(validate('Order')->getError());
        }

        $use_type = input('get.use_type',1,'intval');
        $address = "";
        $address = input('get.address','');

        if($use_type == 2) {    // 如果是外卖，判断地址是否准确
            $lnglat = \Map::getLngLat($address);
            if (empty($lnglat) || $lnglat['status'] != 0) {
                $this->error('无法获取数据，或者匹配的地址不精准 ');
            }
        }

        $deal_id = input('get.id',0,'intval');
        $dealCount = input('get.deal_count',0,'intval');
        $deal = model('Deal')->find($deal_id); // 获取团购商品数据
        if(!$deal || $deal->status != 1) // 判断商品 id 是否合法
        {
            $this->error('商品不存在');
        }

        if(empty($_SERVER['HTTP_REFERER'])) // 判断请求来源
        {
            $this->error('请求不合法');
        }
        // 判断商品剩余数量是否满足订单数量
        if(!$this->checkBuyCount($deal_id, $dealCount))
        {
            $this->error('商品剩余数量不足，请重新选择数量');
        }

        // 计算订单价格
        $totalPrice = $dealCount * $deal->current_price;

        $orderSn = setOrderSn(); // 生成订单编号
        // 组装入库数据
        $data = [
            'out_trade_no' => $orderSn,
            'user_id' => $user->id,
            'username' => $user->username,
            'deal_id' => $deal_id,
            'deal_count' => $dealCount,
            'total_price' => $totalPrice,
            'referer' => $_SERVER['HTTP_REFERER'],
            'use_type' => $use_type,
            'address' => $address,
            'bis_id' => $deal->bis_id,
        ];

        try{
            $orderId = model('Order')->add($data); // 生成订单
        }
        catch (\Exception $e)
        {
            $this->error($e->getMessage());
        }

        if($use_type == 1) {    // 堂食做伪支付
            $this->redirect('pay/index', ['order_id' => $orderId]);  // 跳转支付页面 ,完成伪支付后，修改商品的 buy_count
        }

        if($use_type == 2) // 外卖 // 修改商品剩余数量
        {
            try {
                $result = model('Deal')->updateBuyCount($deal_id, $dealCount);
            } catch (\Exception $e)
            {
                $this->error($e->getMessage());
            }
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

        $deal_id = input('get.id',0,'intval');
        if(!$deal_id)
        {
            $this->error('参数不合法');
        }

        $deal = model('Deal')->find($deal_id);
        if(!$deal || $deal->status != 1)
        {
            $this->error('商品不存在');
        }

        $count = input('get.count',1,'intval');
        if($count > 100 || $count < 0)
        {
            $this->error('购买数量超限额');
        }

        if(empty($_SERVER['HTTP_REFERER'])) // 判断请求来源
        {
            $this->error('请求不合法');
        }

        if(!$this->checkBuyCount($deal_id, $count))
        {
            $this->error('商品剩余数量不足，请重新选择数量');
        }


        $deal = $deal->toArray();

        return $this->fetch('',[
            'controller' => 'pay',
            'deal' => $deal,
            'count' => $count,
        ]);
    }

    /**
     *  根据 deal_id 校验商品数量剩余数量是否足够
     * @param $deal_id
     * @param $deal_count
     * @return bool
     */
    protected function checkBuyCount($deal_id, $deal_count)
    {
        $countArr = model('Deal')->getTotalCountById($deal_id); // countArr key buy_count total_count
        if(($countArr['total_count'] - $countArr['buy_count']) < $deal_count)
        {
            return false;
        }
        return true;
    }
}