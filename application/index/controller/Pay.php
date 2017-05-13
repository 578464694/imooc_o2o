<?php
namespace app\index\controller;
use think\Exception;
use think\Queue;

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
            'deal_count' => $order->deal_count,
            'status' => 0,
        ];
        try {
            $coupon_id = model('Coupons')->add($coupons);                         // 添加优惠券

            model('Order')->where('id', $order_id)->update(['pay_status' => 1]);    // 修改支付状态
            model('Deal')->where('id', $order->deal_id)// 修改商品购买数量
            ->inc('buy_count', $order->deal_count)
                ->update();

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        // 发送邮件
        // TODO
        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
       $jobHandlerClassName = 'app\module\job\JobEmail';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
//        $jobQueueName = "helloJobQueue";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        $jobData = ['user_id' => $user->id, 'coupons_id' => $coupon_id];  // 发送邮件的数据

        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        $isPushed = Queue::push($jobHandlerClassName, $jobData);

        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if ($isPushed !== false) {
            model('Coupons')->where('id',$coupon_id)->update(['status'=>1]);   // 更新状态

            $this->success('成功发送邮件，请查收',url('index/index'));
        } else {
            $this->error('发送邮件失败',url('index/index'));
        }

    }
}