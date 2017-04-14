<?php
namespace app\admin\controller;

use think\Controller;
use \Map;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch(); //返回 view/index/index.html
    }

    public function test()
    {
        return 'wangxiaoming';
    }

    public function welcome()
    {
//        if (\phpmailer\Email::send('578464694@qq.com', '早上好', '我不是机器人'))
//        {
//            return '发送邮件成功';
//        } else
//        {
//            return '发送邮件失败';
//        }
        
    }

    public function map()
    {
        return Map::getStaticImage('河北工业大学城市学院廊坊分校');
    }

}
