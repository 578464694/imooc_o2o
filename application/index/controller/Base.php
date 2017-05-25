<?php
namespace app\index\controller;

use think\Controller;

class Base extends Controller
{
    protected $city = '';
    protected $account = '';
    public function _initialize()
    {
        // 城市数据
        $citys = model('City')->getNormalCitys();
        // 用户数据
        $this->getCity($citys);
        // 获得推荐 商品条目信息
        $cats = $this->getRecommendCats();
        // 获得登陆用户
        $user = $this->getLoginUser();
        // 将城市信息保存到 session 中
        session('city',$this->city,'o2o');

        $this->assign('citys', $citys);
        $this->assign('city',$this->city);
        $this->assign('user',$user);
        $this->assign('cats',$cats);
        $this->assign('controller',strtolower(request()->controller()));
        $this->assign('title','o2o团购网');
    }

    /**
     * 获得默认城市或所选城市
     * @param $citys
     */
    public function getCity($citys)
    {
        $defaultuname = '';
        foreach ($citys as $city) {
            $city = $city->toArray();
            if ($city['is_default'] == 1) {
                $defaultuname = $city['uname'];
                break;
            }
        }
        $defaultuname = $defaultuname ? $defaultuname : 'nanchang';
        if (session('cityuname', '', 'o2o') && !input('get.city')) {
            $cityuname = session('cityuname', '', 'o2o');
        } else {
            $cityuname = input('get.city', $defaultuname, 'trim');
            session('cityuname', $cityuname, 'o2o');
        }
        $this->city = model('City')->where(['uname' => $cityuname])->find();
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

    /**
     * 获取首页推荐中 商品分类数据
     */
    public function getRecommendCats()
    {
        $parentIds = $sedCatArr = $recomCats = [];

        $cats = model('Category')->getNormalRecommendCategoryByParentId(0,5);

        foreach ($cats as $cat)
        {
            $parentIds[] = $cat->id;
        }
        // 获得二级分类数据
        $sedCats = model('Category')->getNormalCategoryIdByParentId($parentIds);

        // 进行分类数据组装
        foreach ($sedCats as $sedcat)
        {
            $sedCatArr[$sedcat->parent_id][] = [
                'id' => $sedcat->id,
                'name' => $sedcat->name,
            ];
        }
        // 组装推荐数据
        foreach ($cats as $cat)
        {
            // recomCats 代表一级和二级分类数据，[] 第一个参数是，一级分类的 name，第二个参数是 此一级分类下所有二级分类数据
            $recomCats[$cat->id] = [$cat->name, empty($sedCatArr[$cat->id])?[]:$sedCatArr[$cat->id]];
        }
        return $recomCats;
    }
}
