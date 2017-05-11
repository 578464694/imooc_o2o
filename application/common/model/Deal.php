<?php
namespace app\common\model;
use think\Db;

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

    /**
     * 根据指定条件获得 deal
     * @param array $data
     * @param $orders
     * @return \think\Paginator
     */
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
        $result = $this->where(implode(' AND ',$datas))->order($order)->paginate(10);
        return $result;
    }

    /**
     * 通过 deal 的 id 获得门店信息
     * @param $id
     * @return array
     */
    public function getLocationInfoByDealId($id)
    {
       $ids = $this->where(['id' => $id])->column('location_ids');
       if(!$ids)
       {
           return [];
       }

       $location_ids = explode(',',$ids[0]);
       $locationInfos = [];
       foreach ($location_ids as $location_id)
       {
           $location_id = intval($location_id);
           $locationInfo = model('BisLocation')->find(['id'=>$location_id]);

           $locationInfos[] = [
               'name' => $locationInfo['name'],
               'address' => $locationInfo->address,
               'tel' => $locationInfo->tel,
               'open_time' => $locationInfo->open_time,
           ];
       }
       return $locationInfos;
    }

    /**
     * 获得门店
     * @param int $id
     * @return array|int
     */
    public function getBisId($id = 0)
    {
        if(!$id)
        {
            return 0;
        }
        $bis_id = $this->where(['id' => $id])->column('bis_id');
        return $bis_id;
    }

    /**
     * 获得商品剩余数量
     * @param $id
     * @return array
     */
    public function getTotalCountById($id)
    {
        $result = $this->field('buy_count,total_count')->find($id);
        return $result;
    }

    // 通过事务方式更新剩余数量
//    public function updateDealCount($id, $count)
//    {
//        Db::startTrans();
//        try{
//            $deal = $this->find($id);
//            $remainCount = $deal->total_count - $deal->buy_count;
//            if($remainCount < $count)
//            Db::table('think_user')->find(1);
//            Db::table('think_user')->delete(1);
//            // 提交事务
//            Db::commit();
//        } catch (\Exception $e) {
//            // 回滚事务
//            Db::rollback();
//        }
//
//    }
    /**
     * 更新商品购买数量
     * @param $id
     * @param $buy_count
     * @return bool
     */
    public function updateBuyCount($id,$buy_count)
    {
        $result = $this->where('id',$id)->setInc('buy_count',$buy_count);
        return $result;
    }

}
