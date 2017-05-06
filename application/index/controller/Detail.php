<?php
namespace app\index\controller;
use think\Controller;
class Detail extends Base
{
    public function index($id)
    {
        // 数据校验
        if(!intval($id))
        {
            $this->error('ID 不合法');
        }
        // 根据 id 查询商品的数据
        $deal = model('Deal')->get($id);
        // 获取分类信息
        $category = model('Category')->get($deal->category_id);
        // 获取分店信息
        $locations = model('BisLocation')->getNormalLocationsInId($deal->location_ids);
        //获取商家信息
        $bis_id = $deal->bis_id;
        $bis = model('Bis')->get($bis_id);
        $bis_description = $bis->description;   // 商家介绍

        // 判断团购是否开始
        $flag = 0;
        if($deal->start_time > time()) // 此时展现 团购开始时间
        {
            $flag = 1;              //flag = 1，表示团购未开始。= 0，表示开始
        }
        //设置距离开始的时间
        $day = $hour = $minitue = $second = 0;
        if($flag == 1)
        {
            $timedata = '';
            $sub_time = $deal->start_time - time(); // 时间差
            $day = floor( $sub_time / (3600 * 24));
            if($day)
            {
                $timedata .= $day . "天 ";
            }
            $hour =floor (($sub_time %(3600 * 24)) / 3600);
            if($hour)
            {
                $timedata .= $hour . "小时";
            }
            $minitue = floor($sub_time %(3600 * 24 )%3600 / 60);
            if($minitue)
            {
                $timedata .= $minitue . "分钟";
            }
            $this->assign('timedata',$timedata);
        }
        return $this->fetch('',[
            'title' => $deal->name,
            'deal' => $deal,
            'category' => $category,
            'locations' => $locations,
            'flag' => $flag,
            'mapstr' => $locations[0]->xpoint.','.$locations[0]->ypoint,
            'bis_description' => $bis_description,
        ]);
    }
}