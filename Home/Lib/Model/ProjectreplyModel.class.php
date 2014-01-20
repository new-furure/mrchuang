<?
	/*
	项目下帖子
	@作者：
	*/
	
	
class ProjectreplyModel extends Model {
    // 定义自动验证
    protected $_validate    =   array(
		array('Projectreply_title','require','题目不能为空'),
		//array('Projectreply_content','require','您还未填写任何内容'),
        );
    // 定义自动完成
    protected $_auto    =   array(
        array('Projectreply_time','time',1,'function'),
        );
 }
