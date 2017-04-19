<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/17
 * Time: 18:23
 */
namespace app\admin\controller;
use think\Controller;
class Featured extends Controller
{
    protected $obj = null;
    public function _initialize()
    {
        $this->obj = model('Featured');
    }


    public function index()
    {
        return $this->fetch();
    }
    public function add()
    {
        $types = config('featured.featured_type');
        return $this->fetch('',[
            'types' => $types,
        ]);

    }

}