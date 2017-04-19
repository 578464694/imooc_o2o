<?php

namespace app\bis\controller;
use think\Controller;
use think\Request;
use app\common\validate;
class Location extends Base
{
    protected $city = null;
    protected $category = null;
    protected $bisLocation = null;
    protected $validate = null;
    protected $bisId = 0;


    public function _initialize()
    {
        $this->city = model('City');
        $this->category = model('Category');
        $this->bisLocation = model('BisLocation');
        $this->bisId = $this->getLoginUser()->bis_id;
        $this->validate = validate('Bis');
    }

    /**
     * 显示门店列表
     * @return \think\Response
     */
    public function index()
    {

        $locations = $this->bisLocation->getLocationsByBisId($this->bisId);
//        print_r($locations);
//        exit();
        return $this->fetch('',[
            'locations' => $locations,
        ]);
    }

    /**
     * 门店添加或修改
     * @return mixed|void
     */
    public function add()
    {
        if(request()->isPost())
        {
            // 获取表单数据
            $data = input('post.');
            // 校验分店基本信息
            if(!$this->validate->scene('fendianadd')->check($data))
            {
                $this->error($this->validate->getError());
            }
            // 分店相关信息校验
            if(!$this->validate->scene('headOffice')->check($data))
            {
                $this->error($this->validate->getError());
            }

//         获取经纬度
            $lnglat = \Map::getLngLat($data['address']);
            if(empty($lnglat) || $lnglat['status'] != 0)
            {
                $this->error('无法获取数据，或者匹配的地址不精准 ');
            }

            $data['cat'] = '';
            if(!empty($data['se_category_id'])) {
                $data['cat'] = implode('|',$data['se_category_id']);
            }
            // 分店相关信息入库
            $locationData = [
                'name' => $data['name'],
                'logo' => $data['logo'],
                'api_address' => $data['address'],
                'tel' => $data['tel'],
                'contact' => $data['contact'],
                'xpoint' => empty($lnglat['result']['location']['lng'])?'':$lnglat['result']['location']['lng'],
                'ypoint' => empty($lnglat['result']['location']['lat'])?'':$lnglat['result']['location']['lat'],
                'bis_id' => $this->bisId,
                'open_time' => empty($data['open_time'])?'':$data['open_time'],
                'content' => empty($data['content'])?'':$data['content'],
                'category_id' => $data['category_id'],
                'category_path' => $data['category_id'] . ',' . $data['cat'],
                'city_path' => (!isset($data['se_city_id']))?$data['city_id']:$data['city_id'].','.$data['se_city_id'],
                'city_id' => $data['city_id'],
                'address' => $data['address'],
                'is_main' => 0, // 代表分店信息
            ];
            if(!empty($data['id']))
            {
                $this->update($locationData,$data['id']);
            }
            $locationId = $this->bisLocation->add($locationData);
            if($locationId)
            {
                return $this->success('门店申请成功',url('location/index'));
            }
            else
            {
                return $this->error('门店申请失败');
            }

            // 发送邮件
//            $url = request()->domain().url('bis/register/waiting',['id' => $bisId]);
//            $title = "o2o入驻申请通知";
//            $content = "您提交的入驻申请需等待平台方审核，您可以通过点击链接<a href='".$url."' target='_blank'>查看链接</a> 查看审核状态";
//            \phpmailer\Email::send($data['email'],$title,$content);

//            $this->success('申请成功',url('register/waiting',['id' => $bisId]));    // 在本模块下，所以只写url 即可
        }
        else
        {
            // 获取一级城市的数据
            $citys = $this->city->getNormalCitysByParentId();
            // 获取一级分类
            $categorys = $this->category->getNormalFirstCategory();

            return $this->fetch('', [
                'citys' => $citys,
                'categorys' => $categorys,
            ]);
        }

    }

    public function edit($id)
    {
        if(!$this->validate->scene('edit')->check(['id'=>$id]))
        {
            $this->error($this->validate->getError());
        }
        $data = [
            'id'=>$id,
            'bis_id'=>$this->bisId,
        ];
        $locationData = $this->bisLocation->getLocationsByData($data);
        if(!$locationData)
        {
            return $this->error('查询门店失败');
        }
        // $locationData = model('BisLocation')->get(['bis_id' => $id, 'is_main' => 1]);
        // 获取一级城市的数据
        $citys = $this->city->getNormalCitysByParentId();
        // 获取一级分类
        $categorys = $this->category->getNormalFirstCategory();
//        var_dump($locationData);
//        exit();
        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
            'locationData'=>$locationData,
        ]);
    }

    // 更新信息
    public function update($locataionData,$id)
    {
        $res = $this->bisLocation->save($locataionData,['id' => intval($id)]); //save 的重载，第一个参数为更新的数据，第二个参数为更新的条件
        if($res)
        {
            $this->success('门店更新成功',url('location/index'));
        }
        else
        {
            $this->error('门店更新失败');
        }
    }

    // 状态逻辑
    public function status()
    {
        $data = input('get.');
        parent::changeStatus($data,$this->validate,$this->bisLocation);

    }
}
