<?php
namespace app\module\job;
use think\queue\Job;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/6
 * Time: 17:07
 */
class JobEmail
{
    /**
     * 以队列的方式发送邮件
     * @param Job $job
     * @param $data
     * @return bool
     */
    public function fire(Job $job,$data)
    {
        $user = model('User')->find($data['user_id']); // 获取用户数据
        $coupons = model('Coupons')->where('id',$data['coupons_id'])->find(); // 获取优惠券数据

        $deal = model('Deal')->find($coupons['deal_id']); // 获取商品数据
        $bis = model('Bis')->find($deal['bis_id']);        // 获取商户
        $order = model('Order')->find($coupons['order_id']); //获取订单
        // 构造发送邮件信息
        $email = $user->email;
        $title = '您收到一张 o2o 优惠券';
        $content = "";
        $info = $deal->getLocationInfoByDealId($deal->id); // 构造商户信息
        $info = $this->formatLocationInfo($info);
        $content .= '消费门店：'.$info;
        $content .= '<br>您的优惠券号：'.$coupons->sn;
        $content .= '<br>使用密码：'.$coupons->password;
        $content .= '<br>人数：'.$order['deal_count'];
        $content .= '<br>请勿泄露优惠券号和密码';

        $isJobDone = \phpmailer\Email::send($email, $title, $content);

        if($isJobDone) {
            $job->delete(); //成功执行，删除任务

            return true;

        } else {
                if($job->attempts() > 3) {
                    print("<warn>邮件已尝试发送三次 3 次"."</warn>\n");
                    $job->delete();
                    return false;
                }

        }
    }


    /**
     * 将门店信息数组处理成字符串
     * @param $infos
     * @param string $content
     * @return string
     */
    public function formatLocationInfo($infos,$content = "")
    {
        foreach ($infos as $info)
        {
            if(!$info)
            {
                $content .= "";
                break;
            }
            $content .= '<br>--------------------------';
            $content .= '<br>门店名称：'.$info['name'];
            $content .= '<br>门店地址：'.$info['address'];
            $content .= '<br>门店电话：'.$info['tel'];
            $content .= '<br>营业时间：'.$info['open_time'];
            $content .= '<br>--------------------------';
        }
        return $content;
    }
}