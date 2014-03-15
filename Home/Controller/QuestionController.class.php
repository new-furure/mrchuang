<?php
/*
+------------------------------------------------
+				问题模块
+ 初稿 ：NewFuture
+ 完善 ：邓茜
+可能需要其他函数，自行添加，
+写清楚注释 @▽@
+关注 赞踩 这些建议写在公用函数库
+-------------------------------------------------
*/
namespace Home\Controller;
use Think\Controller;

class QuestionController extends BaseController {
	
//问题浏览页
//@作者 邓茜

	public function index()
	{
	
		$question = M('question');
		//分页，每页显示10个项目
		$condition['article_effective'] = 1;
		$condition['article_draft'] = 0;
		$conditon['article_type'] = C('QUESTION_TYPE');

		$count = $question
		->join('article ON question.article_id = article.article_id')
		->where($condition)
		->count();

		import('ORG.Util.Page');
		$Page = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page
		->show();
		$this->assign('page',$show);// 赋值分页输出
		$question_list = $question
		->join('article ON question.article_id = article.article_id')
		->join('user ON user.user_id = article.user_id')
		->where($condition)
		->field('article.article_id,article.user_id,article_title,article_profile,article_up_number,
			article_down_number,article_focus_number,article_collect_number,
			article_comment_number,article_time,user_nickname,user_avatar_url')
		->order('article_time DESC')
		->limit($Page->firstRow.','.$Page->listRows)
		->select();//获取项目列表，按时间顺序倒排

		$tag = M('article_have_tag');
		$tag_list = $tag
		->join('tag ON tag.tag_id = article_have_tag.tag_id')
		->select();
		$user_id=get_id(false);
		/*我关注的问题，预留出来的
		$focus_on_article = M('focus_on_article');
		$focus = $focus_on_article->where(array(
			'user_id' => $this->user_id
		))->select();
		$total_count = $focus_on_article->where(array(
			'user_id' => $this->user_id
		))->count(article_id);
		}	*/
		//$this->assign('page', $page->show());
		$this->assign('totalcount', $total_count);
		//$this->assign('my_focus', $my_focus);
		$this->assign('question', $question_list);
		$this->assign('tag',$tag_list);
		$this->display();
	}

//搜索
//建议写在通用库中

//单个问题的查看页
//@作者 
	public function detail($aid)
	{
		$obj = M('article');
		$comment = M('comment');
		$article_id=$aid;

		$article_item = $obj
		->join('question ON question.article_id = article.article_id')
		->join('user ON user.user_id = article.user_id')
		->where("article.article_id = $article_id and article_effective=1 and article_draft=0")
		->find();
		$tag=M('tag')
		->join('article_have_tag A ON A.tag_id = tag.tag_id')
		->where("article_id = $article_id")
		->select();
		$this->assign('tag_list',$tag);
		$this->withdraw_comment($aid,$article_item['article_type']);
		$this->assign('article_id',$article_id);
		//dump($tag);
		if($article_item) {
			$this->data = $article_item;
			$user_id = get_id(false);
			$this->assign('curr_user_id',$user_id);
		//点击次数更新
			$obj->where("article_id = $article_id")->setInc('article_hits');
		}else{
			$this->error('您查看的文章不存在');
			return;
		}
		$this->display();
	}
// 问题发布
//@作者 
	public function publish()
	{
		$user_id = get_id();
		$tag=M('tag');
		$this->tag=$tag
		//->order('tag_hits DESC')
		->select();//标签按照热度倒排
		$this->display();
	}

// 问题修改。
//@作者 
	public function edit($aid)
	{
		$article = M('article');
		$article_id = $aid;
		$data = $article
		->join('user ON user.user_id=article.user_id')
		->where("article_id=$article_id")//查找到该项
		->find();
		$tag=M('tag')
		->join('article_have_tag A ON A.tag_id = tag.tag_id')
		->where("article_id = $article_id")
		->select();
		//dump($tag);
		$this->assign('tag_list',$tag);
		$this->data = $data;
		$this->display();
	}	
	//回复文章。
	public function reply_to_article(){
		$this->base_reply_to_article();
	}
	//回复评论。
	public function reply_to_comment(){
		$this->base_reply_to_comment();
	}	
}