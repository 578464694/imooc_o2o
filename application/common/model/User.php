<?php
namespace app\common\model;

use think\Exception;
use think\Model;

class User extends BaseModel
{
//    protected $pk = 'id';
    public function add($data = [])
    {

        // 校验数据是否为数组
        if(!is_array($data))
        {
            exception('传递的数据不是数组');
        }
        $data['status'] = 1;   // 状态为待审
        return $this->data($data)
                    ->allowField(true) // 过滤数据表中没有的字段
                    ->save();   // 返回主键 id
    }

    /**
     *  通过用户名查找用户
     * @param $username
     * @return array|false|\PDOStatement|string|Model
     */
    public function getUserByUserName($username)
    {
        if(!$username)
        {
            exception('用户名不合法');
        }
        $data = ['username' => $username];
        $result = $this->where($data)->find();
        return $result;
    }

}
