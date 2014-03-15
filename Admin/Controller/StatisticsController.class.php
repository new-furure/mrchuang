<?php
/*
+---------------------------------------------------
+后台模块
+统计模块
+功能：
+1、用户信息统计
+2、项目/政策/问答统计
+3、百度统计//暂时没有做.
+4、
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class StatisticsController extends CommonController {
	//首页，直接显示数据即可？还需要别的吗？
	public function index()
	{
		$this->display();//此处可以跳转到首页，用来显示服务器信息。
	}
	//获取统计数据
	public function userData()
	{
		$Person=D('PersonView');
		$Organization=D('OrganizationView');
		$this->user_person=$Person->where(array('user_effective'=>'1'))->count();//个人用户
		$this->user_org=$Organization->where(array('user_effective'=>'1'))->count();
		$outtime=date('Y-m-d H:i:s',time()-600);
		$where['user_time']=array('gt',$outtime);
		$this->user_now=M('user')->where($where)->where(array('user_effective'=>'1'))->count();
		
		//下面这样的查询方法会有问题，就是的那个category_id变了的时候。
		$this->user_c1=$Organization->where(array('user_effective'=>'1','category_id'=>C('STARTUP')))->count();
		$this->user_c2=$Organization->where(array('user_effective'=>'1','category_id'=>C('ENTERPRISE')))->count();
		$this->user_c3=$Organization->where(array('user_effective'=>'1','category_id'=>C('VC')))->count();
		$this->user_c4=$Organization->where(array('user_effective'=>'1','category_id'=>C('INCUBATOR')))->count();
		$this->user_c5=$Organization->where(array('user_effective'=>'1','category_id'=>C('INVITEE')))->count();
		$this->user_c6=$Organization->where(array('user_effective'=>'1','category_id'=>C('GOVERNMENT')))->count();
		//在线峰值
		//活跃用户怎么判断呢
		//登录用户是什么意思？
		$this->display();	
	}
	public function projectData()
	{
		$Project=M('article');
		$where['article_effective']=1;
		$where['article_type']=C('PROJECT_TYPE');
		$where['article_draft']=0;
		$this->project_sum=$Project->where($where)->count();
		$this->comment_sum=M('comment')->where(array('comment_effective'=>1,'comment_type'=>C('PROJECT_COMMENT')))->count();
		$this->improve_sum=M('comment')->where(array('comment_effective'=>1,'comment_type'=>C('PROJECT_IMPROVE')))->count();
		$this->project_maxup=$Project->where($where)->max('article_up_number');
		$this->project_maxcomment=$Project->where($where)->max('article_comment_number');
		$this->project_maxhits=$Project->where($where)->max('article_hits');
			
		$this->display();
	}
	public function policyData()
	{
		$Policy=M('article');
		$where['article_effective']=1;
		$where['article_type']=C('POLICY_TYPE');
		$where['article_draft']=0;
		$this->policy_sum=$Policy->where($where)->count();
		$this->comment_sum=M('comment')->where(array('comment_effective'=>1,'comment_type'=>C('POLICY_COMMENT')))->count();
		$this->policy_maxup=$Policy->where($where)->max('article_up_number');
		$this->policy_maxcomment=$Policy->where($where)->max('article_comment_number');
		$this->policy_maxhits=$Policy->where($where)->max('article_hits');
		
		$this->display();
	}
	public function qaData()
	{
		$Question=M('article');
		//wait.....下面这两个的type的具体数值都还没有确定。
		$where['article_effective']=1;
		$where['article_type']=C('QUESTION_TYPE');
		$where['article_draft']=0;
		$this->question_sum=$Question->where($where)->count();
		
		$this->answer_sum=M('comment')->where(array('comment_effective'=>'1',
		'comment_type'=>C('QUESTION_COMMENT')))->count();
		//怎么确定优质回答？
		$this->display();
	}
	public function baiduStat()
	{
		//此函数用于跳转到百度统计页面。
	}
	/*
	public function datas()
	{
		//流量统计
		//用户统计
		$User=M('user');
		$this->user_sum=$User->where(array('user_effective'=>'1'))->count();
		$this->user_lock=$User->where(array('user_effective'=>'0'))->count();
		$this->user_c1=$User->where(array('user_effective'=>'1','Category_ID'=>'1'))->count();
		$this->user_c2=$User->where(array('user_effective'=>'1','Category_ID'=>'2'))->count();
		$this->user_c3=$User->where(array('user_effective'=>'1','Category_ID'=>'3'))->count();
		$this->user_c4=$User->where(array('user_effective'=>'1','Category_ID'=>'4'))->count();
		//项目统计
		$Project=M('project');
		$this->project_no=$Project->where(array('project_effective'=>'0'))->count();
		$this->project_yes=$Project->where(array('project_effective'=>'1'))->count();
		$this->project_sum=$this->project_no+$this->project_yes;
		$this->project_up=M('user_up_project')->count();
		$this->project_down=M('user_down_project')->count();
		//难道还要图标显示？！？？！？
		
		//政策统计
		$Policy=M('policy');
		$this->policy_no=$Policy->where(array('policy_effective'=>'0'))->count();
		$this->policy_yes=$Policy->where(array('policy_effective'=>'1'))->count();
		$this->policy_sum=$this->policy_no+$this->policy_yes;
		$this->policy_up=M('user_up_policy')->count();
		$this->policy_down=M('user_down_policy')->count();
		//问答统计
		$this->question_sum=M('question')->where(array('question_effective'=>1))->count();
		$this->answer_sum=M('answer')->where(array('answer_effective'=>1))->count();
		
		$Admin=M('admin');
		$numAdmin=$Admin->where("admin_privilege>0")->count();
		
		$User=M('user');
		$numUser=$User->count();
		return $data;
	}*/
	/*
	a)	流量统计------wait
b)	用户统计
	用户总量，各个内别用户数量
	在线用户数量------wait
	在线峰值统计------wait
	活跃用户统计------wait
	登陆统计------wait

c)	项目统计
	总数9
	赞踩统计（同前台显示）------wait
	回复数统计------wait
	发布时间统计.------wait
d)	政策统计
	总数
	赞踩统计（同前台显示）------wait
	回复数统计------wait
	发布时间统计------wait
e)	问答块统计
	总数
	优质回答统计（同前台显示）//////////这里到底是什么意思。。。。。？？？？？？
	回答数统计
	发布时间统计------wait
*/
}