<?php
namespace app\index\controller;

use think\Controller;


class Index extends Base
{
    public function index()
    {
        // 获取首页大图相关数据
        $big_pictures = model('Featured')->getFeaturedsByTypeAndStatus(0);
        // 获取广告位相关数据
        $advert = model('Featured')->getFeaturedsByTypeAndStatus(1);

        // 商品分类 美食数据
        $cate_data = model('Deal')->getNormalDealByCategoryCityId(1,$this->city->id);
        // 获取 4个 子分类
        $cate_cats = model('Category')->getNormalRecommendCategoryByParentId(1,4);

        return $this->fetch('',[
            'big_pictures' => $big_pictures,
            'advert' => $advert,
            'cate_cats' => $cate_cats,
            'cate_data' => $cate_data,
        ]); //默认返回view/index/ 下面的index.html
    }

}
