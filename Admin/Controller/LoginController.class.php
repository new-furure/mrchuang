<?php
/*
+---------------------------------------------------
+后台模块
+登录模块
+功能说明：
+1、显示登录页面
+2、登录成功后跳转至index。
+
+2014、2、16最后更改
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
	public function index()//显示登录界面
	{
		$this->display();
	}
	public function runLogin()//处理登录post
	{
		if (!IS_POST) 
		{
			$this->error('页面不存在');
		}
		/*if (!I("post.submit")) 
		{
			return false;
		}*/
		//使用验证码功能。
		$verify = new \Think\Verify();    
		if(!$verify->check(I('post.code')))
			$this->error('验证码错误');
		$where['admin_name']=I('post.username');
		$passwd=md5(I('post.passwd'));//md5处理
		$Admin=M('Admin');
		$record=$Admin->where($where)->find();
		if(!$record||$passwd!=$record['admin_passwd'])
		{
			$this->error("用户名或密码错误");
		}
		if($record['admin_privilege'][0]<1)
		{
			$this->error("用户无后台权限");
		}
		//更新管理员最后登录时间
		$data=array(
			'admin_id'=>$record['admin_id'],
			'admin_time'=>date('Y-m-d H:i:s',time())
		);
		if($Admin->save($data)===false)
			$this->error(date('Y-m-d H:i:s',time()));;
		
		session("admin_id",$record['admin_id']);
		session("admin_name",$record['admin_name']);
		$this->success("登录成功.",U("Index/index"));
	}
	
}