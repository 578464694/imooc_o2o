<?php
namespace app\admin\controller;
use think\Controller;

/**
 * 商户登陆模块
 * Class Login
 * @package app\bis\controller
 */
class Login extends Base
{
    protected $validate = null;
    public function _initialize()
    {
        $this->validate = validate('Admin');
    }

    public function index()
    {
        if(request()->isPost())
        {
            $data = input('post.');
            if(!$this->validate->scene('login')->check($data))
            {
                $this->error($this->getError());
            }
            $account = model('Admin')->get(['username'=>$data['username'], 'status' => 1]);   // 获得用户
            if(!$account)
            {
                $this->error('用户不存在');
            }
            if(!$account->password == $data['password'])    // 校验密码
            {
                $this->error('密码错误');
            }
            model('Admin')->save(['last_login_time'=>time()],['id' => $account->id]);   // 更新登陆时间
            session('adminAccount',$account,'admin');  // 保存session
            return $this->success('登陆成功',$this->redirect(url('index/index')));
        }
        else {
            $adminAccount = session('adminAccount','','admin'); //获得管理员 session
            if($adminAccount && $adminAccount->id)
            {
                return $this->redirect(url('index/index'));
            }
            return $this->fetch();
        }
    }

    public function logout()
    {
        session(null, 'admin'); // 清空 session
        return $this->redirect(url('login/index'));
    }
}