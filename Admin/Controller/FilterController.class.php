<?php
/*
+---------------------------------------------------
+后台模块
+垃圾信息处理模块
+功能：
+1、显示举报信息（分为文章和评论、回复）
+2、忽略该举报
+3、删除被举报内容
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class FilterController extends CommonController {
	public function reportArticle()//举报文章这块已经简答的测试过了，下面评论的则还没有测试。
	{
		$where['article_effective']=1;
		$RArticle=D('RArticleView');
		$count=$RArticle->where($where)->count();
		$Page=new \Org\Util\PageW($count,25);
		$show=$Page->show();
		$list=$RArticle->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	public function reportComment()
	{
		$where['comment_effective']=1;
		$RComment=D('RCommentView');
		$count=$RComment->where($where)->count();
		$Page=new \Org\Util\PageW($count,25);
		$show=$Page->show();
		$list=$RComment->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	public function reportSComment()
	{
		$RSComment=D('RSCommentView');
		$count=$RSComment->where($where)->count();
		$Page=new \Org\Util\PageW($count,25);
		$show=$Page->show();
		$list=$RSComment->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	public function runPost()
	{
		if (!IS_POST) 
		{
			$this->error('页面不存在');
		}
		$delarr=I('post.delarr');
		if(I('post.type')==1)
			$this->delReportA($delarr);
		else if(I('post.type')==2)
			$this->delReportC($delarr);
		else
			$this->delReportS($delarr);
	}
	public function delReportA($arr)
	{
		foreach($arr as $key=>$value)
		{
		
			$value=explode("-",$value);
			M('report_article')->where(array('user_id'=>$value[0],'article_id'=>$value[1]))->delete();
		}
		$this->success("忽略成功");
	}
	public function delReportC($arr)
	{
		foreach($arr as $key=>$value)
		{
			$value=explode("-",$value);
			M('report_comment')->where(array('user_id'=>$value[0],'comment_id'=>$value[1]))->delete();
		}
		$this->success("忽略成功");
		//其实感觉忽略一个举报的话，是否可以就忽略所有举报该文章的举报呢？？？
		//////////////////////////wait.......
	}
	public function delReportS($arr)
	{
		foreach($arr as $key=>$value)
		{
			$value=explode("-",$value);
			M('report_second_comment')->where(array('user_id'=>$value[0],'second_comment_id'=>$value[1]))->delete();
		}
		$this->success("忽略成功");
	}
	public function delArticle()
	{
		$where['article_id']=I('get.id');
		if(M('article')->where($where)->setField('article_effective','0'))
			$this->success("删除文章成功");
		else
			$this->error("删除文章失败");
		//删除无效的
		M('report_article')->where($where)->delete();
		$record=M('article')->field('user_id,article_title')->where($where)->find();
		$data['user_id']=$record['user_id'];
		$data['notice_content']="您的文章“".$record['article_title']."”已被管理员删除";
		$data['notice_title']=$data['notcie_content'];
		$data['notice_type']=51;//此处未定，，，，消息的类型未知有哪些。
		M('notcie')->add($data);
	}
	public function delComment()
	{
		$where['comment_id']=I('get.id');
		if(M('comment')->where($where)->setField('comment_effective','0'))
			$this->success("删除评论成功");
		else
			$this->error("删除评论失败");
		M('report_comment')->where($where)->delete();
		$record=M('comment')->field('comment_content,user_id')->where($where)->find();
		$data['user_id']=$record['user_id'];
		$data['notice_content']="您的评论“".substr($record['comment_content'],0,10)."...”已被管理员删除";
		$data['notice_title']=$data['notice_content'];
		$data['notice_type']=51;//此处未定，，，，消息的类型未知有哪些。
		M('notcie')->add($data);
	}
	public function delSComment()
	{
		$where['second_comment_id']=I('get.id');
		if(M('comment')->where($where)->delete())
			$this->success("删除回复成功");
		else
			$this->error("删除回复失败");
		M('report_second_comment')->where($where)->delete();
		$record=M('second_comment')->field('second_comment_content,user_id')->where($where)->find();
		$data['user_id']=$record['user_id'];
		$data['notice_content']="您的回复“".substr($record['comment_content'],0,10)."...”已被管理员删除";
		$data['notice_title']=$data['notice_content'];
		$data['notice_type']=51;//此处未定，，，，消息的类型未知有哪些。
		M('notcie')->add($data);
	}
	//下面函数负责把无效的report删除掉。//感觉又没有什么必要。。上面函数直接解决掉。//此函数暂时没有作用。
	/*public function depositReport()
	{
		$RArticle=D('RArticleView');//join以后好像不太好删除啊
		$where['article_effective']=0;
		$arr=$RArticle->field('user_id,article_id')->where($where)->select();
		foreach($arr as $key=>$value)
		{
			M('report_article')->where(array('user_id'=>$value['user_id'],'article_id'=>$value['article_id']))->delete();
		}
		
		$RComment=D('RCommentView');//join以后好像不太好删除啊
		$where['comment_effective']=0;
		$arr=$RComment->field('user_id,comment_id')->where($where)->select();
		foreach($arr as $key=>$value)
		{
			M('report_comment')->where(array('user_id'=>$value['user_id'],'comment_id'=>$value['comment_id']))->delete();
		}
	}*/
	/*public function index()
	{
		$type=I('get.type');
		if($type==NULL)
			$type=1;
		$show="";
		switch($type)
		{
			case 1:$tname="answer";$show="";break;
			case 2:$tname="policyanswer";$show="";break;
			case 3:$tname="projectanswer";$show="";break;//前三个都是answer类型，需要$tname."_id"去获取实际内容
			case 4:$tname="answercomment";$show="_content";break;
			case 5:$tname="policyanswercomment";$show="_content";break;
			case 6:$tname="policycomment";$show="_content";break;
			case 8:$tname="projectcomment";$show="_content";break;
			case 9:$tname="projectanswercomment";$show="_content";break;//这几个是评论，直接有内容$tname."_content"。
			case 7:$tname="policyquestion";$show="_title";break;
			case 10:$tname="projectquestion";$show="_title";break;
			case 11:$tname="question";$show="_title";break;//这三个是问题，有问题标题$tname."_title"。
			case 12:$tname="policy";$show="";break;
			case 13:$tname="project";$show="";break;//项目和政策应该不是这个地方可以管的,$tname."_id";
			//或者直接就是给后台该模块的负责人发个消息即可。
			default:$tname="answer";$show="";break;
		}
		$rtname="report_".$tname;
		$table=M($rtname);
		if($show!="")//report表缺少一个主键，也就是id。。。。
			$fieldStr=$rtname."_id,".$rtname."_type,".$rtname."_content,".$tname.".".$tname."_id,".$tname.$show;
		else
			$fieldStr=$rtname."_id,".$rtname."_type,".$rtname."_content,".$tname.".".$tname."_id";
		
		
		$count=$table->count();
		$Page= new \Org\Util\Page($count,25);
		$show= $Page->show();
			
			
			
			
		$list=$table->field($fieldStr)//这么写也挺麻烦的
		->join("$tname ON $rtname.".$tname."_id=$tname.".$tname."_id","LEFT")
		->where(array($tname.'_effective'=>'1'))
		->select();
		echo $table->getLastSql();
		$this->assign('list',$list);
		$this->assign('rtname',$rtname);
		$this->assign('tname',$tname);
		$this->assign('show',$show);
		dump($list);
		$this->display();
	}
	public function detailReport()//未完待续！！！！！！！这个函数还要完善
	{
		$tname=I('get.tname');
		$id=I('get.id');
		switch($tname)
		{
			case 'answercomment':
			case 'policyanswercomment':
			case 'policycomment':
			case 'projectcomment':
			case 'projectanswercomment':$type=1;break;//1为评论类，直接可以看到
			case 'policyquestion':
			case 'projectquestion':
			case 'question':$type=2;break;//question type
			
			default:$type=2;
		}
		if($type==1)
		{
			$record=M($tname)->find($id);
			$this->assign('record',$record);
			$this->assign('tname',$tname);
			$this->display();
		}
		else
		{
			echo "需要调用前台的页面显示，暂且没有做";
		}
		//这个函数要做的是详细显示被举报的内容，分类显示：问题，回答，项目，政策，等等
	}
	public function passReport()//忽略该次举报
	{
		$rid=I('get.rid');
		$tname=I('get.tname');
		$rtname="report_".$tname;
		if(M($rtname)->delete($rid))
			$this->success("忽略成功");
		else
			$this->error("忽略失败");
	}
	public function deleteReport()//删除所举报的内容
	{
		$tname=I('get.tname');
		$id=I('get.id');
		$rid=I('get.rid');
		$rtname="report_".$tname;
		M($rtname)->delete($rid);
		$data[$tname.'_id']=$id;
		$data[$tname.'_effective']='0';
		if(M($tname)->save($data))
			$this->success("删除所举报内容成功");
		else
			$this->error("删除失败");
	}*/
}