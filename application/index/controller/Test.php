<?php

namespace app\index\controller;
use think\Controller;

class Test extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(request()->isPost())
        {
            $data = input('post.');
            if(!validate('Test')->scene('add')->check($data))
            {
                $this->error(validate('Test')->getError());
            }
            else
            {
                $this->success('测试成功');
            }
        }
        else
        {
            return $this->fetch();
        }
    }

    //    protected $pk = 'id';
    public function testDealId()
    {
        $locationsInfos = model('Deal')->getLocationNameByDealId(15);
    }

}
