<?php
/*
+---------------------------------------------------
+后台模块
+后台基类，除login模块外都需继承该控制器。
+功能：
+1、根据模块自动判断权限
+2、判断是否为超级管理员
+
+2014、2、16最后更改
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize()//初始化函数，经亲手测试，该函数是调用每个controller下的每一个动作前都会去调用
	{
		$this->super=false;
		//确保登录session
		$where['admin_id']=I('session.admin_id');
		$where['admin_name']=I('session.admin_name');
		if($where['admin_id']==NULL||$where['admin_name']==NULL)
		{
			$this->redirect('Login/index');
		}
		//判断权限。
		$Admin=M('admin');
		$record=$Admin->where($where)->find();
		if($record==NULL)
			$this->error("该用户不存在",U("Login/index"));
		//$this->priArr=explode(":",$record['admin_privilege']);//格式类似2:1:1:0:0。。。。
		$this->priArr=$record['admin_privilege'];//再简化一下，直接21100。。。
		$prineed=0;//当前模块所需要的权限
		$privilege=M('config')->where(array('config_name'=>'admin_privilege'))->getField('config_content');
		$privilege=explode('-',$privilege);
		//dump($privilege);
		$prineed=array_keys($privilege,CONTROLLER_NAME,true);//注意这里返回的数组，找到对应的位数
		/*switch(CONTROLLER_NAME){
			//case Admin: $prineed=1;break;
			case User: $prineed=1;break;//此处的prineed=1，1指的是“2111111”的第1位。该位为1则为有权限。
			case Project: $prineed=2;break;
			case Policy: $prineed=3;break;
			case Filter: $prineed=4;break;
			case Notice: $prineed=5;break;
			case Statistics: $prineed=6;break;
			default:$prineed=0;break;//如果不是这六个模块，先暂定只要是管理员即可
		}*/
		//$this->priArr[0]==0,用于判断该用户时候已经被删除所有权限。
		if($this->priArr[0]==0)
			$this->error("该管理员已被删除",U('Index/index'));
		if($this->priArr[0]!=2&&$this->priArr[$prineed[0]]!=1)//后面这个选项不要用==0判断，用！=1判断好一点。
			$this->error("用户无此模块权限..",U('Index/index'));
		if($this->priArr[0]==2)
			$this->super=true;
	}
	public function needSuper()//判断是否为超级管理员
	{
		if(!$this->super)
			$this->error("不是超级管理员",U('Index/index'));
	}
}