<?php
namespace app\admin\controller;
use think\Controller;
class Base extends Controller
{
    protected $account = null;
    public function _initialize()
    {
        // 判断管理员是否登陆
        $isLogin = $this->isLogin();
        if(!$isLogin)
        {
            return $this->redirect(url('login/index'));
        }
        $this->assign('user',$this->getLoginUser());
    }

    /**
     * 更新状态
     * 王宇
     */
    public function status()
    {
        $data = input('get.');
        if(empty($data['id']))
        {
            $this->error('ID 不合法');
        }
        if(!is_numeric($data['status']))
        {
            $this->error('状态不合法');
        }
        // 获取控制器
        $model = request()->controller();
        $res = model($model)->save(['status' => $data['status']],['id' => intval($data['id'])]);
        if($res) {
            $this->success('状态更新成功');
        }
        else {
            $this->error('状态更新失败');
        }
    }

    /**
     * 获得登陆管理员
     * @return mixed|null
     */
    public function getLoginUser()
    {
        if(!$this->account)
        {
            $this->account = session('adminAccount','','admin');
        }
        return $this->account;
    }

    /**
     * 是否登陆
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
     * ajax 方式进行排序
     * @return array
     */
    public function listorder()
    {

        if(!request()->isPost())
        {
            $this->error('请求错误');
        }
        $id = input('post.id');
        $listorder = input('post.listorder',0,'intval');
        $checkData = [
            'id' => $id,
            'listorder' => $listorder,
        ];

        // 数据校验
        if(empty($id))
        {
            $this->error('ID 错误');
        }
        $model = request()->controller();
        $res = model($model)->save(['listorder' => $listorder],['id'=>intval($id)]);
        if($res)
        {
            return show(1,'success',url($model."/index"));
        }
        else
        {
            return show(0,'error',url($model."/index"));
        }
    }

}
