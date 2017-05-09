<?php
namespace app\common\model;

class Comment extends BaseModel
{
    /**
     * 获得商品的评论信息
     * @param $deal_id
     * @return array
     */
    public function getCommonClassByDealId($deal_id)
    {
        $comment = [];
        $comment['good_comment'] = $this->where('status', 1) // 获得好评
            ->where('comment_class', 0)
            ->where('deal_id', $deal_id)
            ->count();
        $comment['middle_comment'] = $this->where('status', 1) // 获得中评
            ->where('comment_class', 1)
            ->where('deal_id', $deal_id)
            ->count();
        $comment['bad_comment'] = $this->where('status', 1) // 获得差评
            ->where('comment_class', 2)
            ->where('deal_id', $deal_id)
            ->count();
        $comment['score'] = 0.0;
        $comment['count_comment'] = $comment['good_comment'] + $comment['middle_comment'] + $comment['bad_comment'];
        if($comment['count_comment'] != 0)  // count_comment 不为 0，才能进行接下来的运算
        {
            $comment['count_comment'] = $comment['good_comment'] + $comment['middle_comment'] + $comment['bad_comment'];
            $comment['score'] = $comment['good_comment'] * 5.0 + $comment['middle_comment'] * 4.0 + $comment['bad_comment'] * 1.0; // 计算评分
            $comment['score'] = $comment['score'] / floatval($comment['count_comment']);    // 注：在此处四舍五入会有 bug ,所以在前台四舍五入
        }

        return $comment;
    }

    /**
     * 获得商品评论
     * @param $deal_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getCommentInfoByDealId($deal_id)
    {
        $comments = $this->where('status',1)
                ->where('deal_id',$deal_id)
                ->select();

        return $comments;
    }
}
