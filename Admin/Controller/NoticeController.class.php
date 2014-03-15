<?php
/*
+---------------------------------------------------
+后台模块
+消息模块
+目前设计globalnotice
+功能：
+1、显示globalnotice表的历史消息
+2、发送群组消息
+3、发送个人消息
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class NoticeController extends CommonController {
	public function noticeList()
	{
		$Notice=M('global_notice');
		$where['notice_effective']=1;
		$count=$Notice->where($where)->count();
		$Page=new \Org\Util\PageW($count,25);
		$show=$Page->show();
		$list=$Notice->where($where)->order('notice_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();	
	}
	public function sendGroup()//这里应该是只针对全局类型的消息。
	{
		$this->display();
	}
	public function runSendGroup()//该信息要加在global_notice里面
	{
		if(!IS_POST) 
		{
			$this->error('页面不存在');
		}
		if(!I("post.submit")) 
		{
			return false;
		}
		$data['notice_title']=I('post.title');
		$data['notice_content']=I('post.content');
		$data['notice_type']=51;
		
		//这里还是缺少一个type的区别,就是怎么判断是发送给哪一个分组的!
		if(M('global_notice')->add($data))
		{
			$this->success("消息发送成功");
		}
		else
		{
			$this->error("消息发送失败，请重试");
		}
	}
	public function sendPerson()
	{
		$this->display();
	}
	public function runSendPerson()//该信息要加在notice里面
	{
		if(!IS_POST) 
		{
			$this->error('页面不存在');
		}
		if(!I("post.submit")) 
		{
			return false;
		}
		$data['user_id']=I('post.userid');
		$data['notice_title']=I('post.title');
		$data['notice_content']=I('post.content');
		$data['notice_type']=51;
		if(M('notice')->add($data))
		{
			$this->success("消息发送成功");
		}
		else
		{
			$this->error("消息发送失败，请重试");
		}
	}
	/*public function index()
	{
		$Notice=M('notice');
		$type=I('get.type');
		$name=I('post.sname');
		if($type!=NULL)
		{
			$where['notice_type']=$type;
		}
		if($name!=NULL&&$name!='')//搜索用户，应该放在最后
		{
			$where=array('notice_content'=>array('LIKE','%'.$name.'%'));
		}
		$count=$Notice->where($where)->count();
		$Page=new \Org\Util\Page($count,25);
		$show=$Page->show();
		$list=$Notice->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		//组别分组。
		$c=M('category')->select();
		foreach($c as $value)
		{
			$c_id=$value['Category_ID'];
			$c_name=$value['Category_name'];
			$carr[$c_id]=$c_name;
		}
		$carr[5]="全站";
		$this->assign('carr',$carr);
		//---
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	public function delNotice()
	{
		$nid=I('get.id');
		dump($nid);
		if(M('notice')->delete($nid))
			$this->success("删除成功",U('Notice/index'));
		else
			$this->error("删除失败，请重试",U('Notice/index'));
	}
	public function sendNotice()
	{
		$c=M('category')->select();
		foreach($c as $value)
		{
			$c_id=$value['Category_ID'];
			$c_name=$value['Category_name'];
			$carr[$c_id]=$c_name;
		}
		$carr[5]="全部";
		$this->assign('carr',$carr);
		$this->display();
	}
	public function runSendNotice()
	{
		if(!IS_POST) 
		{
			$this->error('页面不存在');
		}
		if(!I("post.submit")) 
		{
			return false;
		}
		$Notice=M('notice');
		$data['notice_content']=I('post.content');
		$data['notice_type']=I('post.type');
		$data['notice_effective']=1;
		if($Notice->create($data))
		{    
			$result = $Notice->add();  
			if($result)
				$this->success("发送成功",U('Notice/index'));
			else
				$this->error("发送失败",U('Notice/index'));
		}
	}
	public function feedBack()
	{
		//处理反馈的信息。
		$this->show("暂时没有确定反馈信息格式以及处理方式");
	}
	*/
}