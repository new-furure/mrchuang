<?
	/*
	政策下帖子
	@作者：
	*/
	
	
class PolicyreplyModel extends Model {
    // 定义自动验证
    protected $_validate    =   array(
		array('policyreply_title','require','题目不能为空'),
		//array('policyreply_content','100','内容不能少于50字'),
        );
    // 定义自动完成
    protected $_auto    =   array(
        array('policyreply_time','time',1,'function'),
        );
 }
