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
class NoticeController extends Controller {

	//测试notice
	public function Test(){
		session('user_id',134217729);
		$user_id=session('user_id');
		$Notice=D('Notice');
		$notice_type=I('get.notice_type');
		//$Notice->articleNotice(536870913,201326593,1);
		//$this->circleNotice(67108865);
		//未读提醒数
		$condition='notice_read=0 and notice_effective=1 and user_id='.$user_id.' and notice_type=';
		$un_comment=$Notice->where($condition.'01')->count('notice_id');
		$un_imp=$Notice->where($condition.'11')->count('notice_id');
		$un_system=$Notice->where($condition.'51')->count('notice_id');
		$this->assign('un_comment',$un_comment);
		$this->assign('un_imp',$un_imp);
		$this->assign('un_system',$un_system);
		//分页显示所有提醒记录
		$noticeList=$Notice->where('user_id='.$user_id.' and notice_effective=1 and notice_type='.$notice_type)->order('notice_time desc')->select();
		$count=count($noticeList);//数据集总数
		$Page=new \Org\Util\Page($count,10);
		$show=$Page->show();
		$list=array_slice($noticeList, $Page->firstRow, $Page->listRows);

		$this->assign('type',$notice_type);
		$this->assign('list', $list);
		$this->assign('page',$show);
		//$this->display();
	}

	//已读
	public function readNotice(){
		$notice_type=I('get.notice_type');
		$data['notice_read']=1;
		M('Notice')->where('notice_type='.$notice_type.' and notice_read=0')->save($data);
	}

	//删除notice
	public function deleteNotice(){
		$notice_id=I('post.notice_id');
		$data['notice_effective']=0;
		M('Notice')->where('notice_id='.$notice_id)->save($data);
	}

	//是否同意加入circle
	/*public function postRequest(){
		$user_id=I('post.user_id');
		$circle_id=I('post.circle_id');
		$if_agreed=I('post.if_agreed');
		$notice_id=I('post.notice_id');
		$Circle=M('Circle');
		$Belong_Circle=M('belong_to_circle');
		$circle=$Circle->where('circle_id='.$circle_id)->field('circle_limit,circle_number')->find();
		$return['circle_limit']=$circle['circle_limit'];
		$return['number']=$circle['circle_number'];
		if($circle['circle_limit']==$circle['circle_number']){
			$return['status']=1;//人数上限
		}

		else{
			if($if_agreed==1){
				//$Circle->where('circle_id='.$circle_id)->setInc('circle_number',1);
				$Belong_Circle->where('circle_id='.$circle_id.' and user_id='.$user_id)->setField(array('belong_to_circle_in_request','belong_to_circle_info'),array(0,'member'));
				$return['status']=2;//通过申请
				$this->request_circle_Notice($user_id,$circle_id,$if_agreed);
			}else{
				$Belong_Circle->where('circle_id='.$circle_id.' and user_id='.$user_id)->setField('belong_to_circle_info','未通过申请');
				$return['status']=3;
				$this->request_circle_Notice($user_id,$circle_id,$if_agreed);
			}
		}
		//删除此条通知
		M('Notice')->where('notice_id='.$notice_id)->setField('notice_effective',0);
		$this->ajaxReturn($return,'JSON');		
	}
*/

	public function postRequest(){
		$user_id=I('post.user_id');
		$circle_id=I('post.circle_id');
		$if_agreed=I('post.if_agreed');
		$notice_id=I('post.notice_id');
		$Circle=M('Circle');
		$Belong_Circle=M('belong_to_circle');
		$circle=$Circle->where('circle_id='.$circle_id)->field('circle_limit,circle_number')->find();
		//	var_dump($circle['circle_limit']);

		$return['circle_limit']=$circle['circle_number'];
		if($circle['circle_limit']==$circle['circle_number']){
			$return['status']=1;//人数上限
		}else{
			if($if_agreed==1){
				$Circle->where('circle_id='.$circle_id)->setInc('circle_number',1);
				$data['belong_to_circle_in_request']=0;
				$data['belong_to_circle_info']='wait';
				$Belong_Circle->where('circle_id='.$circle_id.' and user_id='.$user_id)->save($data);
				$return['status']=2;//通过申请
				$this->request_circle_Notice($user_id,$circle_id,$if_agreed);
			}else{
				$Belong_Circle->where('circle_id='.$circle_id.' and user_id='.$user_id)->setField('belong_to_circle_info','未通过申请');
				$return['status']=3;
				$this->request_circle_Notice($user_id,$circle_id,$if_agreed);
			}
		}
		//删除此条通知
		M('Notice')->where('notice_id='.$notice_id)->setField('notice_effective',0);
		$this->ajaxReturn($return,'JSON');		
	}
	//接受、拒绝进入圈子操作的通知
	public function request_circle_Notice($user_id,$circle_id,$if_agreed){
		$circle_name=M('Circle')->where('circle_id='.$circle_id)->getField('circle_name');
		$circle_url=U('Circle/index');
		$data['user_id']=$user_id;
		$data['notice_type']=32;
		$data['notice_content']=$if_agreed==1?"恭喜您成为“".$circle_name."”的成员，赶快去查看圈子的动态吧！<a href='".$circle_url."'>进入圈子</a>":"抱歉地通知您，您加入的“".$circle_name."”的申请没有被通过！";
		M('Notice')->data($data)->add();
		
	}

	//申请加入圈子notice
	public function circleNotice($circle_id){
		$user_id=get_id();
		$user_url=get_url_by_id($user_id);
		$user_name=M('User')->getFieldByUser_id($user_id,'user_nickname');
		$circle=M('Circle')->where('circle_id='.$circle_id)->field('circle_name,user_id')->find();
		$Belong_Circle=M('belong_to_circle');
		$content="<input name='circle_id' type='hidden' value='".$circle_id."'><input name='user_id' type='hidden' value='".$user_id."'><h4><a href='".$user_url."'>".$user_name."</a></h4>申请加入您的圈子“".$circle['circle_name']."”";
		$data['notice_type']=31;
		$data['notice_content']=$content;
		$data['user_id']=$circle['user_id'];
		M('Notice')->add($data);
	}
}