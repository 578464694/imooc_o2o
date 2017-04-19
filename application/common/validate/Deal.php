<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/17
 * Time: 7:44
 */
namespace app\common\validate;
use think\Validate;
/**
 *  团购商品验证
 * Class Deal
 * @package app\common\validate
 */
class Deal extends Validate
{
    protected $rule = [
        ['name' ,'require|length:1,50','请填写名称|名称长度在1-50'],
        ['category_id','require','请选择品类'],
//        ['bis_id','require','商户标识符不能为空'],
        ['image','require','请选择缩略图'],
        ['description','require','请填写团购描述'],
        ['start_time','require|date','请选择开始时间|结束时间格式为日期'],
        ['end_time','require|date','请选择结束时间|结束时间格式为日期'],
        ['origin_price','require|float','请输入原始价格|价格须为数字'],
        ['current_price','require|float','请输入团购价格|价格须为数字'],
        ['notes','require','请输入购买须知'],
        ['location_ids','require|array','请选择支持门店'],
        ['coupons_begin_time','require|date','请选择团购券开始时间|团购券开始时间格式为日期'],
        ['coupons_end_time','require|date','请选择团购券结束时间|团购券结束时间格式为日期'],
    ];

    protected $scene = [
        'addDeal' => ['name','category_id','bis_id','image','description','start_time',
                        'end_time','origin_price','current_price','notes','location_ids'],  // 添加团购商品情景
        'test' => ['start_time','end_time'],
    ];

    public function isBeforeTime($start_time,$end_time)
    {
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        if($start_time >= $end_time)
        {
            return false;
        }
        return true;
    }
}