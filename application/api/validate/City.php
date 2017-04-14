<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/14
 * Time: 15:43
 */

namespace app\api\validate;


use think\Validate;

class City extends Validate
{
    protected $rule = [
        'id' => ['number|>=:1','ID不合法']  //id 为数字,且大于等于1
    ];

    protected $scene = [
        'query'=> ['id'],
    ];

}