<?php
/*

前台公用函数页，包括赞踩，收藏，关注，举报，回复。

*/
namespace Home\Controller;
use Think\Controller;

class BaseController extends Controller {
	public $pic_url;  // 公用变量，上传图片后赋值，提交文章的时候需要用到。
//赞文章
	public function up_article(){
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$article_id=I('post.aid');
		$item=M('article')->where("article_id=$article_id")->find();
		$user_id=$item['user_id'];
		$curr_user_id=get_id();
		/*echo $user_id.'用户';*/
		/*echo $curr_user_id;*/
		$data['user_id'] = $curr_user_id;
		$data['article_id'] = $article_id;
		if ($curr_user_id == $user_id) {
			$data['type']=0;
            $this->ajaxReturn($data,'json');
        }		
		$obj = M('up_article');
		$opp = M('down_article');
		if($obj->where($data)->count()==0){//赞成的同时如果踩了该文章则取消踩
			/*if($opp->where($data)->count()==1){
				$opp->where($data)->delete();
				M('article')->where(array(
					'article_id' => $article_id
				))->setDec('article_down_number');
			}*/
			$result=$obj->add($data);
			if($result){
				M('article')->where(array(
                'article_id' => $article_id
           		 ))->setInc('article_up_number');
			}
			$data['type']=1;
            $this->ajaxReturn($data,'json');
		}else{
			$obj->where($data)->delete();
			M('article')->where(array(
                'article_id' => $article_id
            ))->setDec('article_up_number');
			$data['type']=2;
            $this->ajaxReturn($data,'json');
		} 
	}
//赞评论
	public function up_comment(){
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$curr_user_id=get_id();
		$data['user_id'] = $curr_user_id;
		$data['comment_id'] = $comment_id;
		$obj = M('up_comment');
		$opp = M('down_comment');
		if ($curr_user_id == $user_id) {
			$data['type']=0;
            $this->ajaxReturn($data,'json');
        }	
		if($obj->where($data)->count()==0){
			/*if($opp->where($data)->count()==1){
				$opp->where($data)->delete();
				M('comment')->where(array(
					'comment_id' => $comment_id
				))->setDec('comment_down_number');
			}*/
			$result=$obj->add($data);
			if($result){
				M('comment')->where(array(
	                'comment_id' => $comment_id
	            ))->setInc('comment_up_number');
			}
            $data['type'] = 1;
			$this->ajaxReturn($data,'json');
		}else{
			$obj->where($data)->delete();
			M('comment')->where(array(
                'comment_id' => $comment_id
            ))->setDec('comment_up_number');
            $data['type'] = 2;
			$this->ajaxReturn($data,'json');
		} 
	}

//踩文章
	public function down_article(){
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$article_id=I('post.aid');
		$item=M('article')->where("article_id=$article_id")->find();
		$user_id=$item['user_id'];
		$curr_user_id=get_id();
		$data['user_id'] = $curr_user_id;
		$data['article_id'] = $article_id;
		if ($curr_user_id == $user_id) {
			$data['type']=0;
            $this->ajaxReturn($data,'json');
        }	
		$obj = M('down_article');
		$opp = M('up_article');
		if($obj->where($data)->count()==0){
			/*if($opp->where($data)->count()==1){
				$opp->where($data)->delete();
				M('article')->where(array(
					'article_id' => $article_id
				))->setDec('article_up_number');
			}*/
			$result=$obj->add($data);
			if($result){
				M('article')->where(array(
	                'article_id' => $article_id
	            ))->setInc('article_down_number');
			}
			$data['type']=1;
            $this->ajaxReturn($data,'json');
		}else{
			$obj->where($data)->delete();
			M('article')->where(array(
                'article_id' => $article_id
            ))->setDec('article_down_number');
			$data['type']=2;
            $this->ajaxReturn($data,'json');	
		} 
	}

//踩评论
	public function down_comment()
	{
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$curr_user_id = get_id();
		$data['user_id'] = $curr_user_id;
		$data['comment_id'] = $comment_id;
		if ($curr_user_id == $user_id) {
			$data['type']=0;
            $this->ajaxReturn($data,'json');
        }	
		$obj = M('down_comment');
		$opp = M('up_comment');
		if($obj->where($data)->count()==0){
			/*if($opp->where($data)->count()==1){
				$opp->where($data)->delete;
				M('comment')->where(array(
					'comment_id'=>$comment_id
				))->setDec('comment_up_number');
			}*/
			$result=$obj->add($data);
			if($result){
			M('comment')->where(array(
                'comment_id' => $comment_id
            ))->setInc('comment_down_number');
			}
			$data['type']=1;
            $this->ajaxReturn($data,'json');
		}else{
			$obj->where($data)->delete();
			M('comment')->where(array(
                'comment_id' => $comment_id
            ))->setDec('comment_down_number');
			$data['type']=2;
            $this->ajaxReturn($data,'json');
		} 
	}

//加关注
//@作者:邓茜
	public function focus() {
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$article_id=I('post.aid');
		$item=M('article')->where("article_id=$article_id")->find();
		$user_id=$item['user_id'];
		$curr_user_id=get_id();
		$data['user_id'] = $curr_user_id;
		$data['article_id'] = $article_id;
        if ($curr_user_id == $user_id) {
			$data['type']=0;
            $this->ajaxReturn($data,'json');
        }	
        $focus = M('focus_on_article');
        if ($focus->where($data)->count()==0) {
            $result=$focus->add($data);
            if($result){
            	 M('article')->where(array(
                'article_id' => $article_id
           		 ))->setInc('article_focus_number');
            }
			$data['type']=1;
            $this->ajaxReturn($data,'json');
        } else {
            $focus->where($data)->delete();
            M('article')->where(array(
                'article_id' => $article_id
            ))->setDec('article_focus_number');
			$data['type']=2;
            $this->ajaxReturn($data,'json');
        }  		
	}
//加收藏
//@作者:邓茜
	public function collect() {
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$article_id=I('post.aid');
		$item=M('article')->where("article_id=$article_id")->find();
		$user_id=$item['user_id'];
		$curr_user_id=get_id();
		$data['user_id'] = $curr_user_id;
		$data['article_id'] = $article_id;
        if ($curr_user_id == $user_id) {
			$data['type']=0;
            $this->ajaxReturn($data,'json');
        }
        $focus = M('collect_article');
        if ($focus->where($data)->count()==0) {
            $result=$focus->add($data);
            if($result){
            	M('article')->where(array(
                'article_id' => $article_id
          	 	))->setInc('article_collect_number');
            }
			$data['type']=1;
            $this->ajaxReturn($data,'json');
            
        } else {
            $focus->where($data)->delete();
			M('article')->where(array(
                'article_id' => $article_id
            ))->setDec('article_collect_number');
            $data['type']=2;
            $this->ajaxReturn($data,'json');
        }  		
	}
//举报文章
//@作者:邓茜
	public function report_article(){
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$curr_user_id = get_id();
		$data['user_id'] = $curr_user_id;
		$article_id = I('post.aid');
        $data['article_id'] = $article_id;
        if ($curr_user_id == $user_id) {
			$data['type']=0;
            $this->ajaxReturn($data,'json');
        }
        $focus = M('report_article');
        if ($focus->where($data)->count()==0) {
            $focus->add($data);
            $data['type']=1;
            $this->ajaxReturn($data,'json');
        } else {
            $focus->where($data)->delete();
            $data['type']=2;
            $this->ajaxReturn($data,'json');
        }  		
	}
//举报评论
//@作者:邓茜
	public function report_comment($comment_id) {
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$curr_user_id = get_id();
		$data['user_id'] = $curr_user_id;
        $data['comment_id'] = $comment_id;
        if ($curr_user_id == $user_id) {
			$data['type']=0;
            $this->ajaxReturn($data,'json');
        }
        $focus = M('report_comment');
        if ($focus->where($data)->count()==0) {
            $focus->add($data);
            $data['type']=1;
            $this->ajaxReturn($data,'json');
        } else {
            $focus->where($data)->delete();
            $data['type']=2;
            $this->ajaxReturn($data,'json');
        }  		
	}
	public function verify()
	{
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$user_id = get_id(false);
		$data['user_id'] = $user_id;
		if($data['user_id'] == 0)
		{
			$data['type'] = 0;
			$this->ajaxReturn($data,'json');
		}
		$article_type=I('post.article_type');
		switch ($article_type) {
			case 'policy':
				//判断是否为政府用户
				$user_type=ac_by_id($user_id);
				/*echo $user_type;
				echo '政府类别'.C('GOVERNMENT');*/
				if($user_type != C('GOVERNMENT')){
					$data['type'] = 1;
					$this->ajaxReturn($data,'json');
				}
				break;
			case 'project':
				$user_type=ac_by_id($user_id);
				if($user_type==C('STARTUP') || $user_type==C('ENTERPRISE')){
					$data['type'] = 2;
					$this->ajaxReturn($data,'json');
				}else if($user_type==C('NO_ORG')){
					$data['type'] = 3;
					$this->ajaxReturn($data,'json');
				}else{
					$data['type'] = 4;
					$this->ajaxReturn($data,'json');
				}
				break;
			default:
				break;
		}
		$data['type'] = 2;
		$this->ajaxReturn($data,'json');
		
	}
//回复文章
//@作者 
//建议写在公用库函数
	public function reply_to_article($article_id)
	{
		//$user_id = get_id();
		//$curr_user_id = get_id();
		$curr_user_id = $_SESSION['user_id'];
		$data['user_id'] = $curr_user_id;
		if ($curr_user_id == $user_id) {
			$type=1;
            $this->ajaxReturn($type,'json');
        }
		//$user_id = $_SESSION('user_id');
		$comment = M('comment');
		$data['user_id'] = $user_id;
		$data['article_id'] = $article_id;
		$data['comment_content'] = I('post.comment_content');
		$comment -> add($data);
		$this->display();	
	}
//回复评论
//@作者 
//建议写在公用库函数
	public function reply_to_comment($comment_id)
	{
		$user_id = get_id();
		//$user_id = $_SESSION('user_id');
		$comment = M('comment');
		$comment_id = I('post.comment_id');
		$data['comment_content'] = I('post.comment_content');
		$data['comment_id_reply_to'] = $comment_id;
		$data['user_id'] = $user_id;
		$comment_item = $comment
		->join('user ON user.user_id = comment.user_id')
		->where(array('comment_id'=>$comment_id))->find();
		$user_name = $comment_item['user_name'];
		$curr_user_id = get_id();
		$this->user_name = $user_name;
		$this->display();
		
		// }	
	}
	

//保存草稿,所有不同的文章类型都放在同一个地方。根据传过来的文章类型对不同的数据表进行处理
//@作者 
	public function save()
	{
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$user_id = get_id(false);	
		$article_type=I('post.article_type');
		switch($article_type){
			case 'policy':
				$data['article_type'] = C('POLICY_TYPE');
				$data['article_profile']=I('post.profile');
				break;
			case 'project':
				$data['article_type'] = C('PROJECT_TYPE');
				$data['article_profile']=I('post.profile');
				break;
			case 'question':
				$data['article_type'] = C('QUESTION_TYPE');
				break;	
			case 'post':
				$data['article_type'] = C('POST_TYPE');
				break;
		}
		$data['user_id'] = $user_id;
		$data['article_title']=I('post.title');				
		$data['article_content']=I('post.content');	
		$data['article_draft']=1;			
		$article = D('Article');
		$result = $article->create($data);
		if(!$result){
			$this->ajaxReturn($article->getError(),'json');
		}
		//dump($result);
		if($result){
			$article_id=$article->add();
			$data1['article_id']=$article_id;
			switch ($article_type) {
				case 'project':
					$pic_name = I('post.pic_name');
					if($pic_name != "")
						$data1['project_avatar_url']=__ROOT__.'/Uploads/Img/article/project/'.$pic_name.'.png';
					if(ac_by_id($user_id,2))
						$data1['user_id']=$user_id;
					else {	
						$cond['user_id'] = $user_id;
						$user = M('belong_to_organization')
								->where($cond)
								->find();
						$org_user_id = $user['organization_user_id'];
						$data1['user_id'] = $org_user_id;
					}
					$result = M('project')->add($data1);
					break;
				case 'policy':
					if(ac_by_id($user_id,2))
							$data1['user_id']=$user_id;
					else {
						$cond['user_id'] = $user_id;
						$user = M('belong_to_organization')
								->where($cond)
								->find();
						$data1['user_id']=$user['organization_user_id'];
					}
					$result = M('policy')->add($data1);
					break;
				case 'question':
					$result = M('question')->add($data1);
					break;
				default:
					break;
			}
		}
		$tag_list=I('post.array');
		$article_tag=M('article_have_tag');
		$tag=M('tag');
		$num=0;
		$data1['article_id']=$article_id;
		foreach ($tag_list as $key => $value) {
			$data['tag_title']=$value;
			$data1['tag_id']=$tag->add($data);
			$article_tag->add($data1);
		}	
		if(!$result){
		$data['type'] = 1; 
		$this->ajaxReturn($data,'json');
		return;
		}else{
			$data['article_id']=$article_id;
			$data['type']=2;
			$this->ajaxReturn($data,'json');
		} 
	}

//文章发布提交 这里也是根据不同的文章类型进行不同的处理
//数据存档和数据提交数据库
//@作者 
	public function submit()
	{
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$user_id = get_id(false);	
		$article_type=I('post.article_type');
		switch($article_type){
			//时光机
			case 'talk':
				$data['article_type'] = C('TALK_TYPE');
				break;
			//创意汇
			case 'idea':
				$data['article_type'] = C('IDEA_TYPE');
				break;
			//风投
			case 'vc':
				$data['article_type'] = C('VC_TYPE');
				break;
			//孵化器
			case 'incubator':
				$data['article_type'] = C('INCUBATOR_TYPE');
				break;
			//政策
			case 'policy':
				$data['article_type'] = C('POLICY_TYPE');
				$data['article_profile']=I('post.profile');
				break;
			//项目
			case 'project':
				$data['article_type'] = C('PROJECT_TYPE');
				$data['article_profile']=I('post.profile');
				break;
			//问题
			case 'question':
				$data['article_type'] = C('QUESTION_TYPE');
				break;
			//帖子	
			case 'post':
				$data['article_type'] = C('POST_TYPE');
				break;
		}
		//$data['user_id'] = $user_id;
		$data['user_id'] = 134217735;
		$data['article_title']=I('post.title');				
		$data['article_content']=I('post.content');	
		if($pic_url != '')
			$data['article_picture_url']=$pic_url;
    	//$pic_name = I('post.pic_name');
    	//判断是否有传图片
    	/*if($pic_name){
    		$sava_name=time();
    		$url=upload_file( $savePath, $sava_name,"picture");
    		$data['article_picture_url'] = $url;	
    	}*/
		$article = D('Article');
		$result = $article->create($data);
		if(!$result){
			$this->ajaxReturn($article->getError(),'json');
		}
		//dump($result);
		if($result){
			$article_id=$article->add($data);
			//echo '提交文章';
			//echo $article_id;
			$data1['article_id']=$article_id;
			switch ($article_type) {
				case 'project':
					$pic_name = I('post.pic_name');
					if($pic_name != "")
						//$data1['project_avatar_url']=__ROOT__.'/Uploads/Img/article/project/'.$pic_name.'.png';
						$data1['project_avatar_url'] = $pic_name;
					if(ac_by_id($user_id,2))
						$data1['user_id']=$user_id;
					else {				
						$cond['user_id'] = $user_id;
						$user = M('belong_to_organization')
							->where($cond)
							->find();
						$org_user_id = $user['organization_user_id'];
						$data1['user_id'] = $org_user_id;
					}
					$result = M('project')->add($data1);
					break;
				case 'policy':
					if(ac_by_id($user_id,2)){
						$data1['user_id']=$user_id;
					}
						
					else {
						$user = M('belong_to_organization')
						->where("user_id = $user_id")
						->find();
						$data1['user_id']=$user['organization_user_id'];
					}
					$result = M('policy')->add($data1);
					break;
				case 'question':
					$result = M('question')->add($data1);
					break;
				default:
					break;
			}
		}
		/*$tag_list=I('post.array');
		$article_tag=M('article_have_tag');
		$tag=M('tag');
		$num=0;
		$data1['article_id']=$article_id;
		foreach ($tag_list as $key => $value) {
			$data['tag_title']=$value;
			$data1['tag_id']=$tag->add($data);
			$article_tag->add($data1);
		}	*/
		if(!$result){
		$data['type'] = 1; 
		$this->ajaxReturn($data,'json');
		return;
		}else{
			$data['article_id']=$article_id;
			$data['type']=2;
			$this->ajaxReturn($data,'json');
		}
	}
//编辑页面再次保存成草稿。
//@作者 
	public function re_save($aid)
	{
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$user_id = get_id();
	    $article_id = $aid;
		$user = M('user');
		$curr_user = $user
		->where("user_id=$user_id")
		->find();
		$article_type = I('post.article_type');
		switch($article_type)
		{
			case 'project':
				$pic_name = I('post.pic_name');
				if($pic_name != ""){
					$data2['project_avatar_url']=__ROOT__.'/Uploads/Img/article/project/'.$pic_name.'.png';
					$project = M('project')
					->where("article_id = $article_id")
					->save($data2);
				}
				$data['article_type'] = C('PROJECT_TYPE');
				$data['article_profile']=I('post.profile');
				break;
			case 'policy':
				$data['article_type'] = C('POLICY_TYPE');
				$data['article_profile']=I('post.profile');
				break;
			case 'question':
				$data['article_type'] = C('QUESTION_TYPE');
				break;
			case 'post':
				$data['article_type'] = C('POST_TYPE');
				break;
		}
			$data['user_id']=$user_id;
			$data['article_title']=I('post.title');
			$data['article_content']=I('post.content');
			$data['article_draft']=1;
			$tag_list=I('post.array');
			$article_tag=M('article_have_tag');
			$tag=M('tag');
			$num=0;
			$data1['article_id']=$article_id;
			foreach ($tag_list as $key => $value) {
				$data['tag_title']=$value;
				$data1['tag_id']=$tag->add($data);
				$article_tag->add($data1);
			}
			$article = M('article');
			$result = $article
			->where("article_id = $article_id")
			->save($data);
			if($result === false)
			{
				$this->error('保存失败！');
				return;
			}
			$data['article_id'] = $article_id;
			$data['type']=2;
			$this->ajaxReturn($data,'json');
		
	}
//编辑页面编辑完成之后的发布。
	public function re_submit($aid)
	{
		if(!IS_AJAX){
			$this->error('页面不存在！');
			return;
		}
		$user_id = get_id();
	    $article_id = $aid;
		$user = M('user');
		$curr_user = $user
		->where("user_id=$user_id")
		->find();
		$article_type = I('post.article_type');
		$article_effective=1;
		switch($article_type)
		{
			//时光机
			case 'talk':
				$data['article_type'] = C('TALK_TYPE');
				break;
			//创意汇
			case 'idea':
				$data['article_type'] = C('IDEA_TYPE');
				break;
			//风投
			case 'vc':
				$data['article_type'] = C('VC_TYPE');
				break;
			//孵化器
			case 'incubator':
				$data['article_type'] = C('INCUBATOR_TYPE');
				break;
			case 'project':
				$data['article_type'] = C('PROJECT_TYPE');
				$data['article_profile']=I('post.profile');
				$pic_name = I('post.pic_name');
				if($pic_name != ""){
					$data2['project_avatar_url']=__ROOT__.'/Uploads/Img/article/project/'.$pic_name.'.png';
					$project = M('project')
					->where("article_id = $article_id")
					->save($data2);
				}
				break;
			case 'policy':
				$data['article_type'] = C('POLICY_TYPE');
				$data['article_profile']=I('post.profile');
				break;
			case 'question':
				$data['article_type'] = C('QUESTION_TYPE');
				break;
			case 'post':
				$data['article_type'] = C('POST_TYPE');
				break;
		}
			$data['user_id']=$user_id;
			$data['article_title']=I('post.title');
			$data['article_content']=I('post.content');
			$data['article_draft']=0;
			$tag_list=I('post.array');
			$article_tag=M('article_have_tag');
			$tag=M('tag');
			$num=0;
			$data1['article_id']=$article_id;
			foreach ($tag_list as $key => $value) {
				$data['tag_title']=$value;
				$data1['tag_id']=$tag->add($data);
				$article_tag->add($data1);
			}
			$article = M('article');
			$result = $article
			->where("article_id = $article_id")
			->save($data);
			if($result === false)
			{
				$this->error('发布失败！');
				return;
			}
			$data['article_id'] = $article_id;
			$data['type']=2;
			$this->ajaxReturn($data,'json');
	}
//删除函数。将文章的有效位置0
	public function delete($aid){
		$article_id = $aid;
		$result = M('article')
		->where("article_id = $article_id")
		->setField('article_effective',0);
		$article= M('article')
		->where("article_id = $article_id")
		->find();
		$article_type = $article['article_type'];
		if($result){
			switch ($article_type) {
			//时光机
			case C('TALK_TYPE'):
				$this->success('删除成功',U('/Home/Project/index'));
				break;
			//创意汇
			case C('IDEA_TYPE'):
				$this->success('删除成功',U('/Home/Project/index'));
				break;
			//风投
			case C('VC_TYPE'):
				$this->success('删除成功',U('/Home/Project/index'));
				break;
			//孵化器
			case C('INCUBATOR_TYPE'):
				$this->success('删除成功',U('/Home/Project/index'));
				break;
			case C("PROJECT_TYPE"):
				$this->success('删除成功',U('/Home/Project/index'));
				break;
			case C("POLICY_TYPE"):
				$this->success('删除成功',U('/Home/Policy/index'));
				break;
			default:
				$this->success('删除成功',U('/Home/Question/index'));
				break;
			}
		}else{
			$this->error('删除失败');
			return;
		}
	}

 	public function withdraw_comment($aid,$article_type){
		// 导入分页类
		import('ORG.Util.Page');
		// 找到文章评论数
		$count = M('article')
		->where("article_id=$aid")
		->find();
		$article_comment_number=$count['article_comment_number'];// 查询满足要求的总记录数
		$Page = new \Think\Page($article_comment_number,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page
		->show();// 分页显示输出
		

		$project_comment=C('PROJECT_COMMENT');
		$this->assign('project_comment',$project_comment);
		$project_improve=C('PROJECT_IMPROVE');

		//提取评论列表
		switch ($article_type) {
			case C('QUESTION_TYPE'):
			$user_comment=M('comment')
				->join("user ON comment.user_id=user.user_id")
				//->join("second_comment ON second_comment.comment_id=comment.comment_id",'left')
				->where("comment.article_id=$aid")
				->field('user_nickname,user_avatar_url,comment_time,user.user_id,comment_content,comment_id as id,comment_id')
				->limit($Page->firstRow.','.$Page->listRows)
				->order("(comment_up_number-comment_down_number) desc")
				->select();
				break;
			case C('POLICY_TYPE'):
				$user_comment=M('comment')
				->join("user ON comment.user_id=user.user_id")
				//->join("second_comment ON second_comment.comment_id=comment.comment_id",'left')
				->where("comment.article_id=$aid")
				->field('user_nickname,user_avatar_url,comment_time,user.user_id,comment_content,comment_id as id,comment_id')
				->limit($Page->firstRow.','.$Page->listRows)
				->order("comment_number desc,comment_up_number-comment_down_number,comment_time desc")
				->select();
				break;
			case C('PROJECT_TYPE'):
			//重新分页
				$count = M('article')
				->join("comment ON article.article_id=comment.article_id")
				->where("article.article_id=$aid AND comment.comment_type=$project_comment")
				->count();
				$article_comment_number=$count;// 查询满足要求的总记录数
				$Page = new \Think\Page($article_comment_number,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
				$show = $Page
				->show();// 分页显示输出

				$this->assign('article_comment_number',$article_comment_number);
			//提取comment
				$user_comment=M('comment')
				->join("user ON comment.user_id=user.user_id")
				//->join("second_comment ON second_comment.comment_id=comment.comment_id",'left')
				->where("comment.article_id=$aid AND comment_type=$project_comment")
				->field('user_nickname,user_avatar_url,comment_time,user.user_id,comment_content,comment_id as id,comment_id')
				->limit($Page->firstRow.','.$Page->listRows)
				->order("(comment_up_number-comment_down_number) desc")
				->select();
			//提取improve数量
				$icount = M('article')
				->join("comment ON article.article_id=comment.article_id")
				->where("article.article_id=$aid AND comment.comment_type=$project_improve")
				->count();
				$article_improve_number=$icount;// 查询满足要求的总记录数
				$iPage = new \Think\Page($article_improve_number,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
				$ishow = $iPage
				->show();// 分页显示输出

				$this->assign('ipage',$ishow);

				$user_improve=M('comment')
				->join("user ON comment.user_id=user.user_id")
				//->join("second_comment ON second_comment.comment_id=comment.comment_id",'left')
				->where("comment.article_id=$aid AND comment_type=$project_improve")
				->field('user_nickname,user_avatar_url,comment_time,user.user_id,comment_content,comment_id as id,comment_id')
				->limit($iPage->firstRow.','.$iPage->listRows)
				->order("(comment_up_number-comment_down_number) desc")
				->select();

						//修改历史2014-3-6 增加article_comment_number
				$this->assign('article_improve_number',$article_improve_number);
				break;
			case C('POST_TYPE'):
				$user_comment=M('comment')
				->join("user ON comment.user_id=user.user_id")
				//->join("second_comment ON second_comment.comment_id=comment.comment_id",'left')
				->where("comment.article_id=$aid")
				->field('user_nickname,user_avatar_url,comment_time,user.user_id,comment_content,comment_id as id,comment_id')
				->limit($Page->firstRow.','.$Page->listRows)
				->order("comment_time desc")
				->select();
				break;		
			default:
				break;
		}
		//修改历史2014-3-6 增加article_comment_number
		$this->assign('article_comment_number',$article_comment_number);
		$this->assign('user_comment',$user_comment);
		$this->assign('page',$show);// 赋值分页输出


		//提取二级评论
		foreach($user_comment as $n=> $val){
	      $user_comment[$n]['comment_id']=M('second_comment')
	      ->join('user send_user ON send_user.user_id=second_comment.user_id')
		  ->join('user ON user.user_id=second_comment.user_reply_to_id')
		  ->field('second_comment_time,second_comment_content,user.user_nickname as receiver_name
		  	,send_user.user_nickname as sender_name,send_user.user_id as user_id,
		  	send_user.user_avatar_url as send_user_avatar_url')
	      ->where('comment_id=\''.$val['comment_id'].'\'')
	      ->select();
	      
	     }

	     if($user_improve){
	     	foreach($user_improve as $n=> $val){
	      	$user_improve[$n]['comment_id']=M('second_comment')
	      	->join('user send_user ON send_user.user_id=second_comment.user_id')
		  	->join('user ON user.user_id=second_comment.user_reply_to_id')
		  	->field('second_comment_time,second_comment_content,user.user_nickname as receiver_name
		  	,send_user.user_nickname as sender_name,send_user.user_id as user_id')
	      	->where('comment_id=\''.$val['comment_id'].'\'')
	      	->select();

	      	$this->assign('user_improve',$user_improve);
	     }
	 	}

	     $this->assign('user_comment',$user_comment);
	     $this->assign('article_id',$aid);
	}

	public function base_reply_to_article(){
		if(!IS_POST) E('页面不存在！');
		//$user_id = get_id();

		$user_id=get_id();

		//$user_id='134217729';
		$data['user_id'] = $user_id;
		$data['article_id'] =  I('post.article_id');
		$data['comment_content'] = I('post.comment_content');
		$aid=I('post.article_id');
		$comment_type=I('post.comment_type');

		
		switch ($comment_type) {
			case 'circle_post_comment':
				$data['comment_type']=C('CIRCLE_POST_COMMENT');
				break;

			case 'question_comment':
				$data['comment_type']=C('QUESTION_COMMENT');
				break;
			case 'policy_comment':
				$data['comment_type']=C('POLICY_COMMENT');
				break;
			case 'project_improve':
				$data['comment_type']=C('PROJECT_IMPROVE');
				break;
			case 'project_comment':
				$data['comment_type']=C('PROJECT_COMMENT');
				break;
			default:
				break;
		}
		$result=M('comment') 
		->data($data)
		-> add();
		//$cid=I('post.circle_id');
		if($result){
			$result_add_number=M('article')
			->where("article_id=$aid")
			->setInc('article_comment_number',1);
		}		


		if($result && $result_add_number){
			$data['type']=1;
			$this->ajaxReturn($data,'json');
		}
		else{
			$data['type']=0;
			$this->ajaxReturn($data,'json');
		}
		
	}

	public function base_reply_to_comment(){
		if(!IS_POST) E('页面不存在！');
		//$user_id = get_id();
		$user_id=get_id();
		//$user_id='134217729';


		$data['user_reply_to_id'] = I('post.user_reply_to_id');
		$data['comment_id'] =  I('post.comment_id');
		$comment_id=I('post.comment_id');
		$data['second_comment_content'] = I('post.second_comment_content');
		$data['user_id']=$user_id;

		$result=M('second_comment')
		->data($data)
		-> add();

		$aid=I('post.article_id');
		$result_add_number1=M('comment')
		->where("comment_id=$comment_id")
		->setInc('comment_number',1);

		$aid=I('post.article_id');
		/*$result_add_number2=M('article')
		->where("article_id=$aid")
		->setInc('article_comment_number',1);*/

		if($result && $result_add_number1 ){
			$data['type']=1;
			$this->ajaxReturn($data,'json');
		}
		else{
			$data['type']=0;
			$this->ajaxReturn($data,'json');
		}
	}

public function test(){
	/*dump(ac_by_id(get_id()));
	dump(M('User')->getByUserId(get_id()));
	echo ac(5);*/
	$time = time(); 
	echo $time;
}
public function uploadPicture(){
        $saveName=time();
        $savePath  = '/Img/Article/project';
        //$url=upload_file( $savePath, $saveName, "photo" );
        $file = $_FILES["photo"];
        dump($file);
        if ( $url==null ) {
          // 上传错误
          // $this->error( "图片上传失败！" );
          // return;
        }else {
          $pic_url = $url;
          $this->success( "修改成功！" );
    }
}
/*function upload_file1( $savePath, $saveName, $postName, $fileexts="img" ) {
  $upload = new \Think\Upload();// 实例化上传类
  $upload->maxSize   =     5*1024*1024;//5M ;// 设置附件上传大小
  // 设置附件上传类型
  if ( is_array( $fileexts ) ) {
    $upload->exts =$fileexts;
  }  elseif ( strcasecmp( $fileexts, "img" )==0 ) {
    $upload->exts      =   array( 'jpg', 'jpeg', 'png', 'gif' );
  }elseif ( strcasecmp( $fileexts, "doc" )==0 ) {
    $upload->exts=array( 'doc', 'docx', 'pdf', 'wps', 'txt', 'htm', 'html' );
  }else {
    $upload->exts=null;
  }
  $upload->savePath = __ROOT__'./Uploads/'.$savePath; // 设置附件上传目录
  $upload->saveName = $saveName;
  $upload->autoSub =false;//不创建子目录
  $upload->replace =ture;//同名覆盖
  // 上传单个文件
  $info   =   $upload->uploadOne( $_FILES[$postName] );
  if ( !$info ) {
    // 上传错误
    return null;
  }else {
    // 上传成功 获取上传文件信息
    return $info;
    $st = new SaeStorage();
    return $st->getUrl( 'Uploads', 'upload' ).'/'.$savePath.$info['savename'];
  }
}*/
}