<?php
/*
+---------------------------------------------------
+后台模块
+前台用户管理模块
+功能：
+1、显示个人用户/组织用户列表
+2、添加组织
+3、认证组织/取消认证
+4、
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class UserController extends CommonController {
	public function index()//首页个人用户
	{
		//$type=I('get.type');//user_type改动，判断不方便，不再分类显示。
		$name=I('post.sname');
		$where['user_effective']='1';
		/*switch($type)
		{
			case 1:$where['user_type']=0;break;
			case 2:$where['user_type']=1;break;
			default:break;//显示全部
		}*/
		if($name!=NULL&&$name!='')//搜索用户，应该放在最后
			$where=array('user_nickname|user_email'=>array('LIKE','%'.$name.'%'),'user_effective'=>'1');//无论验证，都搜索
		$Person=D('PersonView');
		//dump($where);
		$count=$Person->where($where)->count();//
		import('ORG.Util.Page');
		$Page= new \Think\Page($count,25);
		$show= $Page->show();
		$list= $Person->field('user_id,user_nickname,user_type,user_email,user_time')
		->where($where)->order('user_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($list);
		//$type=array();
		//$type[0]="未验证";
		//$type[1]="已验证";
		$this->assign('list',$list);
		$this->assign('page',$show);
		//$this->assign('type',$type);
		$this->display();
	}
	public function org()
	{
		$type=I('get.type');
		$name=I('post.sname');
		$where['user_effective']='1';
		switch($type)//注意一个问题，就是当并没有get.type的时候，用switch的话对应的就是0
		//所以如果下面有case 0；的话，就要注意。
		{
			case 1:$where['organization_certified']=C('UNCERTIFY');break;
			case 2:$where['organization_certified']=C('CERTIFING');break;
			case 3:$where['organization_certified']=C('CERTIFIED');break;
			default:break;//显示全部
		}
		if($name!=NULL&&$name!='')
			$where=array('user_nickname|user_email'=>array('LIKE','%'.$name.'%'),'user_effective'=>'1');//无论验证，都搜索
		$Organization=D('OrganizationView');
		$count=$Organization->where($where)->count();
		$Page = new \Org\Util\PageW($count,10);
		$show= $Page->show();
		$list= $Organization->field('user_id,user_nickname,user_email,category_id,organization_certification_infomation,organization_certified,user_type')
		->where($where)->order('user_time desc')
		->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($list);
		$type=array();
		$type[0]="未认证";
		$type[1]="请求认证";
		$type[2]="已认证";
		$carr=M('category')->getField('category_id,category_name');
		$this->assign('carr',$carr);
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->assign('type',$type);
		$this->display();
	}
	//通过认证，，还需要发送一条系统消息给用户。（涉及权限问题，所以发送消息应该作为一个公用的函数）
	public function certify()
	{
		$userid=I('get.id');
		$Organization=M('organization');
		$cate=$Organization->where(array('user_id'=>$userid))->getField('category_id');
		if($cate==1)//如果是项目认证，则要升级为认证公司。
		{
			//$data=array('organization_certified'=>C('CERTIFIED'),'category_id'=>C('ENTERPRISE'));
			$data['organization_certified']=C('CERTIFIED');
			$data['category_id']=C('ENTERPRISE');
			if($Organization->where(array('user_id'=>$userid))->setField($data))
			{
				//下面还是要改一下user_type
				$usertype=M('user')->where(array('user_id'=>$userid))->getField('user_type');
				set_user_type($usertype,null,1,C("ENTERPRISE"));
				if(M('user')->where(array('user_id'=>$userid))->setField('user_type',$usertype))
				{
					$this->success("认证成功");
					$this->sendNotice($userid,"恭喜您认证成功，并升级为认证公司");
				}
				else
				{
					//恢复上一步的操作。
					$data['organization_certified']=C('UNCERTIFY');
					$data['category_id']=C('STARTUP');
					$Organization->where(array('user_id'=>$userid))->setField($data);
					$this->error("认证失败，请重试1");
				}
			}
			else
				$this->error("认证失败，请重试2");
		}
		else//如果是其他的认证，直接设置认证为认证即可。
		{
			$datahere['organization_certified']=C('CERTIFIED');
			if($Organization->where(array('user_id'=>$userid))->setField($datahere))
			{
				$this->success("认证成功");
				$this->sendNotice($userid,"恭喜您认证成功");
			}
			//认证成功之后，是不是应该有个动态消息或者系统提醒之类的？？？？？
			else
				$this->success("认证失败，请重试3");
		}	
	}
	public function uncertify()
	{
		$userid=I('get.id');
		$Organization=M('organization');
		$cert=$Organization->where(array('user_id'=>$userid))->getField('organization_certified');
		if($cert==C('UNCERTIFY'))
			$this->error("未认证，无法取消认证");
		if($cert==C('CERTIFING'))//如果是处理申请，直接设置为0，并加系统消息即可。
		{
			if($Organization->where(array('user_id'=>$userid))->setField('organization_certified',C('UNCERTIFY')))
			{
				$this->success("驳回申请成功");
				$this->sendNotice($userid,"您的申请认证被管理员驳回");
			}
			else
				$this->success("驳回失败，请重试");
		}
		else//如果不是处理申请，就是取消认证，则需要判断一下
		{
			$cate=$Organization->where(array('user_id'=>$userid))->getField('category_id');
			if($cate==C('ENTERPRISE'))
			{
				$data=array('organization_certified'=>C('UNCERTIFY'),'category_id'=>C('STARTUP'));
				if($Organization->where(array('user_id'=>$userid))->setField($data))
				{
					$usertype=M('user')->where(array('user_id'=>$userid))->getField('user_type');
					set_user_type($usertype,null,1,C("STARTUP"));
					if(M('user')->where(array('user_id'=>$userid))->setField('user_type',$usertype))
					{
						$this->success("取消认证成功");
						$this->sendNotice($userid,"您的认证被管理员取消，并降级成为项目团队");
					}
					else
					{
						//恢复上一步的操作。
						$data1['organization_certified']=C('CERTIFIED');
						$data1['category_id']=C('ENTERPRISE');
						$Organization->where(array('user_id'=>$userid))->setField($data1);
						$this->error("认证失败，请重试");
					}
				}
				else
					$this->success("取消认证失败，请重试");
			}
			else
			{
				if($Organization->where(array('user_id'=>$userid))->setField('organization_certified',C('UNCERTIFY')))
				{
					$this->success("取消认证成功");
					$this->sendNotice($userid,"您的认证被管理员取消");
				}
				else
					$this->success("取消认证失败，请重试");
			}
			
		}
	}
	public function delUser()//根据get.id删除用户
	{
		$userid=I('get.id');
		if(M('user')->where(array('user_id'=>$userid))->setField('user_effective','0'))
			$this->success("删除成功");
		else
			$this->error("删除失败，请重试");
	}
	public function addUser()//显示添加用户界面.......另外，有添加就必须要有更改，因为很可能添加的时候出现错误。
	{
		$carr=M('category')->getField('category_id,category_name');
		$this->assign('carr',$carr);
		$this->display();
	}
	//添加用户完成以后，应该要发送一个需要验证之类的东西，现在还没有做，是邮箱验证吗？？
	//wait。。。。。。验证问题还没有解决。
	public function runAddUser()//处理添加普通用户数据
	{
		if (!IS_POST) 
		{
			$this->error('页面不存在');
		}
		if (!I("post.submit")) 
		{
			return false;
		}
		//先添加到user表中，然后再添加到organization表中。
		$User=D('Home/User');//利用已经写的model
		$data['user_nickname']=I('post.username');
		$data['user_passwd']=md5(I('post.passwd'));
		$data['user_email']=I('post.email');
		$category=I('post.category');
		set_user_type($data['user_type'],0,C("IS_ORG_ON"),$category);

		if($User->create($data))
		{    
			$result = $User->add();  
			if($result)
			{
				$insertid=$result;//现在再加入到organization，
				$data2['user_id']=$insertid;

				if(C('IS_ORG_ON'))
				{//启用组织注册

				$tableName='organization';

				
				$data2['category_id']=I('post.category');
				$data2['organization_certification_infomation']=I('post.info');
				$data2['organization_certified']=C('UNCERTIFY');//后台添加是时候默认不认证的。
				//如果添加的时候默认是认证的话，那么还要注意添加的如果是创业团队的，还要多一步把它升级为认证公司
				}else{
					$tableName='Person';
				
				}
				if(M($tableName)->add($data2))
				{
					//添加用户之后要生成一个验证URL，然后发送到用户邮箱，等待邮箱激活
					//validate()函数在function.php中。
					$key=validate($insertid);
					$url="http://". I( 'server.HTTP_HOST' ).U("/Home/User/validate","id=$insertid&key=$key&type=".C('INVITE_ORG_MAIL'));
					send_mail($data['user_email'],C('INVITE_ORG_MAIL'),'<a href="'.$url.'">'.$url.'</a>');
					//此处需要发送邮件的函数
					$this->success("添加成功");
				}
				else{
					M('user')->delete($insertid);//注意删除！
					$this->error("添加失败");
				}
			}
			else
				$this->error("添加失败");
		}
		else
		{
			$this->error($User->getError());
		}
	}
	public function sendNotice($userid,$msg)
	{
		//然后添加到系统消息中
		$data['user_id']=$userid;
		$data['notice_title']=$msg;
		$data['notice_content']=$msg;
		$data['notice_type']=51;////////此处未知消息类型，待修改。
		M('notice')->add($data);
	}
	/*public function sendNotice()
	{
		$id=I('get.id');
		$record=M('user')->find($id);
		if($record==NULL)
		{
			$this->error("该用户不存在");
		}
		else
		{
			$this->assign('list',$record);
			$this->display();
		}
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
		I('post.content');
		I('post.type');
		//把通知放在私信表里面，
		$this->error("需要把内容插入到私信表中，暂时还没有做，因为私信表没有确定。。wait");
	}*/
	/*public function passUser()
	{
		$data['user_id']=I('get.id');
		$cert=I('get.cert');
		switch($cert)
		{
			case 0:$data['user_certified']=1;break;
			case 1:$data['user_certified']=0;break;
		}
		$msg=$data['user_certified']?'通过':'驳回';
		if(M('user')->save($data))
			$this->success($msg."成功");
		else
			$this->error($msg."失败，请重试");
	}
	public function lockUser()//相当于编辑
	{
		$data['user_id']=I('get.id');//这个地方暂时还不确定是get还是post。
		$lock=I('get.lock');//传过来的是当前的effective
		switch($lock)
		{
			case 0:$data['user_effective']=1;break;
			case 1:$data['user_effective']=0;break;
		}
		$msg=$data['user_effective']?'解锁':'锁定';
		if(M('user')->save($data))
			$this->success($msg."成功",U('index'));
		else
			$this->error($msg."失败，请重试",U('index'));
	}
	public function detailUser()
	{
		$userid=I('get.id');
		$record=M('user')->find($userid);
		$this->assign('list',$record);
		$this->display();
	}*/
}