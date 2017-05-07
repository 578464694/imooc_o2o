<?php

namespace app\bis\controller;
use think\Db;
class Waimai extends Base
{
    protected $user =  null;
    protected $validate = null;
    public function _initialize()
    {
        $this->user = $this->getLoginUser();
        $this->validate = validate('Waimai');
    }

    public function getWaimaiByStatus()
    {
        $data = input('get.');
        if (!$this->validate->scene('index')->check($data)) {
            $this->error(validate('Waimai')->getError());
        }

        $waimai = Db::table('o2o_order')    // 获得外卖
            ->alias('o')
            ->where(['o.use_type' => 2,
                'o.bis_id' => $this->user->bis_id,
                'o.send_type' => $data['send_type']
            ])
            ->join('o2o_user u','u.id = o.user_id')
            ->join('o2o_deal d','o.deal_id = d.id')
            ->field('o.id,d.name,o.send_type,
            o.address,o.deal_count,u.mobile')
            ->paginate();

        return $this->fetch('',[
            'waimai' => $waimai,
            'send_type' => $data['send_type'],
        ]);
    }

    public function sendType()
    {
        $data = input('get.');
        if(!$this->validate->scene('sendType')->check($data))
        {
            $this->error($this->validate->getError());
        }

        $result = model('Order')->allowField(true)->save(['send_type'=>$data['send_type']],['id' => $data['id']]);
        if(!$result)
        {
            $this->error('状态修改失败');
        }
        $this->success('状态修改成功');
    }

}
