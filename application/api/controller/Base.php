<?php
namespace app\api\controller;
use think\console\command\make\Controller;

class Base extends Controller
{
    protected $account = null;
    /**
     *  获取用户登陆 session
     *  session 中保存登陆对象
     * @return mixed
     */
    public function getLoginUser()
    {
        if(!$this->account)
        {
            $this->account = session('o2o_user', '', 'o2o');
        }
        return $this->account;
    }

    /**
     * 检查用户是否登陆
     * @return bool
     */
    public function isLogin()
    {
        $user = $this->getLoginUser();
        if($user && $user->id)
        {
            return true;
        }
        return false;
    }
}