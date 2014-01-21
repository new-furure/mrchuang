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
//登录验证
//@作者：邓茜
 private function tologin() {
        if ($session['user_id'] == '') {
            exit($_POST['c'] . '<script>$("#alert_mid").html("请先登录");$("#top_tip").animate({top:0},400).delay(1000).animate({top:-36},400,function(){location.href="' . U('User/log') . '"});</script>');
        }
    }

//项目浏览页
//@作者 
public function index()
{
 	$project = M('project');
   // $project_list = $project->page($this->get['p'] . ',' . $this->setting['project_per_page'])->order('project_time DESC')->select();
    $count = $project->count();
   // $Page = new Page($count, $this->setting['project_per_page']);
	$user_id = $session[user_id];
    if ($user_id != '') {
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
	//$this->assign('page', $Page->show());
    $this->assign('totalcount', $total_count);
    $this->assign('my_focus', $my_focus);
   // $this->assign('project_list', $project_list);
    $this->assign('title', '所有项目-' . $this->setting['site_name']);
    $this->display();
}

//搜索
//建议写在通用库中

//单个项目查看页
//@作者 
public function detail()
{
	$obj = D('Project');
	$project_id=$_GET[project_id];
	$where['project_id']=$project_id;
	$project = $obj->where($where)->find();
	$project['project_name'] = $this->
	$where['arcid'] = $arcid;
		$article = $obj->relation(true)->where($where)->find();
		$article['arcurl'] =  U('Article/index',array('arcid'=>$arcid));
		$article['commentnum'] = $this->getCommentNum($article['arcid']);
		$article['colurl'] = U('Index/columns',array('colid'=>$article['colid']));
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