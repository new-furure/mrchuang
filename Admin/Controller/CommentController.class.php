<?php
/*
+---------------------------------------------------
+后台模块
+评论管理模块
+功能：
+1、把各个模块的评论模块整合到一起。
+2、按时间顺序列表
+3、删除评论
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class CommentController extends CommonController {
	public function comment()
	{
		$name=I('post.sname');
		$type=I('get.type');
		$where['comment_effective']='1';
		switch($type)
		{
			case 1://project improve
				$where['article_type']=C('PROJECT_TYPE');
				$where['comment_type']=C('PROJECT_IMPROVE');
				$nowname="项目完善";
				break;
			case 2:
				$where['article_type']=C('PROJECT_TYPE');
				$where['comment_type']=C('PROJECT_COMMENT');
				$nowname="项目评论";
				break;
			case 3:
				$where['article_type']=C('POLICY_TYPE');
				$where['comment_type']=C('POLICY_COMMENT');
				$nowname="政策评论";
				break;
			case 4:
				$where['article_type']=C('QUESTION_TYPE');
				$where['comment_type']=C('QUESTION_COMMENT');
				$nowname="问题回答";
				break;
			case 5:
				$where['article_type']=C('POST_TYPE');
				$where['comment_type']=C('POST_COMMENT');//这个在配置文件中还没写！
				$nowname="帖子回复";
				break;
		}
		if($name!=NULL&&$name!='')
			$where['comment_content|article_title']=array('LIKE','%'.$name.'%');
		$Comment=D('CommentView');//因为回答属于comment
		$count=$Comment->where($where)->count();
		$Page= new \Org\Util\PageW($count,25);
		$show= $Page->show();
		$list= $Comment->where($where)->order('comment_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->assign('nowname',$nowname);
		$this->display();
	}
	function delComment()
	{
		$arr=I('post.delarr');
		foreach($arr as $value)
		{
			M('comment')->where(array('comment_id'=>$value))->setField('comment_effective','0');
		}
		$this->success("删除成功");
	}
	public function scomment()
	{
		$type=I('get.type');
		switch($type)
		{
			case 1://project improve
				$where['article_type']=C('PROJECT_TYPE');
				$nowname="项目评论回复";
				break;
			case 2:
				$where['article_type']=C('POLICY_TYPE');
				$nowname="政策评论回复";
				break;
			case 3:
				$where['article_type']=C('QUESTION_TYPE');
				$nowname="问题回答回复";
				break;
			case 4:
				$where['article_type']=C('POST_TYPE');
				$nowname="帖子评论回复";
				break;
		}
		$name=I('post.sname');
		if($name!=NULL&&$name!='')
			$where['second_comment_content']=array('LIKE','%'.$name.'%');
		$SComment=D('SCommentView');//因为回答属于comment
		$count=$SComment->where($where)->count();
		$Page= new \Org\Util\PageW($count,25);
		$show= $Page->show();
		$list= $SComment->where($where)->order('second_comment_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->assign('nowname',$nowname);
		$this->display();
	}
	public function delSComment()//因为这里没有设置有效位，所以暂定为直接删除！
	{
		$arr=I('post.delarr');
		foreach($arr as $value)
		{
			M('second_comment')->where(array('second_comment_id'=>$value))->delete();
		}
		$this->success("删除成功");
	}
}