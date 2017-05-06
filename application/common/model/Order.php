<?php
namespace app\common\model;

use think\Exception;
use think\Model;

class Order extends BaseModel
{
    protected $autoWriteTimestamp = true;

    /**
     * 添加订单
     * @param $data
     * @return mixed
     */
    public function add($data)
    {
        $data['status'] = 1;
        $this->save($data);
        return $this->id;
    }
}
