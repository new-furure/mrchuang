<?php
/*
+---------------------------------------------------
+后台模块
+标签管理模块
+功能：
+1、显示标签
+2、搜索标签
+3、删除标签
+4、添加标签
+5、编辑标签（例如修改标签热度等）
+
+最后修改2014、2、17
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class TagController extends CommonController {
	//首页，显示标签列表
	public function index()
	{
		$Tag=M('tag');
		$name=I('post.sname');
		$where=array();
		//如果需要搜索标签
		if($name!=NULL&&$name!='')
		{
			$where=array('tag_title'=>array('LIKE','%'.$name.'%'));
		}
		$count=$Tag->where($where)->count();
		$Page=new \Org\Util\PageW($count,25);
		$show=$Page->show();
		$list=$Tag->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	//删除标签，数据表中没有设计有效位，所以暂定为直接删除！
	public function deleteTag()
	{
		$id=I('get.id');
		if(M('tag')->delete($id)!==false)
			$this->success("删除成功");
		else
			$this->error("删除失败");
	}
	//添加标签，直接显示
	public function addTag()
	{
		$this->display();
	}
	//添加标签处理
	public function runAddTag()
	{
		if(!IS_POST) 
		{
			$this->error('页面不存在');
		}
		if(!I("post.submit")) 
		{
			return false;
		}
		$data['tag_title']=I('post.title');
		$data['tag_profile']=I('post.profile');
		$data['tag_hits']=I('post.hits');
		if(M('tag')->add($data))
			$this->success("添加成功");
		else
			$this->error("添加失败");
	}
	public function editTag()
	{
		$id=I('get.id');
		$record=M('tag')->find($id);
		$this->assign('list',$record);
		$this->display();
	}
	public function runEditTag()
	{
		if(!IS_POST) 
		{
			$this->error('页面不存在');
		}
		if(!I("post.submit")) 
		{
			return false;
		}
		//判断合法性：拜托，你是管理员，不会瞎弄数据的吧，瞎弄了也可以删掉。
		$data['tag_id']=I('post.id');
		$data['tag_title']=I('post.title');
		$data['tag_profile']=I('post.profile');
		$data['tag_hits']=I('post.hits');
		if(M('tag')->save($data)!==false)
			$this->success("修改成功");
		else
			$this->error("修改失败");
	}
}