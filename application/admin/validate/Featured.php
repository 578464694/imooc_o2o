<?php
namespace app\admin\validate;
use think\Validate;
class Featured extends Validate
{
    protected $rule = [
        ['title','require|max:30','请填写标题|标题最长30字符'],
        ['image','require|max:255','请添加缩略图|缩略图路径超过长度'],
        ['url','require|url|max:255','请添加 URL|请填写有效的 URL|URL 最大长度 255'],
        ['type', 'require|in:0,1,2'],
        ['description','require|max:255','请填写描述']
    ];

    protected $scene = [
        'add' => ['title','image','url','type','description'],
    ];
}