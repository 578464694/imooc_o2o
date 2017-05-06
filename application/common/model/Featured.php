<?php
namespace app\common\model;


class Featured extends BaseModel
{
    /**
     * 根据类型查询推荐位信息
     * @param $type
     * @return \think\Paginator
     */
    public function getFeaturedsByType($type)
    {
        $data = [
            'type' => $type,
            'status' => ['neq',-1], // 状态不等于 -1
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc',
        ];
        $result = $this->where($data)
                    ->order($order)
                    ->paginate();
        return $result;
    }

    public function getFeaturedsByTypeAndStatus($type,$status = 1)
    {
        $data = [
            'type' => $type,
            'status' => $status,
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc',
        ];
        $result = $this->where($data)
            ->order($order)
            ->paginate();
        return $result;
    }
}
