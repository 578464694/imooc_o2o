<?php
namespace app\index\controller;

use think\Controller;
use think\Exception;
use think\Request;

class User extends Base
{
    protected $request = null;
    protected $validate = null;
    protected $obj = null;
    protected $account = null;
    public function _initialize()
    {
        $this->request = Request::instance();
        $this->validate = validate('User');
        $this->obj = model('User');
        $this->account = $this->getLoginUser();
    }

    public function login()
    {
        // 获取 session
        $user = session('o2o_user','','o2o');
        if($user && $user->id)
        {
            $this->redirect(url('index/index')); // 已经登陆直接跳转
        }
        return $this->fetch(); //对应view/user/login.html
    }

    public function register()
    {
        if (request()->isPost()) {
            $data = input('post.');
            // 数据校验
            if (!$this->validate->scene('register')->check($data)) {
                $this->error($this->validate->getError());
            }
            if (!captcha_check($data['verifycode'])) {
                $this->error('验证码不正确');
            }
            if ($data['password'] != $data['repassword']) {
                $this->error('两次输入密码不一样');
            }
            $data['code'] = mt_rand(100, 10000); //生成 加盐字符串
            $data['password'] = md5($data['password'] . $data['code']); //进行 md5 加密
            // 数据入库
            try {
                $res = model('User')->add($data);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            if ($res) {
                $this->success('注册成功',url('user/login'));
            } else {
                $this->error('注册失败');
            }

        } else {
            return $this->fetch();  //对应 view/user/register.html
        }
    }

    /**
     * 登陆验证
     */
    public function logincheck()
    {
        if(!request()->isPost())
        {
            $this->error('提交不合法');
        }

        $data = input('post.');
        if(!validate('Login')->scene('login')->check($data))
        {
            $this->error(validate('Login')->getError());
        }
        try {
            $user = $this->obj->getUserByUserName($data['username']);
        } catch (\Exception $e)
        {
            $this->error($e->getMessage());
        }
        // 判断是否存在用户
        if(!$user || $user->status != 1)
        {
            $this->error('该用户不存在');
        }
        // 判断密码是否合理
        if(md5($data['password'].$user->code) != $user->password)
        {
            $this->error('密码错误，请重新输入');
        }
        // 登陆成功
        $loginData = [
            'last_login_time'=> time(), //登陆时间
            'last_login_ip' => $this->request->ip(), //登陆 ip
        ];
        model('User')->updateById($loginData, $user->id);
        // 把用户信息记录到 session
        session('o2o_user',$user,'o2o');    // session名，session值，作用域
        $this->success('登陆成功',url('index/index'));
    }

    /**
     *  退出登陆
     */
    public function logout()
    {
        session(null,'o2o');
        $this->redirect(url('user/login'));
    }

    /**
     * 修改密码
     * @return mixed
     */
    public function changePwd()
    {
        if(request()->isPost())
        {

            $data = input('post.'); // 数据校验
            $this->assign('user',$data); // 返显数据
            if(!$this->validate->scene('changePwd')->check($data))
            {
                $this->error($this->validate->getError());
            }

            // 从数据库查找用户信息
            $user = model('User')->find($this->account->id);
            if(!$user || !$user->id)
            {
                $this->error('用户不存在');
            }

            // 校验旧密码是否正确
            if($user->password != md5($data['old_pwd'].$user->code))
            {

                $this->error('旧密码不正确');
            }

            // 数据入库
            $pwd = [
              'password' => md5($data['new_pwd'].$user->code),
            ];
            try {
                $id = $this->obj->updateById($pwd, $user->id); // 更新密码
                $user = $this->obj->find($id);
                if(!$user || !$user->id)
                {
                    $this->error('用户不存在');
                }
                session('o2o_user', $user, 'o2o'); // 更新 session
                $this->success('密码修改成功');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        else {
            return $this->fetch();
        }
    }
}
