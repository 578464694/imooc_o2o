<?php

namespace app\common\model;

use think\Model;

class City extends Model
{
    /**
     * 获取一级城市
     * @param int $parentId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getNormalCitysByParentId($parentId = 0)
    {
        $data = [
            'status' => 1,
            'parent_id' => $parentId,
        ];
        $order = [
            'id' => 'desc'
        ];
        $result = $this->where($data)
                    ->order($order)
                    ->select();
        return $result;
    }
}
