<?php
/*
+---------------------------------------------------------------
+ 消息Modle
+ 初稿：夏闪闪
+ 完善
+---------------------------------------------------------------

*/
namespace Home\Model;
use Think\Model;
class MessageModel extends Model {

	//发送私信
	public function send_message($acceptUid,$message){
		unset($data);
		$sendUid=get_id();
		$Message=D('Message');
		$data['send_user_id']=$sendUid;
		$data['receive_user_id']=$acceptUid;
		$data['message_content']=$message;
		$Message->add($data);
		$Dialog=D('Dialog');
		if($sendUid>$acceptUid){
			$less=$acceptUid;
			$more=$sendUid;
		}else{
			$less=$sendUid;
			$more=$acceptUid;
		}
		$dialog=$Dialog->where('user_id_less='.$less.' and user_id_more='.$more)->find();
		//已有私信对话
		if($dialog!=null){
			var_dump($dialog);
			$ddata['dialog_content']=$message;
			//如果私信对话已被设置有效位为0（即删除）
			if(($sendUid>$acceptUid) && $dialog['dialog_more_effective']==0){
				$ddata['dialog_more_effective']=1;				
			}else if(($sendUid<$acceptUid) && $dialog['dialog_less_effective']==0){
				$ddata['dialog_less_effective']=1;
			}	
			var_dump($ddata);	
			if($Dialog->where('user_id_less='.$less.' and user_id_more='.$more)->save($ddata))
				return true;
			else
				return false;
		}
		//新的私信对话
		else {
			$dialogData['user_id_less']=$less;
			$dialogData['user_id_more']=$more;
			$dialogData['dialog_content']=$message;
			if($Dialog->data($dialogData)->add())
				return true;
			else
				return false;
		}		
	}

	//查看私信对话
	public function list_dialog_message($user_id_other)
	{
		unset($msgList);
		$user_id=get_id();
		$Message=D('Message');
		//对话中的全部私信(有效位)
		$list=$Message->where('(send_user_id='.$user_id.' and send_effective=1 and receive_user_id='.$user_id_other.') or (send_user_id='.$user_id_other.' and receive_user_id='.$user_id.' and receive_effective=1)')->order('message_time desc')->select();
		//遍历私信重新填充私信数据
		foreach ($list as $key => $value) {
			$user=M('User')->where('user_id='.$value['send_user_id'])->find();
			$msg['message_id']=$value['message_id'];
			$msg['user_id']=$value['send_user_id'];
			$msg['content']=$value['message_content'];
			$msg['user_name']=$user['user_nickname'];
			$msg['user_avatar_url']=$user['user_avatar_url'];
			$msg['time']=$value['message_time'];
			$msgList[]=$msg;
		}
		return $msgList;

	}

	//未读私信提醒条数
	public function unwatch_message_count(){
		$user_id=get_id();
		$Message=D('Message');
		//用户被屏蔽，不再提醒
		$count=$Message->where('receive_user_id='.$user_id.' and message_read=0 and send_user_id not in (select user_id_blocked from block where user_id='.$user_id.')')->count('message_id');
		return $count;
	}

	//设置对话私信已读
	public function read_dialog($user_id_other){	
		$user_id=get_id();
		$Message=D('Message');
		$Message->where('send_user_id='.$user_id_other.' and receive_user_id='.$user_id)->setField('message_read',1);	
	}

}