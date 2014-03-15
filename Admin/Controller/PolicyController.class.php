<?php
/*
+---------------------------------------------------
+后台模块
+政策管理模块
+功能
+1、显示政策列表
+2、删除政策
+3、通知修改
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class PolicyController extends CommonController {
	//首页，显示政策列表
	public function index()
	{
		$where['article_effective']=1;
		$where['article_type']=C('POLICY_TYPE');
		$where['article_draft']=0;
		$name=I('post.sname');
		if($name!=NULL&&$name!='')
		{
			$where['article_title']=array('LIKE','%'.$name.'%');
		}
		$Policy=D('PolicyView');
		$count=$Policy->where($where)->count();
		//dump($count);
		$Page=new \Org\Util\PageW($count,10);
		$show=$Page->show();
		$list=$Policy->where($where)->order('article_time desc')//这个地方不用做field限制，因为在modelview中已经定义了。
		->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($list);
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	public function detailPolicy()
	{
		//直接调用前台界面
		$pid=I('get.id');
		$url=get_url_by_id($pid);
		$this->redirect($url);
	}
	public function delPolicy()
	{
		$pid=I('get.id');
		if(M('article')->where(array('article_id'=>$pid))->setField('article_effective','0'))
		{
			//并且还要发送消息给项目所有者。
			$record=M('article')->field('article_title,user_id')->where(array('article_id'=>$pid))->find();
			$data['user_id']=$record['user_id'];
			$data['notice_content']="您的文章“".$record['article_title']."”已被管理员删除";
			$data['notice_title']=$data['notice_content'];
			$data['notice_type']=51;//此处未定，，，，消息的类型未知有哪些。
			if(M('notice')->add($data))
				$this->success("删除成功");
		}
		else
		{
			$this->error("删除失败，请重试");
		}
	}
	public function needEdit()
	{
		$id=I('get.id');
		$record=M('article')->field('user_id,article_title')->where(array('article_id'=>$id))->find();
		$this->assign('title',$record['article_title']);
		$this->assign('uid',$record['user_id']);
		$this->display();
	}
	public function runNeedEdit()
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
		$title=I('post.title');
		$data['notice_content']="对您的文章“".$title."”通知如下:".I('post.content');
		$data['notice_title']=I('post.ntitle');
		$data['notice_type']=51;
		if(M('notice')->add($data))
		{
			$this->success("通知修改成功");
		}
		else
		{
			$this->error("通知修改失败，请重试");
		}
	}
	public function comment()
	{
		$this->redirect('Comment/comment',array('type'=>3));
		/*$name=I('post.sname');
		$where['comment_effective']='1';
		$where['article_type']=C('POLICY_TYPE');//具体是几呢，现在不确定，，，wait！！
		$where['comment_type']=C('POLICY_COMMENT');
		//还有一个$where['comment_type']='0';
		if($name!=NULL&&$name!='')
			$where['comment_content|article_title']=array('LIKE','%'.$name.'%');
		$Comment=D('CommentView');//因为回答属于comment
		$count=$Comment->where($where)->count();
		$Page= new \Org\Util\Page($count,25);
		$show= $Page->show();
		$list= $Comment->where($where)->order('comment_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();*/
	}
	/*public function delComment()
	{
		$arr=I('post.delarr');
		foreach($arr as $value)
		{
			M('comment')->where(array('comment_id'=>$value))->setField('comment_effective','0');
		}
		$this->success("删除成功");
	}*/
	public function scomment()
	{
		$this->redirect('Comment/scomment',array('type'=>2));
		/*$name=I('post.sname');
		$where['article_type']=C('POLICY_TYPE');//具体是几呢，现在不确定，，，wait！！
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
	/*public function undelPolicy()
	{
		$data['policy_id']=I('get.id');
		$data['policy_effective']=1;
		if(M('policy')->save($data))
			$this->success("恢复成功");
		else
			$this->error("恢复失败");
	}*/
}

//通知发布者修改，设置有效位，并给政策所有者发送系统消息，调用消息模块？
	/*public function rejectPolicy()
	{
		$pid=I('get.id');
		if($pid==NULL)
			$this->error("not exist");
		$record=M('policy')->find($pid);
		$this->assign('list',$record);
		$this->display();
		//发送修改消息
	}
	public function runRejectPolicy()
	{
		if (!IS_POST) 
		{
			$this->error('页面不存在');
		}
		if (!I("post.submit")) 
		{
			return false;
		}
		//这里发送消息，同时设置失效。wait。。。。。
		
		//send message
		$message=I("post.r_reason");
		//然后更改有效位
		//暂时定成“只是发送通知信息，而不考虑更改有效位”
		$pid=I("post.pid");
		$this->success("暂时还没有确定是否发送消息",U("Policy/index"));
		//下面是更改有效位的，为了简化，此操作不再更改，仅仅发送消息
		
		
	}*/
/*
	$type=I('get.type');
	$where['policy_effective']=1;
	if($type!=NULL)
		$where['policy_effective']=$type;
	$name=I('post.sname');
	if($name!=NULL&&$name!='')
	{
		$where=array('policy_title'=>array('LIKE','%'.$name.'%'));
	}
	$Policy=M('policy');
	$count=$Policy->where($where)->count();
	$Page=new \Org\Util\Page($count,25);
	$show=$Page->show();
	$list=$Policy->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
	$this->assign('list',$list);
	$this->assign('page',$show);
	$this->display();*/