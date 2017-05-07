<?php

namespace app\bis\controller;
use think\Db;
class Waimai extends Base
{
    protected $user =  null;
    public function _initialize()
    {
        $this->user = $this->getLoginUser();
    }

    public function getWaimaiByStatus()
    {
        $data = input('get.');
        if (!validate('Waimai')->check($data)) {
            $this->error(validate('Waimai')->getError());
        }
//        $waimai = model('Order')->where(['use_type' => 2,
//            'bis_id' => $this->user->bis_id,
//            'send_type' => $data['send_type'],
//        ])->select();

        /*Db::table('think_artist')
            ->alias('a')
            ->join('think_work w','a.id = w.artist_id')
            ->join('think_card c','a.card_id = c.id')
            ->select();*/
        $waimai = Db::table('o2o_order')
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
        ]);
    }
}
