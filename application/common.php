<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function status($status)
{
    if($status == 1)
    {
        return "<span class='label label-success radius'>正常</span>";
    }
    if($status == 0)
    {
        return "<span class='label label-default radius'>待审</span>";
    }
    return "<span class='label label-danger radius'>删除</span>";
    //class='label label-danger radius 为u2 自带样式
}

/**
 * 通过cURL 获取数据
 * @param $url
 * @param int $type 0 get 1 post
 * @param array $data
 */
function doCurl($url, $type=0, $data=[])
{
    $curl = curl_init(); //初始化 curl
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER,0); //执行时不返回头部
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //执行后不直接打印

    if($type == 1)
    {   //post方式
        curl_setopt($curl, CURLOPT_POST,0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    // 执行并获取内容
    $output = curl_exec($curl);
    // 释放 curl句柄
    curl_close($curl);
    return $output;
}
?>
