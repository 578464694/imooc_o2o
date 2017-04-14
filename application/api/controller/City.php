<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class City extends Controller
{
    private $obj;
    private $validate;
    public function _initialize()
    {
        $this->obj = model('City');
        $this->validate = validate('City');
    }

    public function getCitysByParentId()
    {
        $id = input('post.id');
        if(!$this->validate->scene('query')->check($id)) {
            $this->error('ID不合法');
        }
        // 通过 ID 获取二级城市
        $citys = $this->obj->getNormalCitysByParentId($id);
        if(empty($citys) || !isset($citys)) {
            return show(0, 'error');
        }
        return show(1, 'success', $citys);
    }
}
