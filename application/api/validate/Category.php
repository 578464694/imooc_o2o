<?php
namespace app\api\validate;
use think\Validate;

class Category extends Validate {
    protected $rule = [
      ['name','require|max:10','名称不能为空|分类名不能超过10个字符'],
      ['parent_id','number|>=:0','分类标识应为数字，且大于等于0'],
      ['id','number','名称标识应为数字'],
      ['status','number|in:-1,0,1','状态必须是数字|状态不合法'],
      ['listorder','number','排序标识为数字']
    ];

    /**场景设置**/
    protected $scene = [
        'add' => ['name','parent_id','id'], //添加场景，增加对 id　的验证，如果没有 id这个值，则不进行校验
        'listorder' => ['id','listorder'],  //排序场景
        'status' => ['id','status'],         // 状态改变场景
        'query' => ['parent_id']              //查询场景
    ];
}

?>
