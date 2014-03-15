<?php
/*
+---------------------------------------------------
+È¦×ÓÄ£¿é
+³õ¸å Àî½¨ÄÐ
+
+---------------------------------------------------
*/
namespace Home\Controller;
use Think\Controller;

class CircleController extends BaseController{
	


/********************************************************
    µÇÂ½ ×¢²á ²¿·Ö
*********************************************************/

//×¢²áÒ³
//@×÷Õß£º
//index 显示首页信息
public function index(){

		//$user_id='134217729';
		$user_id=get_id();
		//提取用户加入的圈子
		$model = M('belong_to_circle');
		//$user_id=get_id();

		//测试数据
		//$user_count

        $my_circle=$model
        ->join('circle ON belong_to_circle.circle_id=circle.circle_id')
        ->where("belong_to_circle.user_id=$user_id AND circle.circle_effective=1 
        	AND belong_to_circle_info<>'quit' AND belong_to_circle_in_request<>1")
        ->limit(2)
        ->select();
        //判断用户属于哪个圈子

      	$i=0;
        foreach ($my_circle as $key => $v) {
        	 $a[$i]=$v['circle_name'];
        	 /*echo $a[$i];*/
        	$i++;
        }

        $belong_1=$a[0];
        $belong_2=$a[1];

        $this->assign('belong_1',$belong_1);
        $this->assign('belong_2',$belong_2);
       $this->assign('my_circle',$my_circle);

        $circle_count=$model
        ->join('circle ON belong_to_circle.circle_id=circle.circle_id')
        ->where("belong_to_circle.user_id=$user_id AND circle.circle_effective=1
        	AND belong_to_circle_info<>'quit' AND belong_to_circle_in_request<>1")
        ->count();

		$this->assign('circle_count',$circle_count);

		//提取所有跨圈活动数量

        $article =M('article');
        $post=M('post');	
        $comment=M('comment');

        $type=C('OUT_POST');

        $circle_activity_number=$post
        ->join("article ON article.article_id=post.article_id")
        ->where("post.post_type=$type AND article_effective=1")
        ->count();
        $this->assign('circle_activity_number',$circle_activity_number);

        //配置分页信息
        import('ORG.Util.Page');
       // 查询满足要求的总记录数
		$Page = new \Think\Page($circle_activity_number,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page
		->show();
		$this->assign('show',$show);

		//提取跨圈活动帖子

		$post_type=C('POST_TYPE');


        $circle_post=$post
        ->join('article ON article.article_id=post.article_id')
        ->join('circle ON circle.circle_id=post.circle_id')
        ->where("article.article_effective=1 AND article.article_type=$post_type AND post.post_type=$type")
        ->limit($Page->firstRow.','.$Page->listRows)
        ->order('article.article_time desc')
        ->select();


        $this->assign('circle_post',$circle_post);
        

        //if($comment_number)
        	//echo 'success';

		$this->display();
	
}
//处理搜索内容
public function search(){
	//$user_id=$_SESSION['user_id'];
	$user_id=get_id();
	//$user_id='134217729';
	/*
	$searh_result 的含义
	0——没有输入搜索内容
	1——没有找到输入的圈号
	2——找到了，用户属于圈子
	3——找到了，用户不属于圈子

	*/
		$search_content=I('search');
		//根据搜索内容判断
		if((int)$search_content){
			$circle=M('circle');
			//对圈号进行搜索
			$result=$circle
			->where("circle.circle_id = $search_content AND circle_effective=1")
			->select();
			//如果有结果
			if ($result) {
				//查看是否为该圈子成员——成员不能为quit
				$belong=M('belong_to_circle')
				->where("belong_to_circle.circle_id = $search_content 
					AND belong_to_circle.user_id=$user_id AND belong_to_circle_info<>'quit' 
					AND belong_to_circle_in_request<>1")
				->find();
				//如果属于
				if($belong){
					$search_result=2;
				}//如果不属于
				else{$search_result=3;}
					$this->assign('result',$result);
			}
			//如果没找到
			else
				$search_result=1;

		}//如果没有任何搜索
		else{
			$search_result=0;
		}
		
		$this->assign('search_result',$search_result);
		$this->display('search');
	
}


//单独圈子首页
public function detail(){
	$cid=$_GET['cid'];
	//$user_id=get_id();
	$user_id=get_id();
	//$user_id='134217729';
	$circle=M('circle');

	//找到该用户加入的圈子
	//在此就无须判断圈子有效性
	$my_circle=$circle
	->join('user ON user.user_id=circle.user_id')
	->where("circle.circle_id=$cid")
	->select();

	$this->assign('my_circle',$my_circle);
	//提取圈子名称
	$my_circle_name=$circle
	->join('user ON user.user_id=circle.user_id')
	->where("circle.circle_id=$cid")
	->find();


	$this->assign('circle_name',$my_circle_name['circle_name']);
	$this->assign('cid',$cid);
	//echo $cid;

	 //配置分页信息——总分页数
	//分页目前仅在显示全部时候用
    import('ORG.Util.Page');
    $count = M('post')
    ->join("article ON article.article_id=post.article_id ")
	->where("circle_id=$cid AND article_effective=1")
	->count();
	$post_number=$count;// 查询满足要求的总记录数
	$Page = new \Think\Page($post_number,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
	$show = $Page
	->show();
	$this->assign('show',$show);


        
	$article=M('article');
	$post_list=$article
	->join('post ON post.article_id=article.article_id')
	->join('user ON user.user_id=article.user_id')
	->where("post.circle_id=$cid AND article.article_effective=1")
	->limit($Page->firstRow.','.$Page->listRows)
	->order('article.article_time desc')//按时间排序
	->select();
	$this->assign('post_list',$post_list);

	//帖子类型变量
	$out_post=C('OUT_POST');
	$in_post=C('IN_POST');
	$chat_post=C('CHAT_POST');
	$normal_post=C('NORMAL_POST');

	//活动帖分页
	//暂时没用到
	$count = M('post')
	->where("circle_id=$cid AND (post_type=0 OR post_type=1)")//post_type=0时为跨圈活动
	->count();
	$post_activity_number=$count;// 查询满足要求的总记录数
	$Page_ac = new \Think\Page($post_activity_number,2);// 实例化分页类 传入总记录数和每页显示的记录数(25)
	$show_ac = $Page_ac
	->show();
	// $this->assign('show_ac',$show_ac);

	$post_list_activity=$article
	->join('post ON post.article_id=article.article_id')
	->join('user ON user.user_id=article.user_id')
	->where("post.circle_id=$cid AND (post.post_type=$out_post OR post.post_type=$in_post)
		AND article.article_effective=1")
	// ->limit($Page_ac->firstRow.','.$Page_ac->listRows)
	->order('article.article_time desc')
	->select();
	$this->assign('post_list_activity',$post_list_activity);

	//群聊帖分页
	$count = M('post')
	->where("circle_id=$cid AND post_type=$chat_post")
	->count();
	$post_chat_number=$count;// 查询满足要求的总记录数
	$Page_chat = new \Think\Page($post_chat_number,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
	$show_chat = $Page_chat
	->show();
	// $this->assign('show_chat',$show_chat);

	$post_list_chat=$article
	->join('post ON post.article_id=article.article_id')
	->join('user ON user.user_id=article.user_id')
	->where("post.circle_id=$cid AND post.post_type=$chat_post 
		AND article.article_effective=1")
	->order('article.article_time desc')
	->select();
	$this->assign('post_list_chat',$post_list_chat);

	$post_list_post=$article
	->join('post ON post.article_id=article.article_id')
	->join('user ON user.user_id=article.user_id')
	->where("post.circle_id=$cid AND post.post_type=$normal_post 
		AND article.article_effective=1")
	->order('article.article_time desc')
	->select();
	$this->assign('post_list_post',$post_list_post);

	$belong_to_circle=M('belong_to_circle');
	$user=M('user');
	//提取最近加入的用户
	$belong=$belong_to_circle
	->join('user ON user.user_id=belong_to_circle.user_id')
	->where("belong_to_circle.circle_id=$cid AND user.user_effective=1 
		AND belong_to_circle_info<>'quit' AND belong_to_circle_in_request<>1")
	->order('belong_to_circle.belong_time desc')
	->limit(8)
	->select();
	$this->assign('belong',$belong);
	/*if ($belong) {
    	echo "success";
    }*/
    //echo $cid;
    $this->assign('circle_cid',$cid);
    $circle = M('circle');
	//$user_id=$_SESSION['user_id'];
	$find_user=$circle
	->where("circle.circle_id=$cid AND circle.user_id=$user_id")
	->select();
	//分配$find，判断当前用户的身份
	/*
	$find_user不同值的含义
	0——普通用户
	1——圈主
	2——管理员
	*/
	if ($find_user) {
		$find=1;//当前用户是圈主
	}
	else{

		$find_user=M('belong_to_circle')
		->where("circle_id=$cid AND user_id=$user_id AND belong_to_circle_info='admin'
			")
		->select();
		if($find_user){
			$find=2;//表示当前用户是管理员
		}
		else{

			$find=0;//当前用户是普通用户
		}
		
	}
	$this->assign('find',$find);
	$this->display('detail');
}

public function apply_confirm(){
	$this->display('apply_confirm');
	
}
//开始创建圈子的验证
//验证用户注册时间
//以及已经加入的圈子数量
public function start(){
	//$user_id=$_SESSION['user_id'];
	//$user_id=get_id();
	$user_id=get_id(false);
	//$user_id='134217729';

	$user=M('user');
	$user_time=$user
	->where("user.user_effective=1 AND user.user_id=$user_id")
	->find();

	$user_t=$user
	->where("user.user_effective=1 AND user.user_id=$user_id")
	->select();

	//提取用户加入圈子的数量
	$belong_to_circle=M('belong_to_circle');
	$belong_number=$belong_to_circle
	->join("circle ON circle.circle_id=belong_to_circle.circle_id")
	->where("user_id=$user_id AND belong_to_circle_info<>'quit' AND circle_effective=1
		 AND belong_to_circle_in_request <>1")
	->count();
	$this->assign('belong_number',$belong_number);

	/*echo $belong_number;*/

	//echo $belong_number;

	//计算用户的注册时间
	$olddate=$user_time['user_time'];//注册时间
	$oldtime = strtotime($olddate);//转化为时间戳
	$passtime = time()-$oldtime; //经过的时间戳。
	$reg_time=floor($passtime/(24*60*60));//注册时间转化为天
	$wait_time=30-$reg_time;//等待时间
	$this->assign('user_t',$user_t);
	$this->assign('user_id',$user_id);
	$this->assign('reg_time',$reg_time);
	$this->assign('wait_time',$wait_time);
	$this->display('start');
}
//圈子信息填写页
public function new_circle(){
	$aid=$_GET['aid'];

	$this->assign('user_id',$aid);
	$this->display();
}
//处理新建圈子的表单
public function handle_new_circle(){

	$aid=I('user_id');

	//$user_id='134217729';
	//$user_id=$_SESSION['user_id'];
	$user_id=get_id();
	if(!IS_POST) E('页面不存在！');

	//查找是否重名
	$circle_name=I('post.title');
	$data['circle_name']=$circle_name;
	$data['circle_effective']=1;
	$find_name=M('circle')
	->where($data)
	->find();
/*
	dump($find_name);

	echo $circle_name;
	echo $find_name;*/


	if($find_name){
		$data['type']=2;//表示圈子名已存在
		$this->ajaxReturn($data,'json');
	}

	else{
	
	//随机分配圈子照片
	$circle_avatar_url="__ROOT___/Public/Img/ReplaceCircle".rand(1,6).".jpg";
	$data_circle=array(
	'circle_name'=>I('title'),
	'circle_profile'=>I('content'),
	'circle_number'=>1,
	'user_id'=>$aid,
	'circle_effective' => 1,
	//默认图片文件
	'circle_avatar_url'=> $circle_avatar_url,
	);

	$result=M('circle')->data($data_circle)->add();

	$data_circle=array(
	'circle_id'=>$result,
	'user_id'=>$user_id,
	'belong_to_circle_in_request'=>0,
	'belong_to_circle_info'=>'owner',
	// 'circle_avartar_url'=>'/www/Public/Img/Replace_circle.jpg'
	);

	$result_circle=M('belong_to_circle')->data($data_circle)->add();
	if($result&&$result_circle){
		$data['type']=1;
		$this->ajaxReturn($data,'json');
	}
	else{
		$data['type']=0;
		$this->ajaxReturn($data,'json');
	}

}

	//$this->assign('user_id',$aid);
	// $this->display();
}
//开始创建活动填写页
public function start_activity(){
	$circle_id=$_GET['cid'];
	$circle=M('circle');
	$circle_info=$circle
	->where("circle_id= $circle_id")
	->select();
	$this->assign('circle_info',$circle_info);
	$this->display('start_activity');
}
//开始创建群聊填写页
public function start_chat(){
	$circle_id=$_GET['cid'];
	$circle=M('circle');
	$circle_info=$circle
	->where("circle_id= $circle_id")
	->select();
	$this->assign('circle_info',$circle_info);
	$this->assign('cid',$circle_id);
	$this->display('start_chat');
}
//开始创建帖子填写页
public function post(){
	$circle_id=$_GET['cid'];
	$circle=M('circle');
	$circle_info=$circle
	->where("circle_id= $circle_id")
	->select();
	$this->assign('circle_info',$circle_info);
	
	$this->display('post');
}
//处理帖子表单
public function handle_new_post(){
	$cid=I('circle_id');
	//$user_id=$_SESSION['user_id'];
		$user_id=get_id();
	if(!IS_POST) E('页面不存在！');
	
	$data=array(
	'article_title'=>I('title'),
	'article_content'=>I('content'),
	'article_type'=>5,
	'user_id'=>$user_id,
	);

	$result=M('article')->data($data)->add();


	$data_post=array(
		'circle_id'=> $cid,
		'article_id'=> $result,
		'post_type'=> 3,
		);

	$result_post=M('post')->data($data_post)->add();

	if($result&& $result_post){
		$data['type']=1;
		$this->ajaxReturn($data,'json');
	}
	else{
		$data['type']=0;
		$this->ajaxReturn($data,'json');
	}
}
//处理活动表单
public function handle_new_activity(){

	//$user_id=$_SESSION['user_id'];
	$cid=I('circle_id');
	$user_id=get_id();
	//$user_id='134217729';
	if(!IS_POST) E('页面不存在！');
	
	$data=array(
	'article_title'=>I('title'),
	'article_content'=>"主题：".I('title')."<br><br>时间: ".I('date').
	"<br><br>地点: ".I('location')."<br><br>内容: ".I('content'),
	'article_type'=>5,
	'user_id'=>$user_id,
	);

	$result=M('article')->data($data)->add();


	$data_activity=array(
		'circle_id'=> $cid,
		'article_id'=> $result,
		'post_type'=> I('type'),
		);

	$result_activity=M('post')->data($data_activity)->add();

	
	if($result&& $result_activity){
	$data['type']=1;
	$this->ajaxReturn($data,'json');
	}
	else{
	$data['type']=0;
	$this->ajaxReturn($data,'json');
	}
}
//处理群聊表单
public function handle_new_chat(){
	//$user_id=$_SESSION['user_id'];
	$cid=I('circle_id');
		$user_id=get_id();
	if(!IS_POST) E('页面不存在！');
	
	$data=array(
	'article_title'=>I('title'),
	'article_content'=>"主题：".I('title')."<br><br>时间: ".I('date').
	"<br><br>网址: ".I('url')."<br><br>内容: ".I('content'),
	'article_type'=>5,
	'user_id'=>$user_id,
	);

	$result=M('article')->data($data)->add();


	$data_chat=array(
		'circle_id'=> $cid,
		'article_id'=> $result,
		'post_type'=> 2,
		);

	$result_chat=M('post')->data($data_chat)->add();

	
	if($result && $result_chat){
		$data['type']=1;
	$this->ajaxReturn($data,'json');
	}
	else{
		$data['type']=0;
	$this->ajaxReturn($data,'json');
	}
}
//提取文章内容页
public function post_detail(){
	$aid=$_GET['aid'];

	$cid=$_GET['cid'];
	//$user_id=$_SESSION['user_id'];
		$user_id=get_id();
	//$user_id='134217729';
	//查找当前用户，确定其是否为圈子成员
	$find=M('belong_to_circle')
	->where("user_id=$user_id AND circle_id=$cid AND belong_to_circle_info<>'quit'
		 AND belong_to_circle_in_request<>1 ")
	->find();

	if($find){
		$enter_type=1;
	}
	else{
		$enter_type=0;//enter_type为0表示跨圈活动进入
	}

	$this->assign('enter_type',$enter_type);
	$article=M('article');
	$post_detail=$article
	->join('user ON user.user_id=article.user_id')
	->join('post ON post.article_id=article.article_id')
	->where("article.article_id=$aid AND user_effective=1 AND article_effective=1 ")
	->select();
	$post=$article
	->join('user ON user.user_id=article.user_id')
	->join('post ON post.article_id=article.article_id')
	->where("article.article_id=$aid AND user_effective=1 AND article_effective=1 ")
	->find();
	$this->assign('post_detail',$post_detail);
	$my_circle_name=M('circle')
	->where("circle.circle_id=$cid")
	->find();
	$this->assign('circle_name',$my_circle_name['circle_name']);
	$this->assign('circle_id',$cid);
	$this->assign('circle_avatar_url',$my_circle_name['circle_avatar_url']);

	//echo $post_detail['article_type'];

	$this->withdraw_comment($aid,$post['article_type']);

	/*// 导入分页类
	import('ORG.Util.Page');
	// 找到文章评论数
		$count = M('article')
		->where("article_id=$aid")
		->find();
		$article_comment_number=$count['article_comment_number'];// 查询满足要求的总记录数
		$Page = new \Think\Page($article_comment_number,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page
		->show();// 分页显示输出


	//提取评论列表
	$user_comment=M('comment')
	->join("user ON comment.user_id=user.user_id")
	//->join("second_comment ON second_comment.comment_id=comment.comment_id",'left')
	->where("comment.article_id=$aid")
	->field('user_nickname,comment_time,user.user_id,comment_content,comment_id as id,comment_id')
	->limit($Page->firstRow.','.$Page->listRows)
	->order("comment_time desc")
	->select();

	$this->assign('user_comment',$user_comment);
	$this->assign('page',$show);// 赋值分页输出
	//提取二级评论
	$user_second_comment=M('second_comment')
	->join('user send_user ON send_user.user_id=second_comment.user_id')
	->join('user receive_user ON receive_user.user_id=second_comment.user_reply_id')
	->select();
	$this->assign('user_second_comment',$user_second_comment);
  	//提取二级评论
	foreach($user_comment as $n=> $val){
      $user_comment[$n]['comment_id']=M('second_comment')
      ->join('user send_user ON send_user.user_id=second_comment.user_id')
	  ->join('user ON user.user_id=second_comment.user_reply_to_id')
	  ->field('second_comment_time,second_comment_content,user.user_nickname as receiver_name
	  	,send_user.user_nickname as sender_name,send_user.user_id as user_id')
      ->where('comment_id=\''.$val['comment_id'].'\'')
      ->select();
      
     }

     $this->assign('user_comment',$user_comment);


    


     $this->assign('article_id',$aid);*/

	$this->display('post_detail');

}

public function down($aid){
	
	 	


}
public function back(){
	$this->display('detail');
}



public function confirm_new_circle(){
	$this->display('confirm_new_circle');
}


public function quit($cid){

	
	//$user_id=$_SESSION['user_id'];
	$user_id=get_id();
	//提取用户名
	$user=M('user')
	->where("user_id=$user_id")
	->find();
	$user_nickname=$user['user_nickname'];
	//提取圈子名称
	$circle=M('circle')
	->where("circle_id=$cid")
	->find();
	$circle_name=$circle['circle_name'];
	//谁要退出什么圈
	$find_user=M('circle')
	->where("circle.circle_id=$cid AND circle.user_id=$user_id")
	->select();
	//若退出者不是圈主，可以退出
	if (!$find_user) {
		$belong=M('belong_to_circle');
		//用户身份改为quit
		$data['belong_to_circle_info']='quit';
		$result=$belong
		->where("belong_to_circle.user_id=$user_id")
		->save($data);
		//用户数量减1
		$result_dec=M('circle')
		->where("circle_id=$cid")
		->setDec('circle_number');
		//系统通知圈主
		//找到圈主
		$circle=M('circle')
		->where("circle_id=$cid")
		->find();
		$circle_owner=$circle['user_id'];
		$data['user_id']=$circle_owner;
		$data['notice_content']="用户".$user_nickname."退出".$circle_name."圈子";
		$data['notice_type']=C('CIRCLE_QUIT');
		//发送系统消息
		$result_notice=M('notice')
		->data($data)
		->add();
		//系统通知管理员
		/*还没做*/

		if($result&&$result_dec&&$result_notice){
		$this->success('成功退出',U('index'));}
	else{
		$this->error('退出失败，请重试...');
	}
	}
	else
	{
		$this->error('您是圈主，不能退出...您可以转让圈主身份后退出或解散圈子');
	}
	
		
}

public function member($cid){
	//$user_id=$_SESSION['user_id'];
	$user_id=get_id();
	/*if(!IS_POST) E('页面不存在！');*/
	$belong_to_circle=M('belong_to_circle');
	//用户加入的圈子数且没有quit
	$belong_number=$belong_to_circle
	->where("user_id=$user_id AND belong_to_circle_info<>'quit'")
	->count();
	if($belong_number>=2){
		$this->error('您已加入两个圈子，加入圈子的上限为2');
	}else
	{
		//否则，向圈主发notice
		$receiver=M('circle')
		->where("circle.circle_id=$cid")
		->find();
		$receiver_id=$receiver['user_id'];
		$user=M('user')
		->where("user.user_id=$user_id")
		->find();
		$send_user_name=$user['user_nickname'];
		$circle_name=$receiver['circle_name'];
		//notice内容
		//加入圈子系统消息提醒圈主

		$content="<input name='circle_id' type='hidden' value='".$cid."'><input name='user_id' type='hidden' value='".$user['user_id']."'><h4><a href='".get_url_by_id($user['user_id'])."'>".$user['user_nickname']."</a></h4>申请加入您的圈子“".$circle_name."”";
		$notice=array(
			'user_id'=>$receiver_id,
			'notice_content'=>$content,
			'notice_type' => C('CIRCLE_APPLY'),
			);

		$data['user_id']=$user['user_id'];
		$data['circle_id']=$cid;
		$data['belong_to_circle_in_request']=1;
		$data['belong_to_circle_info']='wait';

		$save_info=M('belong_to_circle')
		->data($data)
		->add();
		if($save_info){
			$result=M('notice')
			->data($notice)
			->add();
		}
		


		if ($save_info && $result) {
			$this->success('已向圈主发送请求，请耐心等待',U('index'));
		}
		else{
			$this->error('申请出错，请重试...');
		}
	}


}

public function manage(){
	$cid=$_GET['cid'];
	$circle=M('circle')
	->where("circle.circle_id=$cid")
	->find();
	$circle_name=$circle['circle_name'];
	$circle_avatar_url=$circle['circle_avatar_url'];
	$this->assign('circle_profile',$circle['circle_profile']);
	$this->assign('circle_name',$circle_name);
	$this->assign('circle_id',$cid);
	$this->assign('circle_avatar_url',$circle_avatar_url);

	//检查是否修改了content

	$this->display('manage_basic_info');
	//echo $cid;
}


 public function upfile($cid) {
		$path = __ROOT__."/Uploads/Img/circle/manage_basic_info/";
		$path1 = APP_PATH."/Uploads/Img/circle/manage_basic_info/";
		// echo APP_PATH;
		$file_src = "src.png"; 
		$filename162 = $cid.".png"; 
		$src=base64_decode($_POST['pic']);
		$pic1=base64_decode($_POST['pic1']);   
		if($src) {
		file_put_contents($file_src,$src);
		}

		file_put_contents($path1.$filename162,$pic1);
		$path_info = $path.$filename162;
		//将图片路径存入数据库

		$find_url=M('circle')
		->where("circle_id=$cid")
		->find();
		$circle_avatar_url = $find_url['circle_avatar_url'];
		if($circle_avartar_url == $path_info)
		{
			$save_url=1;
		}
		else{
			$data['circle_avatar_url'] = $path.$filename162;
			$save_url=M('circle')
			->where("circle_id=$cid")
			->data($data)
			->save();
		}
		

		
		$rs['status'] = 1;
	
		//$rs['time'] = $time;

		echo json_encode($rs);
    }


public function handle_basic_info($cid){
	// echo I('content');
	if(I('content')){
		$data['circle_profile']=I('content');
		$result=M('circle')
		->where("circle.circle_id=$cid")
		->save($data);
		if($result){
			$this->success('修改成功',U('/Home/Circle/manage/cid/'.$cid));
		}
		else{
			$this->error('修改失败');
		}
	}

	else{
		$this->error('没有修改内容');
	}
}

public function manage_member(){
	//$user_id=$_SESSION['user_id'];
	$user_id=get_id();
	$cid=$_GET['cid'];
	$this->assign('circle_id',$cid);
	/*echo $user_id;
	echo $cid;*/
	//判断当前用户是圈主还是管理员
	$circle=M('circle')
	->where("circle_id =$cid")
	->find();

	$circle_name=$circle['circle_name'];
	$this->assign('circle_name',$circle_name);
	$circle_avatar_url=$circle['circle_avatar_url'];
	$this->assign('circle_id',$cid);
	$this->assign('circle_avatar_url',$circle_avatar_url);

	$find=M('circle')
	->where("circle.circle_id=$cid AND circle.user_id=$user_id")
	->find();
	if ($find) {
		$status=1;//status为1表示圈主
	}
	else{
		$find=M('belong_to_circle')
		->where("belong_to_circle.circle_id=$cid 
			AND belong_to_circle.user_id=$user_id
			AND belong_to_circle.belong_to_circle_info='admin'")
		->find();
		if($find)
			$status=2;//status为2表示管理员
		else
			$status=0;//status为0表示普通成员
	}
	//显示圈主信息
	$circle_owner=M('circle')
	->join('user ON user.user_id = circle.user_id')
	->where("user.user_id=$user_id AND circle.circle_id=$cid")
	->select();

	$this->assign('circle_owner',$circle_owner);
	$this->assign('status',$status);
	// echo $status;

	//显示管理员信息
	$circle_admin=M('belong_to_circle')
	->join('user ON belong_to_circle.user_id= user.user_id')
	->where("belong_to_circle.circle_id=$cid AND belong_to_circle_info='admin'")
	->select();
	$this->assign('circle_admin',$circle_admin);
	//分配当前管理员id，不显示私信
	$this->assign('user_id',$user_id);

	//显示成员信息
	$circle_member=M('belong_to_circle')
	->join('user ON belong_to_circle.user_id= user.user_id')
	->where("belong_to_circle.circle_id=$cid 
		AND belong_to_circle.belong_to_circle_info='member'")
	->select();
	$this->assign('circle_member',$circle_member);
	$this->display('manage_member');

}

//解散圈子
//并且解散圈子的消息私信发送至所有用户
//解散圈子涉及的操作
//step1********circle_effective置0
//step2********向用户发送圈子解散的message
//step3********将用户从belong_to_circle中去掉
//step4********将圈子中的帖子从post表中删除掉，article表中有效位置0
function delete_circle(){
	$user_id=get_id();
	$cid=$_GET['cid'];
	$data['circle_effective']=0;
	//circle有效位置0
	$change_effective=M('circle')
	->where("circle_id=$cid")
	->save($data);
	//echo $change_effective;
	//圈子名称，用于显示信息
	$circle=M('circle')
	->where("circle_id=$cid")
	->find();
	//echo $circle.'<br/>';
	$circle_name=$circle['circle_name'];

	//找到加入该圈子的所有成员
	$belong_to_circle=M('belong_to_circle')
	->where("circle_id=$cid")
	->select();
	//计算有多少人属于该圈子
	$count=M('belong_to_circle')
	->where("circle_id=$cid")
	->count();
	//echo $count.'<br/>';


	$i=0;
	$count_m=0;

	if ($change_effective) {

		 foreach ($belong_to_circle as $key => $v) {

        
       	$a[$i]=$v['user_id'];
        $data['send_user_id']=$user_id;
        $data['receive_user_id']=$a[$i];
        $data['message_content']="您加入的圈子".$circle_name."已被圈主解散";
        $result[$i]=M('message')
        ->data($data)
        ->add();
        if($result[$i]){
        	$count_m++;
        }
        	 /*echo $a[$i];*/
        $i++;
        }
		
	}
	//echo $count_m.'<br/>';
	if($count_m==$count){
		 //发送私信后，将belong表中的用户-圈子归属关系删除
    	$delete_user=M('belong_to_circle')
    	->where("circle_id=$cid")
    	->delete();
	}
	//echo $delete_user.'<br/>';
   if($delete_user){
   	   //删除圈子中的帖子
   		$post = M('post')
   		->where("circle_id = $cid")
   		->find();
   		if($post){
   			$delete_post=M('post')
    		->where("circle_id = $cid")
    		->delete();
   		}
   		else{
   			$delete_post=1;
   		}

   }
	//echo $delete_post.'<br/>';
 
	if ($change_effective&&($count_m==$count)&&$delete_user&&$delete_post) {
        	$this->success("圈子已解散",U('/Home/Circle/index'));
        }
        else{
		$this->error('解散圈子失败，请重试...');
	}



}
//成员管理处理函数
//将某个成员踢出圈子
//在belong_to_circle表中删除信息，并以私信通知对方
public function kick(){
	//谁被踢出了哪个圈子
	$cid=$_GET['cid'];
	$this->assign('circle_id',$cid);
	$aid=$_GET['aid'];
	/*echo $cid;
	echo $aid;*/

	$result=M('belong_to_circle')
	->where("circle_id=$cid AND user_id=$aid")
	->delete();

	if($result){
		$this->success("您已经将该成员踢出圈子",U('/Home/Circle/manage_member/cid/'.$cid));
	}
	else{
		$this->error('成员删除失败，请重试...');
	}


}
//将某个管理员免去其管理员身份
//在belong_to_circle表中修改info字段
public function deprive(){
	$cid=$_GET['cid'];
	$this->assign('circle_id',$cid);
	$aid=$_GET['aid'];

	$data['belong_to_circle_info']='member';
	$result=M('belong_to_circle')
	->where("circle_id=$cid AND user_id=$aid")
	->save($data);

	if($result){
		$this->success("您已免除该成员的管理员身份",U('/Home/Circle/manage_member/cid/'.$cid));

	}

	else{
			$this->error('免除失败，请重试...');
		}
}

public function message(){
	//$user_id=$_SESSION['user_id'];
	$user_id=get_id();
	$cid=$_GET['cid'];
	$this->assign('circle_id',$cid);
	$uid=$_GET['uid'];

	$user=M('user')
	->where("user_id=$uid")
	->find();
	$this->assign('receiver_name',$user['user_nickname']);
	$this->assign('receiver_id',$uid);

	
	$this->display();

	/*$result=M('message')
	->data($data)
	->add();
	if($result){
		$this->success('发送成功',U('/Home/Circle/manage_member/cid/'.$cid));
	}
	else{
		$this->error('发送失败，请重试...');
	}*/



}

public function handleMessage(){
	$receive_user_id=I('post.receive_user_id');
	$user_id=get_id();
	//$user_id=get_id();

	$data=array(
		'send_user_id'=>$user_id,
		'receive_user_id'=>$receive_user_id,
		'message_content'=>I('post.message_content'),
		);

	$result=M('message')
	->data($data)
	->add();

	if($result){
			$data['type']=1;
			$this->ajaxReturn($data,'json');
		}
		else{
			$data['type']=0;
			$this->ajaxReturn($data,'json');
		}

}

public function admin(){
	$cid=$_GET['cid'];
	$this->assign('circle_id',$cid);
	$aid=$_GET['aid'];

	$admin_number=M('belong_to_circle')
	->where("circle_id=$cid AND belong_to_circle_info='admin'")
	->count();
	/*echo $admin_number;*/
	if($admin_number>=3){
		$this->error('管理员数已达上限');
	}
	else{
	$data['belong_to_circle_info']='admin';
	$result=M('belong_to_circle')
	->where("circle_id=$cid AND user_id=$aid")
	->save($data);

	if($result){
		$this->success("您已设置新的管理员",U('/Home/Circle/manage_member/cid/'.$cid));

	}

	else{
			$this->error('设定失败，请重试...');
		}

	}
}

//帖子管理函数
public function post_manage(){
	$cid=$_GET['cid'];
	$this->assign('circle_id',$cid);

	$circle=M('circle')
	->where("circle_id =$cid")
	->find();

	$circle_name=$circle['circle_name'];
	$this->assign('circle_name',$circle_name);

	$circle_avatar_url=$circle['circle_avatar_url'];
	$this->assign('circle_id',$cid);
	$this->assign('circle_avatar_url',$circle_avatar_url);

	$article=M('article');
	$post_list=$article
	->join('post ON post.article_id=article.article_id')
	->join('user ON user.user_id=article.user_id')
	->order('article.article_time desc')
	->where("post.circle_id=$cid AND article.article_effective=1")
	->select();
	$this->assign('post_list',$post_list);

	$post_list_activity=$article
	->join('post ON post.article_id=article.article_id')
	->join('user ON user.user_id=article.user_id')
	->where("post.circle_id=$cid AND (post.post_type=0 OR post.post_type=1)
		AND article.article_effective=1")
	->order('article.article_time desc')
	->select();
	$this->assign('post_list_activity',$post_list_activity);

	$post_list_chat=$article
	->join('post ON post.article_id=article.article_id')
	->join('user ON user.user_id=article.user_id')
	->where("post.circle_id=$cid AND post.post_type=2 
		AND article.article_effective=1")
	->order('article.article_time desc')
	->select();
	$this->assign('post_list_chat',$post_list_chat);

	$post_list_post=$article
	->join('post ON post.article_id=article.article_id')
	->join('user ON user.user_id=article.user_id')
	->where("post.circle_id=$cid AND post.post_type=3 
		AND article.article_effective=1")
	->order('article.article_time desc')
	->select();
	$this->assign('post_list_post',$post_list_post);

	$belong_to_circle=M('belong_to_circle');
	$user=M('user');
	$belong=$belong_to_circle
	->join('user ON user.user_id=belong_to_circle.user_id')
	->where("belong_to_circle.circle_id=$cid AND user.user_effective=1")
	->order('belong_to_circle.belong_time desc')
	->select();
	$this->assign('belong',$belong);

	$this->display('post_manage');

}

//删除圈子中的帖子
//article表中置为无效，post表中删除
public function delete_post(){
	//获取文章id
	$cid=$_GET['cid'];
	$this->assign('circle_id',$cid);
	$pid=$_GET['pid'];
	$data['article_effective']=0;
	$article_delete=M('article')
	->where("article_id=$pid")
	->save($data);

	$post_delete=M('post')
	->where("article_id=$pid")
	->delete();

	if($article_delete&&$post_delete){
		$this->success('删除成功',U('/Home/Circle/post_manage/cid/'.$cid));
	}
	else{
		$this->error('删除失败');
	}
}

public function data(){
	$cid=$_GET['cid'];
	$this->assign('circle_id',$cid);
	$circle=M('circle')
	->where("circle_id =$cid")
	->find();

	$circle_name=$circle['circle_name'];
	$this->assign('circle_name',$circle_name);
	$circle_avatar_url=$circle['circle_avatar_url'];
	$this->assign('circle_id',$cid);
	$this->assign('circle_avatar_url',$circle_avatar_url);

	//计算新增帖子数
	$article_count_week=M('article')
	->join('post ON article.article_id = post.article_id')
	->where("post.circle_id=$cid AND timestampdiff(day,article.article_time,now())<=7
		AND article.article_effective=1")
	->count();


	$article_count_month=M('article')
	->join('post ON article.article_id = post.article_id')
	->where("post.circle_id=$cid AND timestampdiff(day,article.article_time,now())<=30
		AND article.article_effective=1")
	->count(); 
    $this->assign('article_count_week',$article_count_week);
    $this->assign('article_count_month',$article_count_month);

    //计算新增评论数

    $comment_count_week=M('article')
    ->join ('post ON article.article_id = post.article_id')
    ->join ('comment ON comment.article_id = article.article_id')
    ->where("post.circle_id=$cid AND timestampdiff(day,comment.comment_time,now())<=7
		AND article.article_effective=1 AND comment.comment_effective=1")
    ->count();

    $comment_count_month=M('article')
    ->join ('post ON article.article_id = post.article_id')
    ->join ('comment ON comment.article_id = article.article_id')
    ->where("post.circle_id=$cid AND timestampdiff(day,comment.comment_time,now())<=30
		AND article.article_effective=1 AND comment.comment_effective=1")
    ->count();

    $this->assign('comment_count_week',$comment_count_week);
    $this->assign('comment_count_month',$comment_count_month);

    //计算新增成员数
    $member_count_week=M('belong_to_circle')
    ->join('user ON user.user_id= belong_to_circle.user_id')
    ->where("belong_to_circle.circle_id=$cid AND timestampdiff(day,belong_time,now())<=7
    	AND user_effective=1 AND belong_to_circle_info<>'quit' 
    	AND belong_to_circle_in_request<>1")
    ->count();

    $member_count_month=M('belong_to_circle')
    ->join('user ON user.user_id= belong_to_circle.user_id')
    ->where("belong_to_circle.circle_id=$cid AND timestampdiff(day,belong_time,now())<=30
    	AND user_effective=1 AND belong_to_circle_info<>'quit' 
    	AND belong_to_circle_in_request<>1")
    ->count();
    $this->assign('member_count_week',$member_count_week);
    $this->assign('member_count_month',$member_count_month);
    //退出成员数
    $quit_count_week=M('belong_to_circle')
    ->where("belong_to_circle.circle_id=$cid AND timestampdiff(day,belong_time,now())<=7
    	AND belong_to_circle_info='quit' AND belong_to_circle_in_request<>1")
    ->count();

    $quit_count_month=M('belong_to_circle')
    ->where("belong_to_circle.circle_id=$cid AND timestampdiff(day,belong_time,now())<=30
    	AND belong_to_circle_info='quit'")
    ->count();
    $this->assign('quit_count_week',$quit_count_week);
    $this->assign('quit_count_month',$quit_count_month);



	
	$this->display();

}
//处理评论内容
public function reply_to_article()
	{

		

		$this->base_reply_to_article();

		
	}

public function reply_to_comment()
	{
		/*if(!IS_POST) E('页面不存在！');
		//$user_id = get_id();
		$user_id=get_id();


		$data['user_reply_to_id'] = I('post.user_reply_to_id');
		$data['comment_id'] =  I('post.comment_id');
		$comment_id=I('post.comment_id');
		$data['second_comment_content'] = I('post.second_comment_content');
		$data['user_id']=$user_id;

		$result=M('second_comment')
		->data($data)
		-> add();
		$cid=I('post.circle_id');
		$aid=I('post.article_id');
		$result_add_number1=M('comment')
		->where("comment_id=$comment_id")
		->setInc('comment_number',1);

		$aid=I('post.article_id');
		$result_add_number2=M('article')
		->where("article_id=$aid")
		->setInc('article_comment_number',1);

		if($result && $result_add_number1 && $result_add_number2){
			$data['type']=1;
			$this->ajaxReturn($data,'json');
		}
		else{
			$data['type']=0;
			$this->ajaxReturn($data,'json');
		}*/

		$this->base_reply_to_comment();
	}


public function addtwo(){
        $m=M('comment');
        $data['status']=1;
        if($vo=$m->create()){
            if($m->add()){
            	
                $this->ajaxReturn($vo,'json');   
            }else{
            	
                $this->ajaxReturn(0,'json');   
            }   
        }else{
            $this->error($m->getError());   
        }
              
    }

public function add(){
      //ajaxReturn(数据,'提示信息',状态)   
      $m=M('comment');
     /* $data['comment_content']='content';*/
      $resulte=$m
      ->where("comment_id=66043909")
      ->find();
      $comment_content=$resulte['comment_content'];

      $data['comment_content']=I('comment_content');
		$data['article_id'] = 67108893;
		$data['user_id'] = 134217731;

		$result=$m
		->add($data);

      if($result){
      	$find=$m
      	->where("comment_id=66043909")
      	->find();

      	$this->ajaxReturn($find,'json');
      }
      
      /*if($m->add($_GET)){

         $this->ajaxReturn($data,'json');
         
       }else{
         $this->ajaxReturn(0,'json');   
       }*/
    }
}
?>