<?php
namespace app\index\controller;
use think\Controller;
class Lists extends Base
{
    public function index()
    {
        $firstCatIds = []; // 一级分类ID
        $id = input('get.id', 0, 'intval');// 获取参数 id
        $categoryParentId = 0;
        // 获取一级条目
        $categorys = model('Category')->getCategorysByParentId();
        foreach ($categorys as $category)
        {
            $firstCatIds[] = $category->id;
        }
        $data = []; // 查询商品数据的条件
        // id = 0 一级分类 二级分类
        if(in_array($id, $firstCatIds)) // id 是一级分类
        {
            $categoryParentId = $id;
            $data['category_id'] = $id;
        }
        elseif($id) // 二级分类
        {
            $category = model('Category')->find($id);
            if(!$category || $category->status != 1)
            {
                $this->error('数据不合法');
            }
            $categoryParentId = $category->parent_id;
            $data['se_category_id'] = $id;
        }
        else
        {
            $categoryParentId = 0;
        }
        $sedCategorys = [];
        // 获取父类下的所有子类
        if($categoryParentId)
        {

            $sedCategorys = model('Category')->getCategorysByParentId($categoryParentId);
        }
        // 排序逻辑获取
        $orders = [];
        $order_price = input('get.order_price','');
        $order_time = input('get.order_time','');
        $order_sales = input('get.order_sales','');
        if(!empty($order_price))
        {
            $orderflag = 'order_price';
            $orders['order_price'] = $order_price;

        } elseif (!empty($order_sales))
        {
            $orderflag = 'order_sales';
            $orders['order_sales'] = $order_sales;
        } elseif (!empty($order_time))
        {
            $orderflag = 'order_time';
            $orders['order_time'] = $order_time;
        } else
        {
            $orderflag = '';
        }

        $deals = model('Deal')->getDealByConditions($data,$orders);

        return $this->fetch('',[
            'categorys' => $categorys,
            'sedCategorys' => $sedCategorys,
            'id' => $id, // 商品分类
            'categoryParentId' => $categoryParentId,
            'orderflag' => $orderflag,
            'deals' => $deals,
        ]);
    }

    /**
     * 根据商品名模糊查询商品
     * @return mixed
     * 王宇
     */
    public function find()
    {
        $name = input('get.name','');
        if(!$name) {
            $this->error('请输入查询条件');
        }
        // 获取城市 sesssion
        $city = session('city','','o2o');
        if(!$city) {
            $this->error('获取城市信息失败');
        }
        // 查询商品数据
        $data = [
            'name' => ['like','%'.$name.'%'],
            'se_city_id' => $city->id,
        ];
        // 查询商品
        $deals = model('Deal')->queryByDataPaginate($data);

        return $this->fetch('',[
           'deals' => $deals,
            'name' => $name, // 查询关键字
        ]);
    }
}