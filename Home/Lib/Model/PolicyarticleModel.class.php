<?
	/*
	政策下帖子
	@作者：
	*/
	
	
class PolicyarticleModel extends Model {
    // 定义自动验证
    protected $_validate    =   array(
		array('policyarticle_title','require','题目不能为空'),
		//array('policyarticle_content','require','您还未填写任何内容'),
        );
    // 定义自动完成
    protected $_auto    =   array(
        array('policyarticle_time','time',1,'function'),
        );
 }
