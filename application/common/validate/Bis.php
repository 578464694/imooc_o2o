<?php
namespace app\common\validate;
use think\Validate;

class Bis extends Validate
{
    protected $rule = [
        ['name' ,'require|max:25','商户名不能为空'],
        'email' => ['email','require'],
        'logo' => 'require',
        'city_id' => 'require',
        'bank_info' => 'require',
        'bank_name' => 'require',
        'bank_user' => 'require',
        'faren' => 'require',
        ['faren_tel' ,'require|regex:^1[34578]\d{9}$','手机号不能为空|手机号格式不正确'],
        ['tel','require|regex:^1[34578]\d{9}$','总店号码不能为空|手机号格式错误'],
        ['contact','require','联系人不能为空'],
        ['open_time','date','营业时间格式不正确'],
        ['username','require|length:6,13','账号不能为空|账户长度在6-13之间'],
        ['password','require|length:6,13','密码不能为空|密码长度在6-13之间'],
        ['id','require|number|>=:1','ID不能为空|ID为数字|ID不合法'],
        ['status','number|in:-1,0,1,2','状态必须为数字|状态范围不合法'], //-1代表删除，0代表待审，1代表通过，2代表不通过
    ];
    // 场景设置
    protected $scene = [
       'add' => ['name', 'email', 'logo', 'city_id', 'bank_info', 'bank_user', 'faren', 'faren_tel'],
        'fendianadd'=>['name','logo','city_id'],
        'headOffice' => ['tel','contact','open_time'],  // 验证总店信息
        'account' => ['username','password'],            //验证账号信息
        'login'=>['username','password'],
        'edit' => ['id'],
        'status' => ['status'],
    ];



}