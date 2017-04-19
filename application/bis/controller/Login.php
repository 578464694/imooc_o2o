<?php
namespace app\bis\controller;

use think\console\command\make\Model;
use think\Controller;
use app\common\validate;

/**
 * 商户登陆模块
 * Class Login
 * @package app\bis\controller
 */
class Login extends Controller
{
    protected $validate = null;
    public function _initialize()
    {
        $this->validate = validate('Bis');
    }

    public function index()
    {
        if(request()->isPost())
        {
            $data = input('post.');
            // 进行严格校验
            if(!$this->validate->scene('login')->check($data))
            {
                $this->error($this->validate->getError());
            }
            // 通过用户名获取账号信息
            $account = model('BisAccount')->get(['username'=>$data['username']]);
            if(!$account || $account->status != 1)
            {
                $this->error('该用户不存在，或者审核未通过');
            }
            // 如果密码正确
            if($account->password != md5($data['password'].$account->code))
            {
                $this->error('密码不正确');
            }

            //登陆成功，更新数据库
            model('BisAccount')->updateById(['last_login_time'=>time()],$account->id);
            //保存用户信息
            session('bisAccount',$account,'bis');   //bisAccount为变量名， $account为变量值，bis为作用域
            return $this->success('登陆成功',url('index/index'));  //登陆成功，跳转到 index控制器的index方法
        }
        else
        {
            $bisAccount = session('bisAccount','','bis');   // 获取session
            if($bisAccount && $bisAccount->id)
            {
                return $this->redirect(url('index/index')); //存在 session跳转商户首页
            }
            return $this->fetch();
        }

    }

    public function logout()
    {
        // 清除bis作用域下的所有session
        session(null,'bis');
        return $this->redirect(url('login/index'));
    }
}