<?php
/*
+---------------------------------------------------------------
+	消息模块
+ 初稿：NewFuture
+ 完善
+---------------------------------------------------------------
*/
namespace Home\Controller;
use Think\Controller;
class MessageController extends Controller {

//@作者：夏闪闪
//切换功能选项
public function index(){
	//$user_id=get_id();
	session('user_id',134217729);
	$barName=I('get.bar');
	switch ($barName) {
		case '1':
			$this->index_notice();
			$tplName="index_page_all";
			break;
		case '2':
			$this->list_dialog();
			session('unwatch_message_count',0);
			$tplName="index_page_privateletter";
			break;
		case '3':			
			$this->show_comment_notice($barName);
			session('unwatch_reply_count',0);
			$tplName="index_page_comment";
			break;
		case '4':
			$this->show_focus_notice();
			$tplName="index_page_focus";
			break;
		case '5':
			$this->circle_system_part_notice(5);
			session('unwatch_circle_count',0);
			$tplName="index_page_circle";
			break;
		case '6':
			$this->circle_system_part_notice(6);
			session('unwatch_part_count',0);
			$tplName="index_page_part";
			break;	
		case '7':
			$this->circle_system_part_notice(7);
			session('unwatch_system_count',0);
			$tplName="index_page_system";
			break;	
		default:
			$this->index_notice();
			$tplName="index_page_all";
			break;
	}
	$this->assign('tplName',$tplName);
	$this->display();
}


//发送私信
public function send_message(){
	$acceptUid=I('post.user_id_other');
	$message=I('post.content');
	$Message=D('Message');	
	//上传图片
	if(!empty($_FILES['photo']['name'])){
		$info=upload_file('/Img/Message/','M'.time(), 'photo', 'img');
	    if(!$info) {// 上传错误提示错误信息
	        $this->error("上传文件错误，请重新发送私信！",U('Message/list_dialog'));
	    }
	    $fileinfo="<br/>分享文件".$_FILES['photo']['name']."<a href=''>下载</a>";
	}
	$message.=$fileinfo;
	$return['uid']=$message;
	if($Message->send_message($acceptUid,$message)){
		$return['status']=1;		
	}else{
		$return['status']=0;
	}
	$this->ajaxReturn($return,'JSON');
}
//删除私信
public function delete_message(){
	$user_id=get_id();
	$message_id=I('post.message_id');
	$user_id_other=I('post.user_id_other');	
	$Message=D('Message');
	$message=$Message->where('message_id='.$message_id)->find();
	if($user_id==$message['send_user_id']){
		$data['send_effective']=0;
	}else{
		$data['receive_effective']=0;
	}
	//ajax返回
	if($Message->where('message_id='.$message_id)->save($data)){
		$return['status']=1;		
	}else{
		$return['status']=0;
	}
	$this->ajaxReturn($return,'JSON');
}

//查看私信对话
public function list_dialog_message(){
	$user_id_other=$_GET['user_id_other'];
	$user_name=M('User')->getFieldByUser_id(get_id(),'user_nickname');
	$page=$_GET['p']?$_GET['p']:1;
	$Message=D('Message');
	$Message->read_dialog($user_id_other);

	$msgList=$Message->list_dialog_message($user_id_other);
	$count=count($msgList);//数据集总数
	$Page=new \Org\Util\Page($count,10);
	$show=$Page->show();
	$list=array_slice($msgList, $Page->firstRow, $Page->listRows);

	$this->assign('page',$show);// 赋值分页输出
	$this->assign('list',$list);
	$data['user_id_other']=$user_id_other;
	$data['username']=$user_name;
	$this->assign('data',$data);	
	$this->display('privateletter_detail');
}

//私信对话列表
public function list_dialog(){
	$Dialog=D('Dialog');
	$page=I('get.page');
	$page=$page?$page:1;
	$dialogList=$Dialog->list_dialog();
	$count=count($dialogList);//数据集总数
	$Page=new \Org\Util\Page($count,10);
	$show=$Page->show();
	$list=array_slice($dialogList, $Page->firstRow, $Page->listRows);
	$this->assign('page',$show);// 赋值分页输出
	$this->assign('list',$list);
	//$this->display('index');
}

//删除私信对话
public function delete_dialog(){
	$Dialog=D('Dialog');
	$user_id_other=I('post.user_id_other');
	$user_id=get_id();
	$less=$user_id_other>$user_id?$user_id:$user_id_other;
	$more=$user_id_other<$user_id?$user_id:$user_id_other;
	//$dialog=$Dialog->where('user_id_less='.$less.' and user_id_more='.$more)->find();
	//设置dialog有效位
	if($user_id_other>$user_id){
		$data['dialog_less_effective']=0;
	}else{
		$data['dialog_more_effective']=0;
	}
	$return['status']=0;
	if(!$Dialog->where('user_id_less='.$less.' and user_id_more='.$more)->save($data))
		$this->ajaxReturn($return,'JSON');
	//设置dialog下message有效位
	$Message=D('Message');
	if(!$Message->where('send_user_id='.$user_id.' and receive_user_id='.$user_id_other)->setField('send_effective',0))
		$this->ajaxReturn($return,'JSON');
	if(!$Message->where('send_user_id='.$user_id_other.' and receive_user_id='.$user_id)->setField('receive_effective',0))
		$this->ajaxReturn($return,'JSON');
	$return['status']=1;
	$this->ajaxReturn($return,'JSON');
	//$this->redirect('Message/dialogList?page=0');
}

//屏蔽对话
public function block_dialog(){
	$user_id_other=I('post.user_id_other');
	$user_name=M('User')->getFieldByUser_id($user_id_other,'user_nickname');
	$Block=D('Block');
	$data['user_id']=get_id();
	$data['user_id_blocked']=$user_id_other;
	
	$return['user_id']=$user_id_other;
	$return['username']=$user_name;	
	if($Block->data($data)->add()){
		$return['status']=1;
	}
	else{
		$return['status']=0;
	}
	$this->ajaxReturn($return,'JSON');
}


//评论、完善、回答的提醒
public function show_comment_notice($type){
	$user_id=get_id();
	$notice_type=I('notice_type');
	if($notice_type==null)
		$notice_type=11;
	
	$Notice=D('Notice');
	$Notice->read_notice($notice_type);
	$noticeList=$Notice->table('__NOTICE__ as notice')->join('left join __COMMENT__ as comment on notice.comment_id=comment.comment_id')->join(' left join __USER__ as user on comment.user_id=user.user_id')->join('left join __ARTICLE__ as article on comment.article_id=article.article_id')->where('notice.user_id='.$user_id.' and notice_effective=1 and notice_type='.$notice_type)->order('notice_time desc')->field('notice_id,notice_content,notice_time,notice.user_id as notice_user_id,user.user_nickname as username,user.user_avatar_url,article.article_title,article.article_id,notice.comment_id')->select();

	$count=count($noticeList);//数据集总数
	$Page=new \Org\Util\Page($count,10);
	$show=$Page->show();
	$list=array_slice($noticeList, $Page->firstRow, $Page->listRows);
	$this->assign('list', $list);
	$this->assign('page',$show);
}

//focus的动态
pubLic function show_focus_notice(){
	$Notice=D('Notice');
	$notice_type=I('get.notice_type');
	$Notice->read_notice($notice_type);
	$user_id=get_id();
	$user_sql=' and notice_effective=1 and notice.user_id='.$user_id;
	if($notice_type){
		$condition='notice_type='.$notice_type;
	}else{
		$condition='notice_type>20 and notice_type<30';
	}
	$condition.=$user_sql;
	$focus_list=$Notice->table('__NOTICE__ as notice')->join('__COMMENT__ as comment on notice.comment_id=comment.comment_id')->where($condition)->field('notice.notice_title,notice.notice_content,notice.notice_time,comment.comment_up_number,comment.comment_down_number,comment.comment_second_comment_number as comment_number')->order('notice.notice_time desc')->select();//关注全部动态
	$count=count($focus_list);//数据集总数
	$Page=new \Org\Util\Page($count,10);
	$show=$Page->show();
	$focus_list=array_slice($focus_list, $Page->firstRow, $Page->listRows);//分页
	$this->assign('focus_list',$focus_list);
	$this->assign('page',$show);
}

//circle/我要参与/系统消息的提醒
public function circle_system_part_notice($type){
	$Notice=D('Notice');
	$user_id=get_id();
	switch ($type) {
		case 5://circle
			$Notice->read_notice(31);
			$Notice->read_notice(32);
			$condition='(notice_type=31 or notice_type=32)';
			break;
		case 6:
			$Notice->read_notice(41);
			$condition='notice_type=41';
			break;
		case 7://系统消息
			$Notice->read_notice(51);
			$condition='notice_type=51';
			break;
	}
	$circle_list=$Notice->where($condition.' and notice_effective=1 and user_id='.$user_id)->order('notice_time desc')->select();
	$count=count($circle_list);//数据集总数
	$Page=new \Org\Util\Page($count,10);
	$show=$Page->show();
	$circle_list=array_slice($circle_list, $Page->firstRow, $Page->listRows);//分页

	$this->assign('circle_list',$circle_list);
	$this->assign('page',$show);
}


//首页获取未读提醒、私信
public function index_notice(){
	$user_id=get_id();
	//首页私信
	$Message=D('Message');
	$unwatch_message_count=$Message->unwatch_message_count();
	$message=$Message->join('__USER__ as user on send_user_id=user.user_id')->where('receive_user_id='.$user_id.' and receive_effective=1')->field('message_time,message_content,user.user_nickname as user_name,user.user_id')->order('message_time desc')->find();

	session('unwatch_message_count',$unwatch_message_count);
	session('message',$message);
	
	//首页评论
	$this->index_notice_data(11,'unwatch_comment_count','comment_notice');
	//首页完善
	$this->index_notice_data(12,'unwatch_improve_count','improve_notice');
	session('unwatch_reply_count',I('session.unwatch_comment_count')+I('session.unwatch_improve_count'));
	//首页圈子
	$this->index_notice_data(31,'unwatch_circle_count','circle_notice');
	//我要参与
	$this->index_notice_data(41,'unwatch_part_count','part_notice');
	//首页系统消息
	$this->index_notice_data(51,'unwatch_system_count','system_notice');
}

//message进入时显示的信息及提醒数
public function index_notice_data($type,$unwatch_count_name,$notice_name){
	$Notice=M('Notice');
	$user_id=get_id();
	if($type==31)
		$unwatch_count=$Notice->where('user_id='.$user_id.' and notice_effective=1 and notice_read=0 and (notice_type=31 or notice_type=32)')->count('notice_id');
	else
		$unwatch_count=$Notice->where('user_id='.$user_id.' and notice_effective=1 and notice_read=0 and notice_type='.$type)->count('notice_id');
	if($type==11 || $type==12){
		$notice=$Notice->join('left join __COMMENT__ as comment on notice.comment_id=comment.comment_id')->join('__USER__ as user on comment.user_id=user.user_id')->join('__ARTICLE__ as article on comment.article_id=article.article_id')->where('notice.user_id='.$user_id.' and notice_effective=1 and notice_type='.$type)->order('notice_time desc')->field('notice_content,notice_time,user.user_nickname as username,article.article_title,article.article_id')->find();
	}else if($type==31){
		$notice=$Notice->where('user_id='.$user_id.' and notice_effective=1 and (notice_type=31 or notice_type=32)')->order('notice_time desc')->find();
	}else{
		$notice=$Notice->where('user_id='.$user_id.' and notice_effective=1 and notice_type='.$type)->order('notice_time desc')->find();
	}
	session($unwatch_count_name,$unwatch_count);
	session($notice_name,$notice);
}

//设置提醒已读
public function read_notice(){
	$notice_type=I('post.notice_type');
	$Notice=D('Notice');
	$Notice->read_notice($notice_type);
	if(IS_AJAX){
		$return['status']=1;
		$this->ajaxReturn($return,'JSON');
	}
}

//设置全部私信已读
public function read_all_message(){
	$Message=D('Message');
	$Message->where('receive_user_id='.get_id())->setField('message_read',1);
}

//全部(私信和提醒)设置为已读
public function all_read(){
	//设置私信已读
	$this->read_all_message();
	//notice全部已读
	if(D('Notice')->where('notice_read=0 and user_id='.get_id())->setField('notice_read',1))
		$return['status']=1;
	else
		$return['status']=0;
	$this->ajaxReturn($return,'json');
}

//进入消息设置
public function setting(){
	$Setting=M('user_setting');
	$settings=$Setting->where('user_id='.get_id())->find();
	$this->assign('settings',$settings);
	$this->assign('tplName','setting');
	$this->display('index');
}

//保存消息提醒设置
public function save_setting(){
	$receive_sixin_set=I('post.receive_sixin_set');
	var_dump($receive_sixin_set);
	$Setting=M('user_setting');
	$Setting->create();

	if($Setting->where('user_id='.get_id())->save()){
		$return['status']=1;
	}else{
		$return['status']=0;
	}
	$this->redirect('Message/setting');
}

	//测试获取user
	public function user_info(){
		$users=M('User')->where('user.user_id in (select focus.user_id_focused from focus_on_user as focus where focus.user_id='.get_id().')')->field('user_id,user_nickname as user_name')->select();
		$this->ajaxReturn($users,'json');
	}

	//ajax长连接方法获取提醒
	public function new_message_notice(){
		$interval=500000;//轮询间隔
		set_time_limit(3);
		$Message=D('Message');	
		$current_count=session('unwatch_message_count');
		$unwatch_count=$Message->unwatch_message_count();
		while(true){
			//$unwatch_count=$Message->unwatch_message_count();
			if($unwatch_count!=$current_count){
				session('unwatch_message_count',$unwatch_count);
				//$this->ajaxReturn($unwatch_count,'string');
				exit($unwatch_count);
			}
			usleep($interval);
		}
	}

}