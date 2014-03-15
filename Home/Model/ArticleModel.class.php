<?php
namespace Home\Model;
use Think\Model;
class ArticleModel extends Model {
	protected $_validate    =   array(
		array('article_title','require','题目不能太长'),
		array('article_content','require','内容不能为空'),
        );
    // 定义自动完成
    /*protected $_auto    =   array(
        array('article_time','time',1,'function'),
        );*/
}
