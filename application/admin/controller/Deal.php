<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/17
 * Time: 18:23
 */
namespace app\admin\controller;
use think\Controller;
class Deal extends Controller
{
    protected $deal = null;
    protected $city = null;
    protected $category = null;
    public function _initialize()
    {
        $this->city = model('City');
        $this->category = model('Category');
        $this->deal = model('Deal');
    }

    public function index()
    {
        $data = input('get.');     // 获得以 get 方式提交的数据
        $queryData = [];
        // 封装查询数据
        if(!empty($data['category_id'])) {
            $queryData = [
                'category_id' => $data['category_id']
            ];
        }
        if(!empty($data['city_id']))
        {
            $queryData = [
                'city_id' => $data['city_id'],
            ];
        }
        if(!empty($data['start_time']) && !empty($data['end_time']) && (strtotime($data['end_time']) > strtotime($data['start_time'])))
        {
            $queryData['create_time'] = [
                ['gt',strtotime($data['start_time'])],
                ['lt',strtotime($data['end_time'])],
            ];
        }
        if(!empty($data['name']))
        {
            $queryData['name'] = ['like','%'.$data['name'].'%'];
        }

        $deals = $this->deal->getNormalDeals($queryData);   // 根据查询条件获得团购商品

        $categorys = $this->category->getCategorysByParentId(); // 获得一级条目
        $citys = $this->city->getNormalCitys();                 // 获得城市

        $categoryArrs = []; //条目数组
        foreach ($categorys as $category)
        {
            $categoryArrs[$category->id] = $category->name;
        }

        $cityArrs = [];     //城市数组
        foreach ($citys as $city)
        {
            $cityArrs[$city->id] = $city->name;
        }
        return $this->fetch('',[
            'categorys' => $categorys,
            'citys' => $citys,
            'deals' => $deals,
            'name' => empty($data['name'])?'':$data['name'],
            'category_id' => empty($data['category_id'])?'':$data['category_id'],
            'city_id' => empty($data['city_id'])?'':$data['city_id'],
            'start_time'=>empty($data['start_time'])?'':$data['start_time'],
            'end_time'=>empty($data['end_time'])?'':$data['end_time'],
            'cityArrs' => $cityArrs,
            'categoryArrs' => $categoryArrs,
        ]);
    }

    /**
     *  商家团购商品申请
     * @return mixed
     */
    public function apply()
    {
        $dealData = $this->deal->getDealsByStatus();
        return $this->fetch('',[
           'dealData'=>$dealData,
        ]);
    }

    /**
     * 更新状态
     */
    public function status()
    {
        $data = input('get.');
        if(empty($data['status']) || empty($data['id']))
        {
            return $this->error('请求数据不能为空');
        }
        $dealData = [
            'status'=>$data['status'],
        ];
        $result = $this->deal->updateDealById($dealData,$data['id']);
        if($result)
        {
            return $this->success('状态更新成功');
        }
        return $this->success('状态更新失败');
    }

    /**
     *  修改
     * @return mixed
     */
    public function detail()
    {
        $data = input('get.');
        if(!$data)
        {
            $this->error('请求数据不能为空');
        }
        // 获取一级城市的数据
        $citys = $this->city->getNormalCitysByParentId();
        // 获取一级分类
        $categorys = $this->category->getNormalFirstCategory();
        // 获取团购商品信息
        $deal = model('Deal')->get($data['id']);
        if(!$deal)
        {
            $this->error('查找失败');
        }

        // 获得门店信息
        $location_ids = $deal['location_ids'];
        $selectlocations= explode(',',$location_ids);
        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
            'bislocations' => model('BisLocation')->getNormalLocationByBisId($deal->bis_id),
            'deal' => $deal,
            'selectlocations' => empty($selectlocations)?[]:$selectlocations,
        ]);
    }

}