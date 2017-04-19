<?php

namespace app\common\model;

use think\Model;

class BisAccount extends BaseModel
{
    /**
     *  通过id 更新 bisAccount表
     * @param $data
     * @param $id
     * @return false|int
     */
    public function updateById($data,$id)
    {
        // allowField 过滤data数组中 非数据表中的数据
        return $this->allowField(true)->save($data,['id'=>$id]);
    }


}
