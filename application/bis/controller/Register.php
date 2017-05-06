<?php
namespace app\bis\controller;

use think\Controller;
use app\common\validate;

/**
 * 商户注册模块
 * Class Register
 * @package app\bis\controller
 */
class Register extends Controller
{
    private $city;
    private $category;

    public function _initialize()
    {
        $this->city = model('City');
        $this->category = model('Category');
    }

    public function index()
    {
        // 获取一级城市的数据
        $citys = $this->city->getNormalCitysByParentId();
        // 获取一级分类
        $categorys = $this->category->getNormalFirstCategory();

        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
        ]);
    }

    public function add()
    {
        if (!request()->isPost()) {
            $this->error('请求错误');
        }
        // 获取表单数据
        $data = input('post.');
        // 校验表单数据
        $validate = validate('Bis');
        if (!$validate->scene('add')->check($data)) {
            $this->error($validate->getError());
        }
        // 总店相关信息校验
        if (!$validate->scene('headOffice')->check($data)) {
            $this->error($validate->getError());
        }
        // 账户相关信息校验
        if (!$validate->scene('account')->check($data)) {
            $this->error($validate->getError());
        }
//         获取经纬度
        $lnglat = \Map::getLngLat($data['address']);
        if (empty($lnglat) || $lnglat['status'] != 0) {
            $this->error('无法获取数据，或者匹配的地址不精准 ');
        }
        // 判定提交的用户是否存在
        $accountResult = model('BisAccount')->where('username', $data['username'])->find();
        if ($accountResult) {
            $this->error('该用户已存在，请重新分配');
        }
        $bisInfo = [
            'name' => $data['name'],
            'email' => $data['email'],
            'logo' => $data['logo'],
            'licence_logo' => $data['licence_logo'],
            'description' => empty($data['description']) ? '' : $data['description'],
            'city_id' => $data['city_id'],
            'city_path' => (!isset($data['se_city_id'])) ? $data['city_id'] : $data['city_id'] . ',' . $data['se_city_id'],
            'city_id' => $data['city_id'],
            'bank_info' => $data['bank_info'],
            'bank_name' => $data['bank_name'],
            'bank_user' => $data['bank_user'],
            'faren' => $data['faren'],
            'faren_tel' => $data['faren_tel'],
        ];

        $bisId = model('Bis')->add($bisInfo);
        $data['cat'] = '';
        if (!empty($data['se_category_id'])) {
            $data['cat'] = implode('|', $data['se_category_id']);
        }
        // 总店相关信息入库
        $locationData = [
            'name' => $data['name'],
            'logo' => $data['logo'],
            'api_address' => $data['address'],
            'tel' => $data['tel'],
            'contact' => $data['contact'],
            'xpoint' => empty($lnglat['result']['location']['lng']) ? '' : $lnglat['result']['location']['lng'],
            'ypoint' => empty($lnglat['result']['location']['lat']) ? '' : $lnglat['result']['location']['lat'],
            'bis_id' => $bisId,
            'open_time' => empty($data['open_time']) ? '' : $data['open_time'],
            'content' => empty($data['content']) ? '' : $data['content'],
            'category_id' => $data['category_id'],
            'category_path' => $data['category_id'] . ',' . $data['cat'],
            'city_path' => (!isset($data['se_city_id'])) ? $data['city_id'] : $data['city_id'] . ',' . $data['se_city_id'],
            'city_id' => $data['city_id'],
            'address' => $data['address'],
            'is_main' => 1, // 代表总店信息
        ];
        $locationId = model('BisLocation')->add($locationData);
        // 自动生成 密码的加盐字符串
        $data['code'] = mt_rand(100, 10000);
        $accountData = [
            'bis_id' => $bisId,
            'username' => $data['username'],
            'code' => $data['code'],
            'password' => md5($data['password'] . $data['code']),
            'is_main' => 1, // 代表的是总管理员
        ];
//        var_dump($accountData);
//        exit();
        $accountId = model('BisAccount')->add($accountData);
        if (!$accountId) {
            $this->error('申请失败');
        }

        // 发送邮件
        $url = request()->domain() . url('bis/register/waiting', ['id' => $bisId]);
        $title = "o2o入驻申请通知";
        $content = "您提交的入驻申请需等待平台方审核，您可以通过点击链接<a href='" . $url . "' target='_blank'>查看链接</a> 查看审核状态";
        \phpmailer\Email::send($data['email'], $title, $content);

        $this->success('申请成功', url('register/waiting', ['id' => $bisId]));    // 在本模块下，所以只写url 即可
    }

    public function waiting($id)
    {
        if (empty($id)) {
            $this->error('error');
        }
        $detail = model('Bis')->get($id);
        return $this->fetch('', [
            'detail' => $detail,
        ]);
    }
}