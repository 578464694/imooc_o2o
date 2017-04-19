<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/16
 * Time: 16:02
 */
namespace app\bis\controller;
use think\Controller;

class Base extends Controller
{
    protected $account;
    public function _initialize()
    {
        // 判断用户是否登陆
        $isLogin = $this->isLogin();
        if(!$isLogin)
        {
            return $this->redirect(url('login/index'));
        }
    }

    /**
     *  通过session 判断用户是否登陆
     * @return bool
     */
    public function isLogin()
    {
        $user = $this->getLoginUser();
        if($user && $user->id)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *  获取用户登陆 session
     *  session 中保存登陆对象
     * @return mixed
     */
    public function getLoginUser()
    {
        if(!$this->account)
        {
            $this->account = session('bisAccount', '', 'bis');
        }
        return $this->account;
    }

    /**
     *  状态逻辑
     * @param $data 表单中数据
     * @param $validate 校验对象
     * @param $model 模型
     */
    public function changeStatus($data,$validate,$model)
    {
        if (!$validate->scene('status')->check($data)) {
            $this->error($validate->getError());
        }
        if($data['status'] == 1)
        {
            return $this->error('无权更改');
        }
        $res = $model->save(['status' => $data['status']],['id' => intval($data['id'])]);
        if($res) {
            $this->success('状态更新成功');
        }
        else {
            $this->error('状态更新失败');
        }
    }

}