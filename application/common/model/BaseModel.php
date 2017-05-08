<?php
/**
 *  basemodel 公共的model层
 */
namespace app\common\model;

use think\Model;

class BaseModel extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     *  添加数据,默认状态为 0
     * @param $data
     * @return mixed
     */
    public function add($data)
    {
        $data['status'] = 0;   // 状态为待审
        $this->save($data);
        return $this->id;   // 返回主键 id
    }



    /**
     *  根据 id 更新数据
     * @param $data
     * @param $id
     * @return false|int
     */
    public function updateById($data,$id)
    {
        $result = $this->allowField(true)->save($data,['id' => $id]);
        return $result;
    }

}
