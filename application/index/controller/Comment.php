<?php
namespace app\index\controller;


class Comment extends Base
{

    protected $validate = null;
    protected $user = null;
    public function _initialize()
    {
        $this->validate = validate("Comment");
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
            'username' => $this->user['username'],
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
     * 验证用户是否登陆且有评论权限
     * @param $data
     * @return bool
     */
    public function checkIsCommentAndLogin($data)
    {
        if(!$this->isLogin())
        {
            return false;
        }
        $result = model('Order')->isComment($data['deal_id'],$this->user->id);
        if(!$result)
        {
            return false;
        }
        return $result;
    }

}