<?php
namespace app\bis\controller;

use think\Controller;

/**
 * 商户注册模块
 * Class Register
 * @package app\bis\controller
 */
class Register extends Controller
{
    private $city;
    private $category;

    public function _initialize()
    {
        $this->city = model('City');
        $this->category = model('Category');
    }

    public function index()
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