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
class ProjectAction extends Action {
	
	public $user_id; // 公用变量

//项目浏览页
//@作者 邓茜

	public function index()
	{
		$project = M('project');
		//分页，每页显示10个项目
		$project_list = $project->page('1,10')->order('project_time DESC')->select();
		$count = $project->count();
		//$page = new Page($count,10);
		$user_id = $session[user_id];
		if ($user_id != '')
		{
			$focus_on_project = M('focus_on_project');
			$focus = $focus_on_project->where(array(
				'user_id' => $this->user_id
			))->select();
			$total_count = $focus_on_project->where(array(
				'user_id' => $this->user_id
			))->count(project_id);
			$my_focus = array();
			foreach ($focus as $k => $v) {
				if ($v['user_id'] == $this->user_id) {
					$my_focus[] = $v['project_id'];
				}
			}
		}	
		//$this->assign('page', $page->show());
		$this->assign('totalcount', $total_count);
		$this->assign('my_focus', $my_focus);
		$this->assign('project_list', $project_list);
		$this->assign('title', '所有项目-' . $this->setting['site_name']);
		$this->display();
	}

//搜索
//建议写在通用库中

//单个项目查看页
//@作者 
	public function detail()
	{
		$obj = M('project');
		$project_id=$_GET[project_id];
		$project = $obj->find($project_id);
		if($project) {
			$this->data = $project;
		}else{
			$this->error('数据错误');
		}
		$this->display();
	}

//回复
//@作者 
//建议写在公用库函数
	public function reply()
	{
		if(session('user_id')==''){
				$this->error('您尚未登录',U('User/login'));
				return;
			}	
	}

// 项目填写
//@作者 
	public function edit()
	{
		$tag=M('tag');
		$this->tag=$tag->find();
		$this->display();
	}

//保存
//@作者 
	public function save(/*参数自定*/)
	{
	
	}

//项目提交
//数据存档和数据提交数据库
//@作者 
	public function submit()
	{
			//$data['project_']=I('post.project_');//预留的，草稿判断位；
			//$data['project_']=I('post.project_');//预留的，图片链接位;
			//$data['project_']=I('post.project_');//预留的，文档链接
			//$data['project_title'] = $_POST['project_title'];
			//$data['project_profile'] = $_POST['project_profile'];
			$data['project_title'] = I('post.project_title');
			$data['project_profile'] = I('post.project_profile');
			$data['project_time']=date('Y-m-d H:i:s');
			$project = M('project');
			$result = $project->add($data);
			if(!$result) {
			$this->waitSecond=5;
			$this->error('写入错误！');
			return;
			}else{
				$this->success('添加成功','index');
			}
	}


//加关注
//@作者:邓茜
	function focus() {
   		$this->tologin();
        $data['uid'] = $this->uid;
        $data['topicid'] = $this->post['topicid'];
        $topic = M('topicfocus');
        if ($topic->where($data)->count() == 0) {
            $topic->add($data);
            echo '取消关注';
        } else {
            $topic->where($data)->delete();
            echo '+ 关注';
        }
	}
///项目资料卡 建议写在公共模板库
///此部分需要和其他人沟通好 
}