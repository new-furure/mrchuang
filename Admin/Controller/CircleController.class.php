<?php
/*
+---------------------------------------------------
+后台模块
+圈子模块
+功能
+1、圈子列表
+2、删除圈子
+3、（升级圈子，提醒圈主）
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class CircleController extends CommonController {
	//圈子列表
	public function index()
	{
		$Circle=M('circle');
		$where['circle_effective']=1;
		$count=$Circle->where($where)->count();
		$Page=new \Org\Util\PageW($count,25);
		$show=$Page->show();
		$list=$Circle->where($where)->order('circle_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	//解散圈子
	public function delCircle()
	{
		$id=I('get.id');
		$record=M('circle')->field('circle_name,user_id')->where(array('circle_id'=>$id))->find();
		$msg="您的群“".$record['circle_name']."”已被管理员解散";
		$data['user_id']=$record['user_id'];
		$data['notice_title']=$msg;
		$data['notice_content']=$msg;
		if(M('notice')->add($data)&&M('circle')->where(array('circle_id'=>$id))->setField('circle_effective',0))
			$this->success("圈子解散成功");
		else
			$this->error("圈子解散失败，请重试");
		//目前不知道要怎么个解散方法
	}
	public function sendNotice()
	{
		$id=I('get.id');
		$record=M('circle')->field('circle_name,user_id')
		->where(array('circle_id'=>$id))->find();
		$this->assign('record',$record);
		$this->display();
	}
	public function runSendNotice()
	{
		if (!IS_POST) 
		{
			$this->error('页面不存在');
		}
		if (!I("post.submit")) 
		{
			return false;
		}
		$data['user_id']=I('post.uid');
		$data['notice_title']=I('post.ntitle');
		$data['notice_content']=I('post.ncontent');
		if(M('notice')->add($data))
		{
			$this->success("通知修改成功");
		}
		else
		{
			$this->error("通知修改失败，请重试");
		}
	}
	public function postList()
	{
		$where['article_effective']=1;
		$where['article_type']=C('POST_TYPE');
		$where['article_draft']=0;
		$name=I('post.sname');
		if($name!=NULL&&$name!='')
		{
			$where['article_title']=array('LIKE','%'.$name.'%');
		}
		$Post=D('PostView');
		$count=$Post->where($where)->count();
		$Page=new \Org\Util\PageW($count,10);
		$show=$Page->show();
		$list=$Post->where($where)->order('article_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		$posttype=array();
		$posttype[C("OUT_POST")]="跨圈";
		$posttype[C("IN_POST")]="圈内";
		$posttype[C("CHAT_POST")]="群聊贴";
		$posttype[C("NORMAL_POST")]="普通帖子";
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->assign('posttype',$posttype);
		$this->display();
	}
	public function delPost()
	{
		$id=I('get.id');
		if(M('article')->where(array('article_id'=>$id))->setField('article_effective','0'))
			$this->success("删除帖子成功");
		else
			$this->error("删除帖子失败，请重试");
	}
	public function comment()
	{
		$this->redirect('Comment/comment',array('type'=>5));
	}
	public function scomment()
	{
		$this->redirect('Comment/scomment',array('type'=>4));
	}
	
	
	//圈子管理中的回复和评论管理呢。。。。。
	//现在做的太麻烦了！减少重复代码，把问题，项目，政策，帖子的评论和回复全部做到一个控制器里面。
	/*public function comment()
	{
		$name=I('post.sname');
		$where['comment_effective']='1';
		$where['article_type']=C('POST_TYPE');//具体是几呢，现在不确定，，，wait！！
		$where['comment_type']=C('POST_COMMENT');
		//还有一个$where['comment_type']='0';
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
		$this->display();
	}
	public function delComment()
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
		$name=I('post.sname');
		$where['article_type']=C('POLICY_TYPE');//具体是几呢，现在不确定，，，wait！！
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
	}*/
}