<?php
/*
+---------------------------------------------------
+后台模块
+项目管理模块
+功能：
+1、显示项目列表
+2、删除
+3、通知修改。
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class ProjectController extends CommonController {
	//首页，显示项目列表
	public function index()
	{
		$where['article_effective']=1;
		$where['article_type']=C('PROJECT_TYPE');
		$where['article_draft']=0;
		$name=I('post.sname');
		if($name!=NULL&&$name!='')
		{
			$where['article_title']=array('LIKE','%'.$name.'%');
		}
		$Project=D('ProjectView');
		$count=$Project->where($where)->count();
		$Page=new \Org\Util\PageW($count,10);
		$show=$Page->show();
		$list=$Project->where($where)->order('article_time desc')//这个地方不用做field限制，因为在modelview中已经定义了。
		->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($list);
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	public function detailProject()
	{
		//次函数应该需要调用前台的显示项目的效果。
		$pid=I('get.id');
		$url=get_url_by_id($pid);
		$this->redirect($url);
	}
	public function delProject()
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
	
	public function comment()//wait：评论是有完善和评论之分的！
	{
		$type=I('get.type');
		$this->redirect('Comment/comment',array('type'=>$type));
		//因为涉及权限问题，所以不能直接访问comment控制器，而应该从这里跳转。
		/*$name=I('post.sname');
		$where['comment_effective']='1';
		$where['article_type']=C('PROJECT_TYPE');//具体是几呢，现在不确定，，，wait！！
		$where['comment_type']=C('PROJECT_COMMENT');
		$type=I('get.type');
		switch($type)
		{
			case 1:$where['comment_type']=C('PROJECT_IMPROVE');break;//improvement
			case 2:$where['comment_type']=C('PROJECT_COMMENT');break;
			default:break;
		}
		if($name!=NULL&&$name!='')
			$where['comment_content|article_title']=array('LIKE','%'.$name.'%');
		$Comment=D('CommentView');//因为回答属于comment
		$count=$Comment->where($where)->count();
		$Page= new \Org\Util\Page($count,25);
		$show= $Page->show();
		$list= $Comment->where($where)->order('comment_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('type',$type);
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
		$this->redirect('Comment/scomment',array('type'=>1));
		/*$name=I('post.sname');
		$where['article_type']=C('PROJECT_TYPE');//具体是几呢，现在不确定，，，wait！！
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
//以下为注释
/*//最好是明确用left join，此处注释掉为老式写法。
	$Model = new \Think\Model();
	$list=$Model->table('project,workgroup,user,enterprise')
->where('project.workgroup_id=workgroup.workgroup_id and project.user_id=user.user_id and project.enterprise_id=enterprise.enterprise_id')
->select();
	*/
	//现在遇到的问题是，join的时候，遇到的很多都是重复的数据，所以很多没法使用！！！
	//所以只能先用别名来做了。但其实我在这并不需要显示太多，甚至这些都不做都可以的！反正审核都是要看详细的，这里只是一个列表而已。
	//这个地方真的听繁琐的！如果可以的话，显示列表就简单一点，不用join也好吧！
	/*//考虑了一下，此处并不用过多的显示，更多放在详细展示里面吧
	如果需要，可以再添加上去。
	$list=$Project->field('project_id as pid,project.user_id as uid,
	project.workgroup_id as wid,project.enterprise_id as eid,
	project_effective,project_title,project_profile,project_time,
	user.user_name,workgroup.workgroup_name,enterprise.enterprise_title')
	->join('user ON project.user_id=user.user_id','LEFT')
	->join('workgroup ON project.workgroup_id=workgroup.workgroup_id','LEFT')
	->join('enterprise ON project.enterprise_id=enterprise.enterprise_id','LEFT')
	->where($where)->select();
	$this->assign('list',$list);
	$this->assign('type',$type);
	$this->display();
	*/
	/*public function passProject()
	{
		//直接更改成功即可
		$pid=I('get.id');
		if($pid==NULL)
			$this->error("not exist");
		$data['project_id']=$pid;
		$data['project_effective']=1;
		if(M('project')->save($data)!=false)//如果没有更改，返回的是0；
			$this->success("审核通过成功");
		else
			$this->error("修改失败");
		
	}
	//审核未通过，并利用系统消息留言
	public function rejectProject()
	{
		$pid=I('get.id');
		if($pid==NULL)
			$this->error("not exist");
			//显示页面，添加留言等等
		$record=M('project')->find($pid);
		$this->assign('list',$record);
		$this->display();
	}
	public function runRejectProject()
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
		$pid=I("post.pid");
		$data['project_id']=$pid;
		$data['project_effective']=0;
		if(M('project')->save($data)!=false)
			$this->success("驳回成功",U("Project/index"));
		else 
			$this->error("修改失败",U("Project/index"));
	}
	//没有添加吗？！？！？？！？*/