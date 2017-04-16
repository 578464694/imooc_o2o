<?php
/**
 *  basemodel 公共的model层
 */
namespace app\common\model;

use think\Model;

class BaseModel extends Model
{
    protected $autoWriteTimestamp = true;
    public function add($data)
    {
        $data['status'] = 0;   // 状态为待审
        $this->save($data);
        return $this->id;   // 返回主键 id
    }
}
