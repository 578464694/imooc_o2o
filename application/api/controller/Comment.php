<?php
namespace app\api\controller;


class Comment extends Base
{
    protected $user = null;
    public function _initialize()
    {
        $this->user = $this->getLoginUser();
    }

    /**
     * 验证权限
     */
    public function checkPower()
    {
        if(!$this->isLogin())
        {
            return show(0,'error'); //0 代表未登录
        }
        return show(1,'success');
    }

    /**
     * 添加评论
     */
    public function addComment()
    {
        // 校验是否有isComment islogin
        $data = input('post.');
        // 校验评论信息
        if(!$this->validate->scene('comment')->check($data))
        {
            $this->error($this->validate->getError());
        }

        $order = null;
        if(!($order = $this->checkIsCommentAndLogin($data)))
        {
            $this->error('无评论权限');
        }
        $commentData = [
            'user_id' => $this->user->id,
            'comment' => $data['content'],
            'deal_id' => $data['deal_id'],
            'comment_class' => $data['comment_class'],
        ];
        //此处有坑 try catch 会进入 catch
        $result = model('Order')->updateById(['is_comment' => 1], $order['id']);
        $comment = model('Comment')->save($commentData);
        if($result && $comment) {
            $this->success('评论成功');
        } else {
            $this->error('评论失败');
        }

    }

    /**
     * 校验是否有评论权限和登陆
     * @param $data
     * @return bool
     */
    public function checkIsCommentAndLogin($data)
    {
        if(!$this->isLogin())   // 如果用户没有登录
        {
            return false;
        }
        if(!$this->checkIsComment($data))
        {
            return false;
        }
        return true;
    }

    /**
     * 验证用户是否有评论权限
     * @param $data
     * @return bool
     */
    public function checkIsComment($data)
    {
        $result = model('Order')->isComment($data['deal_id'],$this->user->id);

        if(!$result)
        {
            return false;
        }
        return $result;
    }

    /**
     * 验证用户评论权限 0 为数据错误，1 为正常，2 为未登录，3 为无权限
     * @return array
     */
    public function checkCommentPower()
    {
        $data = input('post.');
        // 校验数据
        if(!validate("Comment")->scene('comment_power')->check($data))
        {
            return show(0,'error');
        }

        $this->user = $this->getLoginUser();
        if(!$this->isLogin())   // 如果用户没有登录
        {
            return show(2,'nologin');
        }
        if(!$this->checkIsComment($data))   // 如果用户没有评论权限
        {
            return show(3,'nopower');
        }
        return show(1,'success');
    }



}