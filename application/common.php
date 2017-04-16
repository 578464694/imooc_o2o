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

// 商户入驻申请的文案
function bisRegister($status)
{
    $str = '';
    switch ($status)
    {
        case 0:
            $str = '待审核，审核后平台方会发送邮件通知，请关注邮件';
            break;
        case 1:
            $str = '入驻申请成功';
            break;
        case 2:
            $str = '非常抱歉，您提交的材料不符合条件，请重新提交';
            break;
        default:
            $str = '该申请已被删除';
            break;
    }
    return $str;
}

function pagination($obj)
{
    if(!$obj)
    {
        return '';
    }
    return '<div class="cl pd-5 bg-1 bk-gray mt-20 tp5-paginate">'.$obj->render().'</div>';
}

/**
 *  根据 city_path获取子城市名称
 * @param $path
 * @return string
 */
function getSeCityName($path)
{
    if(empty($path))
    {
        return '';
    }
    $cityId = 0;
    if(preg_match('/,/', $path))
    {
        $cityPath = explode(',',$path);
        $cityId = $cityPath[1];
    }else {
        $cityId = $path;
    }
    $city = model('City')->get($cityId);
    return $city->name;
}

/**
 *  根据 category_path 获得
 * @param $path
 * @return array|string
 */
function getCategoryPath($path)
{
    $categoryinfo = [
    ];

    if(empty($path))
    {
        return '';
    }
    $categoryPath = explode(',', $path);    //将 path 分割成数组
    array_shift($categoryPath);             //移除数组首元素
    if(sizeof($categoryPath) > 0)
    {
        $str = implode($categoryPath);                 // 将数组转换成字符串
        $categoryPath = explode('|', $str);      //按照 | 分割字符串为数组
    }

    foreach ($categoryPath as $value) {
        $category = model('Category')->get($value);
        $categoryinfo[] = $category->name;
    }

    return $categoryinfo;
}
?>
