<?php
namespace app\common\model;


class Deal extends BaseModel
{
    /**
     *  通过状态获得团购信息
     * @param int $status
     * @return \think\Paginator
     */
    public function getDealsByStatus($status = 0)
    {
        $order = [
            'id' => 'desc',
        ];
        $data = [
            'status' => $status,
        ];

        $result = $this->where($data)
            ->order($order)
            ->paginate();
        return $result;
    }

    /**
     *  通过id 更新deal
     * @param $data
     * @param $id
     * @return $this
     */
    public function updateDealById($data, $id)
    {
        $result = $this->save($data, ['id' => $id]);
        return $result;
    }

    /**
     * 通过条件获得团购商品
     * @param array $data 查询条件
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getNormalDeals($data = [])
    {
        $order = [
            'id' => 'desc',
        ];
        $data['status'] = 1;
        $deals = $this->where($data)
            ->order($order)
            ->paginate();
        foreach ($deals as $deal)
        {
            $deal->se_city_id = getSeCitys($deal->city_path)->id;
        }

        return $deals;
    }

    /**
     * 根据分类以及城市 获得商品数据
     * @param $category_id 分类id
     * @param $city_id  城市id
     * @param int $limit 限制条数
     */
    public function getNormalDealByCategoryCityId($category_id, $se_city_id, $limit = 10)
    {
        $data = [
            'end_time' => ['gt',time()],
            'category_id' => $category_id,
            'se_city_id' => $se_city_id,
            'status' => 1,
        ];



        $order = [
            'listorder' => 'desc',
            'id' => 'desc',
        ];

        $this->where($data)
            ->order($order);
        if($limit)
        {
            $this->limit($limit);
        }
        $result = $this->select();
        return $result;
    }

    public function getDealByConditions($data = [],$orders)
    {

        if(isset($orders['order_time']))
        {
            $order['create_time'] = 'desc';
        }
        if(isset($orders['order_price']))
        {
            $order['current_price'] = 'desc';
        }
        if(isset($orders['order_sales']))
        {
            $order['buy_count'] = 'desc';
        }
        $order['id'] ='desc';
        $datas[] = "end_time > ".time();
        if(!empty($data['se_category_id']))
        {
            $datas[] = " find_in_set(".$data['se_category_id'].', category_path)';
        }
        if(!empty($data['category_id']))
        {
            $datas[] = "category_id = ".$data['category_id'];
        }
        if(!empty($data['city_id']))
        {
            $datas[] = "city_id = ".$data['city_id'];
        }
        $datas[] = "status=1";
        $result = $this->where(implode(' AND ',$datas))->order($order)->paginate(2);
        return $result;
    }

}
