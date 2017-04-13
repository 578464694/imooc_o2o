<?php
namespace app\common\model;

use think\Model;

class Category extends Model
{
//    protected $autoWriteTimestamp = true;   //自动维护 create_time ,update_time 时间戳
    public function add($data)
    {
        $data['status'] = 1;
        //$data['create_time'] = time();
        return $this->save($data);
    }

    /**获得分类一级条目
     *
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getNormalFirstCategory()
    {
        $data = [
            'status' => 1,
            'parent_id' => 0,
        ];

        $order = [
            'id' => 'desc',
        ];

        $result = $this->where($data)
            ->order($order)
            ->select();
         // 此处也有链接被重置的 bug,所以用 $result 中转
        return $result;
    }

    /** 获得一级栏目
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getFirstCategorys($parentId = 0)
    {
        //        echo "parentId".$parentId;
        $var = $parentId;   //此处有一个bug，当不对$parentId操作时，页面会卡住
        $data = [
            'status' => ['neq', -1],    //status（状态）,不等于 -1
            'parent_id' => $parentId            // parent_id 等于 0
        ];

        $order = [
            'listorder' => 'desc',
            'id' => 'desc',
        ];

        $result = $this->where($data)
            ->order($order)
            ->paginate();     //分页获取，默认为15
        //        echo $this->getLastSql();   //TP5 中输出 sql语句的方法
        return $result;
    }
}