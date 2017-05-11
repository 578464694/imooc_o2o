<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class User extends Base
{
    protected $obj = null;
    protected $validate = null;

    /**
     * TP5 默认的初始化方法 _initialize
     */
    public function _initialize()
    {
        $this->obj = model('User');
        $this->validate = validate('User');
    }

    /**
     * 获得正常用户
     * @return mixed
     */
    public function index()
    {
        $status = input('get.status',0,'intval'); //获得状态
        $checkStatus = [
            'status' => $status
        ];
        if(!$this->validate->scene('status')->check($checkStatus)) // 校验状态
        {
            $this->error($this->validate->getError());
        }

        $users = $this->obj->getUserByStatus($status); // 根据状态获得用户

        return $this->fetch('',[
            'users' => $users,
        ]);
    }

    /**
     * 获得删除用户
     * @return mixed
     */
    public function delete()
    {
        $users = $this->obj->getUserByStatus(-1); // 获得删除用户
        return $this->fetch('',[
            'users' => $users,
        ]);
    }
}
