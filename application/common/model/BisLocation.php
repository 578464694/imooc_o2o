<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/15
 * Time: 15:05
 */
namespace app\common\model;

use think\model;

class BisLocation extends BaseModel
{
    /**
     * 根据bis_id 分页获得门店信息
     * @param $bisId
     */
    public function getLocationsByBisId($bisId)
    {
        $order = [
            'id' => 'asc'
        ];
        $data = [
            'bis_id' => $bisId,
        ];
        $locations = $this->where($data)
            ->order($order)
            ->paginate();
        return $locations;
    }

    /**
     *  根据条件查询门店
     * @param $data
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getLocationsByData($data)
    {
        $location = $this->get($data);
        if (!$location) {
            return '';
        }
        return $location;
    }

    /**
     *  根据 bis_id 获得门店信息
     * @param $bisId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getNormalLocationByBisId($bisId)
    {
        $data = [
            'bis_id' => $bisId,
            'status' => 1,
        ];
        $result = $this->where($data)
            ->order('id', 'desc')
            ->select();
        return $result;
    }

    /**
     * 根据状态查询门店
     * @param int $status
     * @return \think\Paginator
     */
    public function getLocationsByStatus($status = 0)
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
     * 根据条件查询门店
     * @param array $data
     * @return \think\Paginator
     */
    public function getLocationsByStatusAndBisId($data)
    {
        $order = [
            'id' => 'desc',
        ];
        $result = $this->where($data)
            ->order($order)
            ->paginate();
        return $result;
    }

}