<?php
namespace app\admin\controller;
use think\Controller;
class Bis extends Controller
{
    protected $obj = null;

    /**
     * TP5 默认的初始化方法 _initialize
     */
    public function _initialize()
    {
        $this->obj = model('Bis'); // 将 model('Category') 抽离出来
    }

    /**
     *  入驻申请列表
     * @return mixed
     */
    public function apply()
    {
        $bis = $this->obj->getBisByStatus();
        return $this->fetch('',[
            'bis' => $bis,
        ]);
    }

    public function detail()
    {
        $id = input('get.id');
        if(empty($id))
        {
            return $this->error('ID错误');
        }
        // 获取一级城市的数据
        $citys = model('City')->getNormalCitysByParentId();
        // 获取一级分类
        $categorys = model('Category')->getNormalFirstCategory();
        // 获取商户基本信息
        $bisData = model('Bis')->get($id);
        // 获取总店信息
//        $locationData = model('BisLocation')->where(['is_main'=>1,'bis_id' => $id])->select();
        $locationData = model('BisLocation')->get(['bis_id'=>$id,'is_main'=>1]);
        // 获取总账号信息
//        $accountData = model('BisAccount')->where(['bis_id'=>$id,'is_main'=>1])->select();
        $accountData = model('BisAccount')->get(['bis_id'=>$id,'is_main'=>1]);
//        print_r("id".$id);
//        var_dump($locationData);
//        exit();
        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
            'bisData' => $bisData,
            'locationData' => $locationData,
            'accountData' => $accountData,
        ]);
    }


}
