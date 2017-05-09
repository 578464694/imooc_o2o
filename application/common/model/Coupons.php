<?php
namespace app\common\model;

use think\Exception;
use think\Model;

class Coupons extends BaseModel
{
    /**
     * 获得购物券
     * @param $bis_id
     * @param int $neqStatus
     * @return array
     */
    public function getCouponsByBisId($bis_id,$neqStatus = 2)
    {
        // 0:生成未发送给用户
        // 1:已经发送给用户 2：用户已经使用 3 禁用
        $deal_ids = model('Deal')->where('bis_id',$bis_id)->column('id');
        $unuseCoupons = [];
        foreach ($deal_ids as $deal_id)
        {
           $unuseCoupons[] = model('Coupons')->where(['deal_id'=>$deal_id,
                            'status'=>['neq',$neqStatus]])
                            ->select();
        }
        return $unuseCoupons;
    }

    public function getUsedCoupons($bis_id)
    {
        $deal_ids = model('Deal')->where('bis_id',$bis_id)->column('id');
        $useCoupons = [];
        foreach ($deal_ids as $deal_id)
        {
            $useCoupons[] = model('Coupons')->where(['deal_id'=>$deal_id,
                'status'=>2])
                ->select();
        }
        return $useCoupons;
    }


    public function add($data)
    {
        $this->save($data);
        return $this->id;
    }
}
