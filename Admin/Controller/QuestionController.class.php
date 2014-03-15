<?php
/*
+---------------------------------------------------
+后台模块
+问答管理模块
+功能：
+1、问题列表
+2、回答列表
+3、删除问题
+4、删除回答
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class QuestionController extends CommonController {
	function question()
	{
		$name=I('post.sname');
		$where['article_effective']='1';
		$where['article_type']=C('QUESTION_TYPE');
		$where['article_draft']=0;
		if($name!=NULL&&$name!='')
			$where['article_title']=array('LIKE','%'.$name.'%');
		$Question=D('QuestionView');
		$count=$Question->where($where)->count();
		//dump($list);
		$Page= new \Org\Util\PageW($count,25);
		$show= $Page->show();
		$list= $Question->where($where)->order('article_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($list);
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	function delQuestion()
	{
		$id=I('get.id');
		if(M('article')->where(array('article_id'=>$id))->setField('article_effective','0'))
			$this->success("删除成功");
		else
			$this->error("删除失败");
	}
	public function answer()
	{
		$this->redirect('Comment/comment',array('type'=>4));
		/*$name=I('post.sname');
		$where['comment_effective']='1';
		$where['comment_type']=C('QUESTION_COMMENT');//具体是几呢，现在不确定，，，wait！！
		if($name!=NULL&&$name!='')
			$where['comment_content|article_title']=array('LIKE','%'.$name.'%');
		$Answer=D('CommentView');//因为回答属于comment
		$count=$Answer->where($where)->count();
		$Page= new \Org\Util\Page($count,25);
		$show= $Page->show();
		$list= $Answer->where($where)->order('comment_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();*/
	}
	/*public function delAnswer()
	{
		$id=I('get.id');
		if(M('comment')->where(array('comment_id'=>$id))->setField('comment_effective','0'))
			$this->success("删除成功");
		else
			$this->error("删除失败");
	}*/
	public function scomment()
	{
		$this->redirect('Comment/scomment',array('type'=>3));
		/*$name=I('post.sname');
		$where['article_type']=C('QUESTION_TYPE');//具体是几呢，现在不确定，，，wait！！
		if($name!=NULL&&$name!='')
			$where['second_comment_content']=array('LIKE','%'.$name.'%');
		$SComment=D('SCommentView');//因为回答属于comment
		$count=$SComment->where($where)->count();
		$Page= new \Org\Util\Page($count,25);
		$show= $Page->show();
		$list= $SComment->where($where)->order('second_comment_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();*/
	}
	/*public function delSComment()//因为这里没有设置有效位，所以暂定为直接删除！
	{
		$arr=I('post.delarr');
		foreach($arr as $value)
		{
			M('second_comment')->where(array('second_comment_id'=>$value))->delete();
		}
		$this->success("删除成功");
	}*/
}