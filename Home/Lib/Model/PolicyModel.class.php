<?
	/*
	政策发布
	@作者：
	*/
	
	
class PolicyModel extends Model {
    // 定义自动验证
    protected $_validate    =   array(
		array('policy_title','require','题目不能为空'),
		//array('policy_content','100','内容不能少于50字'),
        );
    // 定义自动完成
    protected $_auto    =   array(
        array('policy_time','time',1,'function'),
        );
 }
