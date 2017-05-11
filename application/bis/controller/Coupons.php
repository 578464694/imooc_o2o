<?php

namespace app\bis\controller;

use think\Controller;
use think\Request;
use app\common\validate;

class Coupons extends Base
{
    protected $validate = null;
    protected $obj = null;
    public function _initialize()
    {
        $this->validate = validate('Coupons');
        $this->obj = model('Coupons');
    }

    public function index()
    {
        model('Coupons')->where(['status' => 1])->paginate();
        return $this->fetch();
    }

    public function unuse()
    {
        // 0:生成未发送给用户
        // 1:已经发送给用户 2：用户已经使用 3 禁用

        $user = $this->getLoginUser();
        $coupons = model('Coupons')->getCouponsByBisId($user->bis_id);

        return $this->fetch('', [
            'coupons' => $coupons
        ]);
    }

    /**
     * 获得已使用的券
     * @return mixed
     */
    public function used()
    {
        $user = $this->getLoginUser();
        $coupons = model('Coupons')->getUsedCoupons($user->bis_id);

        return $this->fetch('', [
            'coupons' => $coupons
        ]);
    }

    public function status()
    {
        $data = input('get.');
        if(!$this->validate->scene('status')->check($data))
        {
            $this->error(validate('Coupons')->getError());
        }
        try {
            $result = model('Coupons')->where('id',$data['id'])->update(['status' => $data['status']]);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('状态修改成功');
    }

    public function query()
    {
        if(request()->isPost())
        {
            // 数据校验
            $data = input('post.');
            if(!$this->validate->scene('queryCoupons')->check($data))
            {
                $this->error(validate('Coupons')->getError());
            }
            // 查询优惠券
            $query = [
                'sn' => $data['coupons_id'],
            ];
            $coupon = $this->obj->where($query)->find(); // 查询优惠券
            if(!$coupon)
            {
                print_r('test');
            }
            return $this->fetch('',[
                'coupon' => $coupon,
            ]);
        }
        else
        {
            return $this->fetch();
        }
    }
}
