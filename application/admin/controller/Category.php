<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Category extends Controller
{
    protected $obj = null;
    protected $validate = null;

    /**
     * TP5 默认的初始化方法 _initialize
     */
    public function _initialize()
    {
        $this->obj = model('Category'); // 将 model('Category') 抽离出来
        $this->validate = validate('Category');
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $parentId = input('get.parent_id',0,'intval');//parent_id 默认为0，整数
        // 查询一级栏目
        $categorys = $this->obj->getFirstCategorys($parentId);
        //返回 index.html 视图，并绑定数据 categorys
        return $this->fetch('',[
            'categorys' => $categorys,
        ]);
    }

    /**返回 add.html 视图
     * @return mixed
     */
    public function add()
    {

        $categorys = $this->obj->getNormalFirstCategory();
        return $this->fetch('',[    //$this->fetch 第一参数默认为 add这个方法模板（即add.html）
            'categorys' => $categorys, //第二参数绑定到模板中的变量
        ]);
    }

    /**
     * 添加分类
     */
    public function save()
    {
        //print_r($_POST);
        // print_r(input('post.'));
        // print_r(request()->post());
        /**
         * 做下严格校验
         */
        if(!request()->isPost()) {
            $this->error('请求错误');
        }

        $data = input('post.');

        if (!$this->validate->scene('add')->check($data)) {
            $this->error($this->validate->getError());
        }
        // 如果 id 不为空，则进行更新操作
        if(!empty($data['id']))
        {
            return $this->update($data);
        }
        $res = $this->obj->add($data);
        if($res)
        {
            $this->success("添加分类成功");
        }
        else
        {
            $this->error("添加分类失败");
        }
    }
    //获取页面传递的数据
//    public function edit()
//    {
//        $id = input("get.id", 0, 'intval');
//
//    }
    /**编辑页面
     * @param int $id
     * @return mixed
     */
    public function edit($id = 0)
    {
        // 数据校验
        if(intval($id) < 1)
        {
            $this->error("分类标识不合法");
        }
        $category = $this->obj->get($id);   //get 方法继承自 model，根据 id查找，必须掌握
        $categorys = $categorys = $this->obj->getNormalFirstCategory(); // 获得一级条目
//        $this->fetch('',[
//           'category' => $category,
//            'categorys' => $categorys,
//        ]);
        return $this->fetch('',[
            'categorys' => $categorys,
            'category' => $category,
        ]);
    }
    // 更新信息
    public function update($data)
    {
        $res = $this->obj->save($data,['id' => intval($data['id'])]); //save 的重载，第一个参数为更新的数据，第二个参数为更新的条件
        if($res)
        {
            $this->success('更新成功');
        }
        else
        {
            $this->error('更新失败');
        }
    }

    // 排序逻辑
    public function listorder($id,$listorder)
    {
        if(!request()->isPost())
        {
            $this->error('请求错误');
        }
        $checkData = [
            'id' => $id,
            'listorder' => $listorder,
        ];
        // 数据校验
        if(!$this->validate->scene('listorder')->check($checkData))
        {
            $this->error($this->validate->getError());
        }
        $res = $this->obj->save(['listorder' => $listorder],['id'=>intval($id)]);
        if($res)
        {
            $this->result($_SERVER['HTTP_REFERER'], 1, 'success'); //向 js返回 json数据  1 代表成功
        }
        else
        {
            $this->result($_SERVER['HTTP_REFERER'], 0, '更新失败'); //$_SERVER['HTTP_REFERER']以得到链接/提交当前页的父页面URL.0 表示失败
        }
    }

    // 状态逻辑
    public function status()
    {
        $data = input('get.');
        if (!$this->validate->scene('status')->check($data)) {
            $this->error($this->validate->getError());
        }
        $res = $this->obj->save(['status' => $data['status']],['id' => intval($data['id'])]);
        if($res) {
            $this->success('状态更新成功');
        }
        else {
            $this->error('状态更新失败');
        }

    }
}
