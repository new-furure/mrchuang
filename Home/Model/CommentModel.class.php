<?php
namespace Home\Model;
use Think\Model;
class CommentModel extends Model {
	protected $_validate    =   array(
		array('comment_content','require','评论内容不能为空'),
        );
    // 定义自动完成
    protected $_auto    =   array(
        array('comment_time','time',1,'function'),
        );
}
