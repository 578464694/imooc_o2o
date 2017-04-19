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
        $result = $this->update($data, ['id' => $id]);
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

}
