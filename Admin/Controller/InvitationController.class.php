<?php
/*
+---------------------------------------------------
+后台模块
+邀请码管理
+功能：
+1、生成验证码
+2、导出验证码
+3、
+
+****************************************
+请注意：
+如果需要设置邀请码模块的权限管理
+需要在后台管理员模块中选择“添加新控制器权限”，
+按照说明分别添加“Invitation”，“邀请码模块”两个数据。
+************************************************
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class InvitationController extends CommonController {
//显示邀请码列表
	public function index()
	{
		$Notice=M('invitation');
		$type=I('get.type');
		if($type==1)
			$where['invitation_effective']=1;
		else
			$where['invitation_effective']=0;
		$count=$Notice->where($where)->count();
		$Page=new \Org\Util\PageW($count,25);
		$show=$Page->show();
		$list=$Notice->where($where)->order('invitation_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();	
	}
	//给一个邮箱发送一个邀请码
	public function sendOne()
	{
		$email=I('post.email');
		$msg=I('post.msg');
		$code=invite();
		$msg.='<a href="http://'.I('server.HTTP_HOST').U('Home/User/reg',"invite_code=$code").'">'.$code."</a>";
		if(send_mail($email,C("SEND_INVITE_CODE"),$msg))
			$this->success("邀请码:".$code."发送成功",U('Invitation/index'),20);
	}
	//批量生成。
	public function createMany()
	{
		$num=I('post.num');
		if($num>100)
		{
			$this->error("超出上限100个");
		}
		$email=I('post.email');
		$msg="<br>共生成".$num."个邀请码：<br>";
		for($i=0;$i<$num;$i++)
		{
			$code=invite();
			$msg.='<a href="http://'.I('server.HTTP_HOST').U('Home/User/reg',"invite_code=$code").'">'.$code."</a><br>";
		}
		if(send_mail($email,C("SEND_INVITE_CODE"),$msg))
			$this->success("邀请码发送成功",U('Invitation/index'),20);
	}
}