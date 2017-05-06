<?php
namespace app\common\model;

use think\Model;

class Category extends BaseModel
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
        return $result;
    }

    /**
     *  根据品类中父类id 获得子类
     * @param int $parentId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getCategorysByParentId($parentId = 0)
    {
        $data = [
            'parent_id' => $parentId,
            'status' => 1,
        ];
        $order = [
            'id' => 'desc',
        ];

        $result = $this->where($data)
                        ->order($order)
                        ->select();
        return $result;

    }

    /**
     * 通过 parent_id 获得推荐商品条目
     * @param int $id
     * @param int $limit
     */
    public function getNormalRecommendCategoryByParentId($id,$limit)
    {
        $data = [
            'parent_id' => $id,
            'status' => 1,
        ];

        $order = [
            'listorder' => 'desc',
            'id' => 'desc'
        ];
        $this->where($data)->order($order);
        if(!empty($limit)) {
            $this->limit($limit);
        }
        $result = $this->select();
        return $result;
    }

    /**
     *  根据 parent_id 获得 二级分类
     * @param $parentIds
     */
    public function getNormalCategoryIdByParentId($parentIds)
    {
        $data = [
            'parent_id' => ['in',implode(',',$parentIds)],
            'status' => 1,
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc',
        ];
        $result = $this->where($data)->order($order)->select();
        return $result;
    }
}