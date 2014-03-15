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
class DialogModel extends Model{

	//获取私信对话列表
	public function list_dialog(){
		$Dialog=D('Dialog');
		$user_id=session('user_id');
		$list=$Dialog->where('(user_id_less='.session('user_id').' and dialog_less_effective=1) or (user_id_more='.$user_id.' and dialog_more_effective=1)')->order('dialog_time desc')->select();
		unset($dialogList);
		foreach ($list as $key => $value) {

			$user_id=$value['user_id_more']==$user_id?$value['user_id_less']:$value['user_id_more'];
			//查询用户信息
			$user=M('User')->where('user_id='.$user_id)->find();
			$dialog['user_id_other']=$user_id;
			$dialog['user_name']=$user['user_nickname'];
			$dialog['user_avatar_url']=$user['user_avatar_url'];
			$dialog['unwatch_message_count']=$this->unWatchMsg($value['user_id_less'],$value['user_id_more']);
			$dialog['dialog_content']=$value['dialog_content'];
			$dialog['dialog_time']=$value['dialog_time'];
			$dialogList[]=$dialog;
		}
		return $dialogList;
	}

	//获取对话未读私信数
	public function unWatchMsg($user_id_less,$user_id_more){
		$Message=D('Message');
		//发送人uid
		$user_id=session('user_id');
		$send_user_id=$user_id_more==$user_id?$user_id_less:$user_id_more;

		$count=$Message->where('message_read=0 and receive_user_id='.$user_id.' and send_user_id='.$send_user_id)->count('message_id');
		return $count;
	}
}