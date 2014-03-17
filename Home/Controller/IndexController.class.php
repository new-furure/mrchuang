<?php
/*
+------------------------------------------------
+				默认模块
+ 初稿 NewFuture
+ 完善 
+-------------------------------------------------
*/
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	
	//默认页面(游客首页)
	//此页需要验证是否已经登陆
	//或存在cookie
    //@作者：
    public function index(){
        $this->display();
    }
    /**
     *修改者：夏闪闪
     *添加逻辑 
     */
    public function indexAll(){
        $user_id=get_id();
        $Article=M('article');
        $fieldSql='article.*,user.user_nickname as user_name,user.user_avatar_url';
        $joinSql=array();//user join sql
        $joinSql[0]="left join __USER__ as user on user.user_id=article.user_id";
        $article_type=I('get.article_type');//不同模块
        
        if($article_type){
            $typeSql=' and article.article_type='.$article_type;
            //政策原链接地址
            if($article_type==C("POLICY_TYPE")){
                $fieldSql.=',policy.policy_url';
                $joinSql[1]="left join __POLICY__ as policy on article.article_id=policy.article_id";
            }          
        }
        //条件先忽略，后期去掉注释
        //$condition='article_effective=1 and user.user_id in (select user_id_focused from focus_on_user where user_id='.$user_id.')'.$typeSql;
        $count=$Article->where($condition)->count();
        import('ORG.Util.Page');
        $Page=new \Think\Page($count,6);
        $show=$Page->show();
        
        //ajax请求
        if(IS_AJAX){
            $maxArticleId=I('post.maxArticleId');//新页最大article id
            $articleList=$Article
            ->join($joinSql)
            ->where('article_id<='.$maxArticleId)
            ->field($fieldSql)
            ->order('article_id desc')
            ->limit($Page->firstRow+$Page->listRows/2,$Page->listRows/2)//加载更多
            ->select();
            if($articleList){
                $data['status']=1;
                $data['articleList']=$articleList;
                $this->ajaxReturn($data,'json');
            }
        }else{
            $articleList=$Article
            ->join($joinSql)
            ->where($condition)
            ->field($fieldSql)
            ->order('article_id desc')
            ->limit($Page->firstRow, $Page->listRows/2)
            ->select();
            //下次操作（加载更多）的最大articleId
            $maxArticleId=$articleList[0]['article_id'];

            $this->assign('maxArticleId',$maxArticleId);
            $this->assign('list', $articleList);
            $this->assign('page', $show);
        }
        $this->display();
    }

    //几个搜索方面的方法
    //@author:牛亮
    //调用的函数来自于search.php
    public function searchUser(){
        //dump($_POST);
        header("Content-Type: text/html; charset=UTF-8");
        $type  = array_sum(I('post.TYPE', array(0) )) % 4;   // 取模以使参数安全
        $cat   = array_sum(I('post.CATE', array(0) )) % 128; // 取模以使参数安全
        $field = array_sum(I('post.FIELD',array(0) )) % 8;   // 取模以使参数安全
        if(I('exact',1)) $exact = true;
        else $exact = false;
        $this->user_list = search_user(I('post.keyword',''), $type, $cat, $field, $exact);
        if($this->user_list && !($this->user_list<0)){
            // dump($this->user_list);
            $this->display();}
        else{
            //$this->display();}
            $this->error('没有返回结果,请检查您的勾选选项或关键字.'); }
    }

    public function searchCircle(){
        header("Content-Type: text/html; charset=UTF-8");
        $this->result = search_circle(I('keyword', ''));
        //dump($this->result);
        if($this->result && !($this->result<0)){
            $this->display();}
        else{
            $this->error('没有返回结果,请检查您的勾选选项或关键字.'); }
    }

    public function searchTag(){
        header("Content-Type: text/html; charset=UTF-8");
        if(I('exact',true)){
            $exact = true;}
        else{
            $exact = false;}
        $this->tag_list = search_tag(I('keyword',''), $exact);
        // dump($this->tag_list);
        if($this->tag_list && $this->tag_list!=-1){
            $this->display(); }
        else{
            $this->error('没有返回结果,请检查您的勾选选项或关键字.'); }
    }

    public function searchArticle(){
        header("Content-Type: text/html; charset=UTF-8");
        $type  = array_sum(I('post.TYPE' , array(0) )) % 8; // 取模以使参数安全
        $field = array_sum(I('post.FIELD', array(0) )) % 8; // 取模以使参数安全
        $order = array_sum(I('post.ORDER', array(0) )) % 4; // 取模以使参数安全
        if(I('exact',true)) $exact = true;
        else $exact = false;
        $this->article_list = search_article(I('post.keyword',''), $type, $field, $order, $exact);
        // dump($this->article_list);
        if($this->article_list && !($this->article_list<0)){
            $this->display();}
        else{
            $this->error('没有返回结果,请检查您的勾选选项或关键字.'); }
    }
    //风投页
    //@作者：
    public function vc()
    {

    }

    //介绍页
    //@作者：
    public function  about()
    {

    }

    //网站地图
    //所有页面连接
    //@作者：
    public function sitemap()
    {

    }

}