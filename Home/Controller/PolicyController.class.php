<?php
/********************************************
+政策模块
+和项目模块基本相同
+
**********************************************/
namespace Home\Controller;
use Think\Controller;

class PolicyController extends BaseController {
	//政策浏览页。
	public function index()
	{
		$policy = M('policy');
		$tag  = M('article_have_tag');
		$condition['article_draft'] = 0;
		$condition['article_effective'] = 1;
		$condition['article_type'] = C('POLICY_TYPE');
		//分页，每页显示10个项
		import('ORG.Util.Page');
		$count = $policy
		->join('article ON article.article_id = policy.article_id')
		->where($condition)
		->count();

		$Page = new \Think\Page($count,3);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page
		->show();
		$this->assign('page',$show);// 赋值分页输出

		//$p = new Page($count,10);//分页函数，参数1、2分别为总数和每页显示的数目
		$policy_list = $policy	//获取项目列表，按时间顺序倒排	
		->join('article ON article.article_id = policy.article_id')
		->join('user ON user.user_id = article.user_id')
		->where($condition)
		->order('article_time DESC')
		->field('policy.article_id,policy.user_id,article_title,article_profile,article_up_number,
			article_down_number,article_focus_number,article_collect_number,
			article_comment_number,article_time,user_nickname,user_avatar_url')
		->limit($Page->firstRow.','.$Page->listRows)
		->select();
		if($policy_list){
			$this->assign('policy_list', $policy_list);
		}
		$verify = false;
		$user_id = get_id();
		if($user_id){
			$user_type=ac_by_id($user_id);
			if($user_type == C('GOVERNMENT')){
				$verify = true;
			}
		}
		$this->verify = $verify;
		$tag_list = $tag
		->join('tag ON article_have_tag.tag_id = tag.tag_id')
		->select();
		/*$user_id = $_SESSION['user_id'];//我关注的政策
		if ($user_id != '')
		{
			$focus_on_policy = M('focus_on_policy');
			$focus = $focus_on_policy->where(array(
				'user_id' => $user_id
			))->select();
			$total_count = $focus_on_policy->where(array(
				'user_id' => $user_id
			))->count(); //获取关注数。
			$my_focus = array(); //关注的数组。
			foreach ($focus as $k => $v) {
				if ($v['user_id'] == $user_id) {
					$my_focus[] = $v['policy_id'];
				}
			}
		}	*/
		//$this->assign('my_focus', $my_focus);
		$this->assign('count', $total_count);
		$this->assign('tag',$tag_list);
		$this->display();
	}

//单个政策查看页
//@作者 
	public function detail($aid)
	{
		$article = M('article');
		$comment = M('comment');
		$article_id = $aid;
		$policy = $article
		->join('user ON user.user_id=article.user_id')
		->join('policy ON article.article_id = policy.article_id')
		->where("article.article_id = $article_id and article_draft=0 
		and article_effective=1")//查找到该项
		->find();
		//dump($policy);
		$tag=M('tag')
		->join('article_have_tag A ON A.tag_id = tag.tag_id')
		->where("article_id = $article_id")
		->select();
		$this->tag=$tag;
		$focus_list = M('focus_on_article')
		->join('user ON user.user_id=focus_on_article.user_id')
		->where("article_id = $article_id and user_type")
		->select();
		$num=0;
		foreach ($focus_list as $key => $value) {
			if(ac_by_id($value['user_id'] )== C("VC"))
			{
				$focus_sec_list[$num] = $value;
				$num = $num+1;
			}
				
		} 
		$this->focus_sec_list = $focus_sec_list;
		$this->focus_list = $focus_list;
		if($policy) {
			$this->assign('data',$policy);
			$user_id = get_id(false);
			//$user_id = 2;
			$this->assign('curr_user_id',$user_id);
			$article->where("article_id = $article_id")->setInc('article_hits');//点击次数更新
		}else{
			$this->error('您查看的文章不存在');
			return;
		}
		echo $aid;
		echo $policy['article_type'];	
		$this->withdraw_comment($aid,$policy['article_type']);
		$this->display();
	}

// 政策填写
//@作者 
	public function publish()
	{
		$user_id = get_id();
		if($user_id){
			$user_type=ac_by_id($user_id);
			if($user_type != C('GOVERNMENT')){
				$this->error('您没有发布政策的权限');
				return;
			}
		}
		$tag=M('tag');
		$this->tag=$tag->order('tag_hits DESC')->select();//标签按照热度倒排
		$this->display();
	}

// 政策修改
//@作者 
	public function edit($aid)
	{
		$article = M('article');
		$article_id = $aid;
		$condition['article_id'] = $article_id;
		$data = $article
		->join('user ON user.user_id=article.user_id')
		->where($condition)//查找到该项
		->find();
		$tag=M('tag')
		->join('article_have_tag A ON A.tag_id = tag.tag_id')
		->where("article_id = $article_id")
		->select();
		$this->tag_list=$tag;
		$this->data = $data;
		$this->display();
	}
//回复政策。
	public function reply_to_article(){
		$this->base_reply_to_article();
	}
//回复评论
	public function reply_to_comment(){
		$this->base_reply_to_comment();
	}	
}