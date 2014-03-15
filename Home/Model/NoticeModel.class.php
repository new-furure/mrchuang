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
class NoticeModel extends Model {	

	//新评论/完善时notice方法
	//动作类型 1、评论 2、完善 3、回答 4、发布article 5、赞同
	public function article_notice($comment_id=null,$article_id,$action_type){
		$user_id=get_id();
		$User=M('User');
		$userName=$User->getFieldByUser_id($user_id,'user_nickname');
		//article信息
		$Article=M('Article');
		$article=$Article->where('article_id='.$article_id)->field('user_id,article_title,article_profile,article_type')->find();
		$publisher_id=$article['user_id'];
		$publisher_name=$User->getFieldByUser_id($publisher_id,'user_nickname');//发布者名字
		$string=new \Org\Util\String();//扩展函数
		$article_title=$string->msubstr($article['article_title'],0,50);//截取标题长度20
		$article_type=$article['article_type'];//文章类型
		$article_url=get_url_by_id($article_id);//文章url

		//article类型确定notice类型
		switch ($article_type) {
			case C( "PROJECT_TYPE" ):
				$type_name="项目";
				$notice_type=21;
				break;
			case C( "POLICY_TYPE" ):
				$type_name="政策";
				$notice_type=22;
				break;
			case C( "QUESTION_TYPE" ):
				$type_name="问题";
				$notice_type=23;
				break;
			case '3':
				$type_name='完善';
				$notice_type=24;
				break;
		}
		//操作类型 参考comment表字段
		switch ($action_type) {
			case '1':
				$action_name="评论";
				$publisher_notice_type=11;
				break;
			case '2':
				$action_name="完善";
				$publisher_notice_type=12;
				break;
			case '3':
				$action_name="回答";
				$publisher_notice_type=13;
				break;
			case '4':
				//publisher 关注者
				$action_name="发布新";
				break;
			case '5':
				$action_name="赞";
				break;
		}

		if($comment_id){
			$content=M('Comment')->getFieldByComment_id($comment_id,'comment_content');
		}
		$Notice=M('Notice');
		//publisher收到notice
		// $contentOne="<span style='font-size:13px'>您的".$type_name."“<a href='".$article_url."'>".$article_title."</a>”收到<span style='font-size:16px'>".$userName."</span>的".$action_name."<p>".$content."</p></span>";
		if($publisher_notice_type){
			$data['user_id']=$publisher_id;
			$data['notice_type']=$publisher_notice_type;
			$data['notice_content']=$content;
			$data['comment_id']=$comment_id;
			$Notice->add($data);
			unset($data);
		}
		//publisher关注者收到notice
		$title="您关注的".$userName.$action_name."了".$type_name."“<a href='".$article_url."'>".$article_title."</a>”";
		$Focus_User=M( 'focus_on_user' );
		$user_id_article=$Focus_User->where('user_id_focused='.$publisher_id)->getField('user_id',true);
		$data['notice_type']=25;
		$data['notice_title']=$title;
		$data['notice_content']=$content;
		$data['comment_id']=$comment_id;
		foreach ($user_id_article as $uid) {
			$data['user_id']=$uid;
			$Notice->add($data);
		}
		unset($data);

		//article关注者收到notice
		$title="您关注的".$type_name."“<a href='".$article_url."'>".$article_title."</a>”收到".$userName."</span>的".$action_name;	
		$Focus_Article=M('focus_on_article');
		//article关注者
		$user_id_article=$Focus_Article->where('article_id='.$article_id)->getField('user_id',true);
		$data['notice_type']=$notice_type;
		$data['notice_title']=$title;
		$data['notice_content']=$content;
		$data['comment_id']=$comment_id;
		foreach ($user_id_article as $uid) {
			$data['user_id']=$uid;
			$Notice->add($data);
		}
		unset($data);

		//user的关注者收到notice内容
		if($user_id!=$publisher_id){
			$title="您关注的".$userName.$action_name."了".$type_name."“<a href='".$article_url."'>".$article_title."</a>”";		
			//user关注者
			$user_id_article=$Focus_User->where('user_id_focused='.$user_id)->getField('user_id',true);
			$data['notice_type']=25;
			$data['notice_title']=$title;
			$data['notice_content']=$content;
			$data['comment_id']=$comment_id;
			foreach ($user_id_article as $uid) {
				$data['user_id']=$uid;
				$Notice->add($data);
			}
			unset($data);
		}		
	}

	//设置notice已读
	public function read_notice($notice_type){
		$data['notice_read']=1;
		$user_id=session('user_id');
		M('Notice')->where('notice_type='.$notice_type.' and notice_read=0 and user_id='.$user_id)->save($data);
	}
}