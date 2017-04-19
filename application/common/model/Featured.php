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
