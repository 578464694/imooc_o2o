<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\api\validate;

class Category extends Controller
{
    private $obj;
    public function _initialize()
    {
        $this->obj = model('Category');
    }

    /**
     *  获取品类，并返回 json 对象
     * @param int $parentId
     * @return array
     */
    public function getCategorysByParentId()
    {
        $id = input('post.id', 0, 'intval');
        if(!$id)
        {
            $this->error('ID不合法');
        }
        $catgegorys = $this->obj->getCategorysByParentId($id);
        if(empty($catgegorys) || !isset($catgegorys))
        {
            return show(0,'error');
        }
        return show(1, 'success', $catgegorys);
    }
}
