<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/19
 * Time: 14:21
 */

namespace app\admin\controller;

use \phpmailer;
use think\Controller;
use think\Exception;
class Test extends Controller
{
    public $id = 0;
    public function getId()
    {
        echo $this->id;
    }

    public function testEmail()
    {
        $to = '578464694@qq.com';
        $title = '给王小明';
        $content = '明天下午到我办公室来一趟';
        $result = \phpmailer\Email::send($to, $title, $content);
    }
}


