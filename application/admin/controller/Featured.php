<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/17
 * Time: 18:23
 */
namespace app\admin\controller;

use think\Controller;

class Featured extends Base
{
    protected $obj = null;
    protected $validate = null;

    public function _initialize()
    {
        $this->validate = validate('Featured');
        $this->obj = model('Featured');
    }

    /**
     * 推荐位列表页
     * @return mixed
     */
    public function index()
    {
        $types = config('featured.featured_type');
        $type = input('get.type',0,'intval');
        $featureds = $this->obj->getFeaturedsByType($type);

        return $this->fetch('',[
            'types' => $types,
            'featureds' => $featureds,
            'type' => $type,
        ]);
    }

    /**
     * 添加推荐位信息
     * @return mixed
     */
    public function add()
    {
        // 判断控制器访问方式
        if (request()->isPost())
        {
            $data = input('post.');
            // 校验数据
            if(!$this->validate->scene('add')->check($data))
            {
                $this->error($this->validate->getError());
            }
            // 判断是否更新
            if(!empty($data['id']))
            {
                return $this->update($data);
            }
            // 数据入库
            $id = $this->obj->add($data);
            if($id)
            {
                $this->success('数据添加成功');
            }
            else
            {
                $this->error('数据添加失败');
            }
        }
        else
        {
            $types = config('featured.featured_type');
            return $this->fetch('', [
                'types' => $types,
            ]);
        }
    }

    /**
     * 更新推荐位
     * @param $data
     */
    public function update($data)
    {
        $id = $this->obj->save($data,['id'=>intval($data['id'])]);
        if($id)
        {
           return $this->success('更新成功');
        }
        return $this->error('更新失败');
    }

    /**
     * 修改推荐位
     * @return mixed
     */
    public function edit()
    {
        $data = input('get.');
        if(!$this->validate->scene('edit')->check($data))
        {
            $this->error($this->validate->getError());
        }
        $featured = $this->obj->get($data['id']);
        $types = config('featured.featured_type');
        return $this->fetch('',[
           'featured' => $featured,
            'types' => $types,
        ]);
    }

}