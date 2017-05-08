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

    /**
     * 验证用户是否能够评论
     * @param $deal_id
     * @param $user_id
     * @return array|false|\PDOStatement|string|Model
     */
    public function isComment($deal_id,$user_id = 0)
    {
        //Db::table('think_user')
        //->where('id > :id AND name LIKE :name ',['id'=>0, 'name'=>'thinkphp%'])
        //->select();

       $result = $this->where('user_id', $user_id)
                        ->where('deal_id',$deal_id)
                        ->where('is_comment',0)
                        ->find();
       return $result;
    }
}
