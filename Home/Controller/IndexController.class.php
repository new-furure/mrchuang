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