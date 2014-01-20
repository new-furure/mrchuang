<?
	/*
	项目发布
	@作者：
	*/
	
	
class ProjectModel extends Model {
    // 定义自动验证
    protected $_validate    =   array(
		array('project_title','require','题目不能为空'),
	//	array('project_content','100','内容不能少于50字'),
        );
    // 定义自动完成
    protected $_auto    =   array(
        array('project_time','time',1,'function'),
        );
 }
