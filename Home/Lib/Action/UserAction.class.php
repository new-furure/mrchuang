<?php
/*
+---------------------------------------------------
+用户模块
+初稿 NewFuture
+完善 牛亮
+---------------------------------------------------
*/

class UserAction extends Action{


/********************************************************
    登陆 注册 部分
*********************************************************/

//注册页
//@作者：
public function reg()
{	//验证是否已经登陆
	if(I('session.user_id')!='')
	{
		$this->display();
	}
	else
	{
		$this->error("您已登陆",U('User/index'));
	}
}

//注册
//@作者：第一版NewFuture
public function resign()
{
        $nickname=I('post.user_nickname');
        $email=I('post.user_email');
    
        if(!validateEmail($email))
        {
        	$this->error('请输入正确的邮箱');
        	return;
        }

        $User=M('user');
		    $UserRecord=$User->where("user_email='".$email."'")->find();
        if($UserRecord!=null) {
			   $this->error('此邮箱已注册！请登录',U('User/log'));
		    	return;
		}

		$data['user_email']=$email;
		$data['user_nickname']=$nickname;
		$data['user_passwd']=I('post.user_passwd');#二次加密省略
	    
	 $result = $User->add($data);
		
		if(!$result) {
			$this->error('注册失败！');
		}else{
		  session('user_id',$result);#加入session
		$this->success('注册成功！',U('User/log'));
		}
}

//登录页面
//@作者：
public function log()
{
  $id=I('session.user_id');#获取用户id
  if($id!=null)
  {
    $this->error("您已经登陆",U('User/index'));
  }else
  {
    $this->display();
  }
}

//登陆
//@作者 第一版NewFuture
public function login()
{
   $email=I('post.user_email');
	 $User=M('user');
	 $UserRecord=$User->where("user_email='".$email."'")->find();
	  if($UserRecord!=null)//验证邮箱
	  {
			$passwd=I('post.user_passwd');#二次加密省略
			if($passwd!=$UserRecord['user_passwd']) 
				{
	   	session('user_id',$UserRecord['user_id']);
	   	$this->success('登录成功！',U('User/index'));
		return;
    	}
	  }
			$this->waitSecond=20;
			$this->error('邮箱或者密码错误！');
		 
}

//登出
//@作者：
public function logout()
{
	$session['user_id']='';
  $this->success('已经成功注销！',U('Index/index'));
}

//验证email合法性
//@作者 NewFuture copy from internet
//可移动公用函数
function validateEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}


/********************************************************
    用户信息
*********************************************************/

//用户主页 
//@作者：
public function index()
{

 $id=I('session.user_id');#获取用户id
  
  if($id!='')#用户登录
  {

  }
  else
  {
    $this->error("您尙未登陆！",U('User/log'));
  }

}

//用户信息
//自己查看和他人查看不同
//建议通过get参数id判断
//@作者：
public function info($id=0)
{

} 


//资料卡模板
//建议写在公共库中



/*****************************************
认证 部分
*******************************************/

//个人认证页面
//@作者：
public function p_verify()
{
  
}

//认证信息提交
//@作者：
public function p_verify_submit()
{

}

//公司认证页
//@作者：
public function c_verify()
{

}

//公司认证提交
//@作者：
public function c_verify_submit()
{

}

}