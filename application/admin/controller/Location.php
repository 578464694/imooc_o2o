<?php
namespace app\admin\controller;
use think\Controller;

class Location extends Controller
{
    protected $location = null;
    protected $validate = null;
    protected $city = null;
    protected $category = null;

    public function _initialize()
    {
        $this->location = model('BisLocation');
        $this->validate = validate('Bis');
        $this->city = model('City');
        $this->category = model('Category');
    }

    /**
     *  正常门店列表
     * @return mixed
     */
    public function index()
    {
        $locations = $this->location->getLocationsByStatus(1);
        return $this->fetch('',[
            'locations' => $locations,
        ]);
    }

    /**
     * 待审门店列表
     * @return mixed
     */
    public function apply()
    {
        // 查询待审的门店
        $locations = $this->location->getLocationsByStatus();
        return $this->fetch('',[
            'locations' => $locations,
        ]);
    }

    // 状态逻辑
    //-1代表删除，1代表通过，2代表不通过
    public function status()
    {
        $data = input('get.');
        if (!$this->validate->scene('status')->check($data)) {
            $this->error($this->validate->getError());
        }
        $bis = $this->location->save(['status' => $data['status']], ['id' => intval($data['id'])]);   // 状态修改
        if(!$bis)
        {
            return $this->error('状态修改失败');
        }
        else
        {
            return $this->success('状态修改成功');
        }
    }

    /**
     * 门店详情
     * @param $id
     * @return mixed|void
     */
    public function detail($id)
    {
        $data = [
            'id'=>$id,
        ];
        $locationData = $this->location->getLocationsByData($data);
        if(!$locationData)
        {
            return $this->error('查询门店失败');
        }
        // 获取一级城市的数据
        $citys = $this->city->getNormalCitysByParentId();
        // 获取一级分类
        $categorys = $this->category->getNormalFirstCategory();
        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
            'locationData'=>$locationData,
        ]);
    }
}