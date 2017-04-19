<?php

namespace app\bis\controller;

use think\Controller;
use think\Request;
use app\common\validate;

class Deal extends Base
{
    protected $city = null;
    protected $category = null;
    protected $validate = null;

    public function _initialize()
    {
        $this->city = model('City');
        $this->category = model('Category');
        $this->validate = validate('Deal');
    }

    /**
     * 团购商品列表 待完成
     * @return \think\Response
     */
    public function index()
    {
        $dealData = model('Deal')->where(
                                        ['bis_id'=>$this->getLoginUser()->bis_id,
                                        'status' => 1])
                                ->paginate();
        return $this->fetch('',[
            'dealData' => $dealData,
        ]);
    }

    /**
     *  团购商品添加
     * @return mixed
     */
    public function add()
    {
        //获得 bis_id
        $bisId = $this->getLoginUser()->bis_id;
        if (request()->isPost()) {
            $data = input('post.');
            //严格的数据校验
            if(!$this->validate->scene('addDeal')->check($data))
            {
                $this->error($this->validate->getError());
            }
            // 日期前后的验证
            $start_time = $data['start_time'];
            $end_time = $data['end_time'];
            $coupons_begin_time = $data['coupons_begin_time'];
            $coupons_end_time = $data['coupons_end_time'];
            if(!$this->validate->isBeforeTime($start_time,$end_time))
            {
                $this->error('开始时间应在结束时间之前');
            }
            if(!$this->validate->isBeforeTime($coupons_begin_time,$coupons_end_time))
            {
                $this->error('团购券开始时间应在结束时间之前');
            }
            if(!isset($data['location_ids'][0]) || empty($data['location_ids'][0]))
            {
                $this->error('未选择门店');
            }
            $location = model('BisLocation')->get($data['location_ids'][0]);    // 获取第一个门店的地址

            // 数据入库
            $dealData = [
                'name' => $data['name'],
                'city_id' => $data['city_id'],
                'city_path' => (!isset($data['se_city_id']))?$data['city_id']:$data['city_id'].','.$data['se_city_id'],
                'category_id' => $data['category_id'],
                'category_path' => empty($data['se_category_id'])?'':implode(',',$data['se_category_id']) ,
                'bis_id' => $bisId,
                'location_ids' =>  !isset($data['location_ids'])?'':implode(',',$data['location_ids']) ,
                'image' => $data['image'],
                'description' => !isset($data['description'])?'':$data['description'],
                'notes' =>!isset($data['notes'])?'':$data['notes'],
                'start_time' => strtotime($data['start_time']),
                'end_time' => strtotime($data['end_time']),
                'origin_price' => $data['origin_price'],
                'current_price' => $data['current_price'],
                'total_count' => $data['total_count'],
                'coupons_begin_time' => strtotime($data['coupons_begin_time']),
                'coupons_end_time' => strtotime($data['coupons_end_time']),
                'bis_account_id' => $this->getLoginUser()->id,
                'xpoint' => $location->xpoint,
                'ypoint' => $location->ypoint,
            ];
            $id = model('Deal')->add($dealData);
            // 判断更新或添加是否成功
            if(!$id)
            {
                return $this->error('操作失败');
            }
            // 更新成功
            if(!empty($data['id']))
            {

                return $this->success('修改成功');
            }
            // 添加成功
            return $this->success('添加成功',url('deal/index'));


        }
        else
        {
            // 获取一级城市的数据
            $citys = $this->city->getNormalCitysByParentId();
            // 获取一级分类
            $categorys = $this->category->getNormalFirstCategory();
            return $this->fetch('', [
                'citys' => $citys,
                'categorys' => $categorys,
                'bislocations' => model('BisLocation')->getNormalLocationByBisId($bisId),
            ]);
        }
    }

    /**
     *  修改
     * @return mixed
     */
    public function edit()
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
