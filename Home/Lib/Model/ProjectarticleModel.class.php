<?
	/*
	项目下帖子
	@作者：
	*/
	
	
class ProjectarticleModel extends Model {
    // 定义自动验证
    protected $_validate    =   array(
		array('Projectarticle_title','require','题目不能为空'),
		//array('Projectarticle_content','require','您还未填写任何内容'),
        );
    // 定义自动完成
    protected $_auto    =   array(
        array('Projectarticle_time','time',1,'function'),
        );
 }
