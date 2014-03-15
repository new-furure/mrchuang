<?php
/*
+---------------------------------------------------
+后台模块
+首页模块
+功能：
+1、展示首页
+2、管理左菜单和顶部菜单。
+3、退出登录
+4、更改个人密码
+5、显示服务器信息
+2014/1/24
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index()//首页
	{
		$this->display();
	}
	public function top()//top menu
	{
		$topmenu=array(
    		 array('name'=>'会员管理','url'=>U('Admin/Index/left',array('type'=>1))),
    		 array('name'=>'内容管理','url'=>U('Admin/Index/left',array('type'=>2))),
    		 array('name'=>'消息管理','url'=>U('Admin/Index/left',array('type'=>3))),
    		 array('name'=>'系统管理','url'=>U('Admin/Index/left',array('type'=>4))),
			 //下面一行删掉就行，只是为了debug显示方便，直接忽略即可
			 array('name'=>'系统管理','url'=>U('Admin/Index/left',array('type'=>4))),
    		);
    	$this->assign('topmenu',$topmenu);
    	$this->display();
	}
	public function left()
	{
		$type=I('get.type');
		if($type==NULL)
			$type=1;
		switch($type)
		{
			//对应顶部“会员管理”
			case 1:
				$menu=
					array(
						array(
							'name'=>'前台会员管理',
							'menu'=>array(
										array('name'=>'个人用户列表','url'=>U('User/index')),
										array('name'=>'组织用户列表','url'=>U('User/org')),
										array('name'=>'添加组织用户','url'=>U('User/addUser')),
									),
						),
						array(
							'name'=>'后台会员管理',
							'menu'=>array(
										array('name'=>'显示管理员列表','url'=>U('Admin/index')),
										array('name'=>'添加管理员','url'=>U('Admin/addAdmin')),
										array('name'=>'添加新控制器权限','url'=>U('Admin/addPri')),
									),
						),
					);
				break;
			//对应顶部“内容管理”
			case 2:
				$menu=
					array(
						array(
							'name'=>'项目管理',
							'menu'=>array(
										array('name'=>'项目列表','url'=>U('Project/index')),
										array('name'=>'完善列表','url'=>U('Project/comment',array('type'=>1))),
										array('name'=>'评论列表','url'=>U('Project/comment',array('type'=>2))),
										array('name'=>'完善&评论回复','url'=>U('Project/scomment')),
									),
						),
						array(
							'name'=>'政策管理',
							'menu'=>array(
										array('name'=>'政策列表','url'=>U('Policy/index')),
										array('name'=>'评论列表','url'=>U('Policy/comment')),
										array('name'=>'评论回复','url'=>U('Policy/scomment')),
										
									),
						),
						array(
							'name'=>'问答管理',
							'menu'=>array(
										array('name'=>'问题列表','url'=>U('Question/question')),
										array('name'=>'回答列表','url'=>U('Question/answer')),
										array('name'=>'回答回复','url'=>U('Question/scomment')),
									),
						),
						array(
							'name'=>'圈子管理',
							'menu'=>array(
										array('name'=>'圈子管理','url'=>U('Circle/index')),
										array('name'=>'帖子管理','url'=>U('Circle/postList')),
										array('name'=>'帖子评论','url'=>U('Circle/comment')),
										array('name'=>'评论回复','url'=>U('Circle/scomment')),
									),
						),
						array(
							'name'=>'标签管理',
							'menu'=>array(
										array('name'=>'显示标签列表','url'=>U('Tag/index')),
										array('name'=>'添加标签','url'=>U('Tag/addTag')),
									),
						),
					);
				break;
			//对应顶部“消息管理”
			case 3:
				$menu=
					array(
						array(
							'name'=>'垃圾信息管理',
							'menu'=>array(
										array('name'=>'被举报文章','url'=>U('Filter/reportArticle')),
										array('name'=>'被举报评论','url'=>U('Filter/reportComment')),
										array('name'=>'被举报回复','url'=>U('Filter/reportSComment')),										
									),
						),
						array(
							'name'=>'系统通知管理',
							'menu'=>array(
										array('name'=>'历史群组消息','url'=>U('Notice/noticeList')),
										array('name'=>'发送群组消息','url'=>U('Notice/sendGroup')),
										array('name'=>'发送个人消息','url'=>U('Notice/sendPerson')),
									),
						),
					);
				break;
			//对应顶部“系统管理”
			case 4:
				$menu=
					array(
						array(
							'name'=>'统计信息',
							'menu'=>array(
										array('name'=>'用户统计','url'=>U('Statistics/userData')),
										array('name'=>'项目统计','url'=>U('Statistics/projectData')),
										array('name'=>'政策统计','url'=>U('Statistics/policyData')),
										array('name'=>'问答统计','url'=>U('Statistics/qaData')),
										array('name'=>'百度统计','url'=>U('Statistics/baiduStat')),
									),
						),
						array(
							'name'=>'系统配置',
							'menu'=>array(
										array('name'=>'显示配置列表','url'=>U('Config/index')),
										array('name'=>'添加配置项','url'=>U('Config/addConfig')),
										//array('name'=>'写入配置文件','url'=>U('Config/rewriteConfig')),
									),
						),
						array(
							'name'=>'常用操作',
							'menu'=>array(
										array('name'=>'清除缓存','url'=>U('Cache/index')),
										array('name'=>'邀请码管理','url'=>U('Invitation/index')),
										//array('name'=>'数据库管理','url'=>''),
										//array('name'=>'日志管理','url'=>''),
									),
						),
					);
				break;
			default:break;
		}
		$this->assign('menu',$menu);
		$this->display();
	}
	public function main()
	{
		//获取服务器信息
        $sysdata['sysos']=$_SERVER['SERVER_SOFTWARE']; //获取服务器标识的字串
        $sysdata['sysversion']=PHP_VERSION; //获取PHP服务器版本
        mysql_connect(C('DB_HOST'), C('DB_USER'), C('DB_PWD'));
        $sysdata['mysqlinfo'] = mysql_get_server_info();
        //从服务器中获取GD库的信息
        if(function_exists("gd_info")){ 
        $gd = gd_info();
        $sysdata['gdinfo']=$gd['GD Version'];
        }else {
        $sysdata['gdinfo']="未知";
        }
        //从GD库中查看是否支持FreeType字体
        $sysdata['freetype']=$gd["FreeType Support"] ? "支持" : "不支持";
        //从PHP配置文件中获得是否可以远程文件获取
        $sysdata['allowurl']=ini_get("allow_url_fopen") ? "支持" : "不支持";
        //从PHP配置文件中获得最大上传限制
        $sysdata['max_upload']=ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled";
        //从PHP配置文件中获得脚本的最大执行时间
        $sysdata['max_ex_time']=ini_get("max_execution_time")."秒";
        //以下两条获取服务器时间，中国大陆采用的是东八区的时间,设置时区写成Etc/GMT-8
        date_default_timezone_set("Etc/GMT-8");
        $sysdata['systemtime']=date("Y-m-d H:i:s",time()); 
        $this->assign('sysdata',$sysdata);
    	$this->display();
	}
	//退出登录
	public function logout()
	{
		session(null); 
		$this->redirect('Login/index');
	}
	//修改自己的密码
	public function editPW()
	{
		$this->display();
	}
	//测试模板专用。
	public function test()
	{
		$this->display();
	}
	//处理修改密码请求。
	public function runEditPW()
	{
		if (!IS_POST) 
		{
			$this->error('页面不存在');
		}
		if (!I("post.submit")) 
		{
			return false;
		}
		if(I('post.newpasswd')!=I('post.repasswd'))
			$this->error("重复密码错误");
		$Admin=M('admin');
		$data['admin_id']=I('session.admin_id');
		$record=$Admin->where($data)->find();
		if($record['admin_passwd']!=md5(I('post.oldpasswd')))
			$this->error("输入原来密码错误");
		$data['admin_passwd']=md5(I('post.newpasswd'));
		if($Admin->save($data)===false)
			$this->error("修改密码失败，请重试");
		else
			$this->success("修改密码成功",U(Index/main));
	}
}