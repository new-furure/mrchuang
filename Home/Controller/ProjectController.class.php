<?php
/*
+------------------------------------------------
+				项目模块
+ 初稿 ：NewFuture
+ 完善 ：建男 茜茜
+可能需要其他函数，自行添加，
+写清楚注释 @▽@
+关注 赞踩 这些建议写在公用函数库
+-------------------------------------------------
*/
namespace Home\Controller;
use Think\Controller;

class ProjectController extends BaseController {
	
/*-----------------分割线---------------------------
		
		项目区域，不可共享	
---------------------分割线------------------------*/
//项目浏览页
//@作者 邓茜

	public function index()
	{
		$project = M('project');
		$condition['article_effective'] = 1;
		$condition['article_draft'] = 0;
		$condition['article_type'] = C('PROJECT_TYPE');
		$count = $project
		->join('article ON article.article_id = project.article_id')
		->where($condition)
		->count();
		import('ORG.Util.Page');
		$Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page
		->show();
		$this->assign('page',$show);// 赋值分页输出
		
		$project_list = $project
		->join('article ON article.article_id = project.article_id')
		->join('user ON user.user_id = project.user_id')
		->where($condition)	
		->order('article_time DESC')
		->field('article.article_id,project.user_id,article_profile,article_up_number,article_focus_number,
			article_title,article_time,user_nickname,user_avatar_url,project_avatar_url')
		->limit($Page->firstRow.','.$Page->listRows)
		->select();
		$tag = M('article_have_tag');
		$tag = $tag
		->join('tag ON article_have_tag.tag_id = tag.tag_id')
		->select();
		/*
		我关注的项目列表，项目模块暂时用不上。
		if ($user_id != '')
		{
			$focus_on_article = M('focus_on_article');
			$focus = $focus_on_article->where(array(
				'user_id' => $user_id
			))->select();
			$total_count = $focus_on_article->where(array(
				'user_id' => $user_id
			))->count(); //获取关注的项目数。
			$my_focus = array(); //关注的项目数组。
			foreach ($focus as $k => $v) {
				if ($v['user_id'] == $user_id) {
					$my_focus[] = $v['article_id'];
				}
			}
		}	*/
		//$this->assign('my_focus', $my_focus);
		$this->assign('count', $count);//总项目数	
		$this->assign('project_list', $project_list);
		$this->assign('tag',$tag_list);
		$this->display(); 
	}
//搜索
//建议写在通用库中

//单个项目查看页
//@作者 
	public function detail($aid)
	{
		$article = M('article');
		$comment = M('comment');
		$article_id = $aid;
		//查找项目标签
		$tag=M('tag')
		->join('article_have_tag A ON A.tag_id = tag.tag_id')
		->where("article_id = $article_id")
		->select();
		$this->tag=$tag;
		//查找到项目	
		$article_item = $article
		->join('project ON project.article_id = article.article_id')
		->join('user ON user.user_id = project.user_id')
		->where("article.article_id = $article_id and article_effective=1 and article_draft=0")
		->find();
		
		$focus_list = M('focus_on_article')
		->join('user ON user.user_id=focus_on_article.user_id')
		->where("article_id = $article_id and user_type")
		->select();

		$this->withdraw_comment($aid,$article_item['article_type']);
		$this->assign('article_id',$article_id);
		
		if($article_item) {
			//点击次数更新
			$article->where("article_id =$article_id")->setInc('article_hits');
			$curr_user_id = get_id(false);
			$this->assign('data',$article_item);
			$this->assign('curr_user_id',$curr_user_id);

		}else{
			$this->error('您查看的文章不存在');
			return;
		}	
		$this->display();
	}
	
	public function projectstep2()
	{
		$tag=M('tag');
		$tag_list = $tag->limit(10)->select();
		$this->tag_list= $tag_list;//标签按照热度倒排
		$this->display();
	}
// 项目填写
//@作者 
	public function publish()
	{
		$user_id=get_id();
		$cond['user_id'] = $user_id;
		$user =  M('belong_to_organization')
				->where($cond)
				->find();
		$org_user_id = $user['organization_user_id'];
		/*echo $user_id;
		echo $org_user_id;*/
		$tag = M('tag')->limit(10)->select();
		$this->assign('tag_list',$tag);
		$this->assign('user_id',$user_id);
		$this->assign('org_user_id',$org_user_id);
		$this->display();

	}
// 项目修改
//@作者 
	public function edit($aid)
	{
		$article = M('article');
		$article_id = $aid;
		$data = $article
		->join('project ON project.article_id = article.article_id')
		->join('user ON user.user_id = project.user_id')
		->where("article.article_id = $article_id")
		->find();
		$tag=M('tag')
		->join('article_have_tag A ON A.tag_id = tag.tag_id')
		->where("article_id = $article_id")
		->select();
		$this->tag_list=$tag;
		$this->data = $data;
		$this->display();
	}
/*
项目页的我要参与
*/
	public function join(){
		$article_id = I('post.aid');
		$user_id = get_id(false);
		if($user_id == 0){
			$data['type'] =0;
			$this->ajaxReturn($data,'json');
		}
		$content = I('post.content');
		$article=M('article')->where("article_id = $article_id")->find();
		$article_title = $article['article_title'];
		$notice = M('notice');
		$user=M('user')->where("user_id = $user_id")->find();
		$user_nickname = $user['user_nickname'];
		$user_url = U("/Home/User/info/id/".$user_id);
		$article_url = U("/Home/Project/detail/aid/".$article_id);
		$data['notice_title'] = '<a href='.$user_url.'>'.$user_nickname.'</a>
		<h>想要参与您的项目:</h><a href='.$article_url.'>'.$article_title.'</a>';
		$data['notice_content'] = $content;
		$result = $notice->add($data);
		/*if($result){*/
			/*echo $data['notice_title'];
			echo $content;*/
		/*}*/
		$data['type']=1;
		$this->ajaxReturn($data,'json');
	}
//项目发布的注册组织步骤。
	public function publish_step1(){

		//A('User')->reg(U('Project/publish'),ture);
		$this->isorg = true;
		$this->goto = U('Project/publish');
		$this->display();
	}
//回复文章
	public function reply_to_article(){
		$this->base_reply_to_article();
	}
//回复评论。
	public function reply_to_comment(){
		$this->base_reply_to_comment();
	}
}