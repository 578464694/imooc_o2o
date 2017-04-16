<?php
namespace app\admin\controller;

use phpmailer\Email;
use think\Controller;

class Bis extends Controller
{
    protected $obj = null;
    protected $validate = null;

    /**
     * TP5 默认的初始化方法 _initialize
     */
    public function _initialize()
    {
        $this->obj = model('Bis'); // 将 model('Category') 抽离出来
        $this->validate = validate('Bis');
    }

    /**
     *  入驻申请列表
     * @return mixed
     */
    public function apply()
    {
        $bis = $this->obj->getBisByStatus();
        return $this->fetch('', [
            'bis' => $bis,
        ]);
    }

    public function detail()
    {
        $id = input('get.id');
        if (empty($id)) {
            return $this->error('ID错误');
        }
        // 获取一级城市的数据
        $citys = model('City')->getNormalCitysByParentId();
        // 获取一级分类
        $categorys = model('Category')->getNormalFirstCategory();
        // 获取商户基本信息
        $bisData = model('Bis')->get($id);
        // 获取总店信息
        $locationData = model('BisLocation')->get(['bis_id' => $id, 'is_main' => 1]);
        // 获取总账号信息
        $accountData = model('BisAccount')->get(['bis_id' => $id, 'is_main' => 1]);
        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
            'bisData' => $bisData,
            'locationData' => $locationData,
            'accountData' => $accountData,
        ]);
    }

    // 状态逻辑
    public function status()
    {
        $data = input('get.');
        if (!$this->validate->scene('status')->check($data)) {
            $this->error($this->validate->getError());
        }
        $bis = $this->obj->save(['status' => $data['status']], ['id' => intval($data['id'])]);   // 商户基本表状态修改
        $location = model('BisLocation')->save(['status' => $data['status']], ['bis_id' => intval($data['id']), 'is_main' => 1]); // 商户总店表状态修改
        $account = model('BisAccount')->save(['status' => $data['status']], ['bis_id' => intval($data['id']),'is_main'=>1]);   //商户账号状态修改

        //获取email
        $email = $this->obj->where(['id'=>$data['id']])->column('email');
        $email = implode($email);   //将数组转为字符串

        //-1代表删除，1代表通过，2代表不通过
        $title = '';
        $content = '您的账号'.$account['username'];
        switch ($data['status'])
        {
            case -1:
                $title = '您的o2o申请被删除';
                $content .= '申请被删除,请重新提交申请';
                break;
            case 1:
                $title = '您的o2o申请已通过';
                $content .= '申请已通过';
                break;
            case 2:
                $title = '您的o2o申请未通过';
                $content .= '申请未通过，请重新申请';
                break;
        }

        //发送邮件
        if ($bis && $location && $account) {
            \phpmailer\Email::send($email,$title,$content);
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }

    }
}
