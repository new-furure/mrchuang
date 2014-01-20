<?php
/*
+------------------------------------------------
+				默认模块
+ 初稿 NewFuture
+ 完善 
+-------------------------------------------------
*/
class IndexAction extends Action {
	
	//默认页面(游客首页)
	//此页需要验证是否已经登陆
	//或存在cookie
    //@作者：
    public function index(){
        $this->display();
    }


    //搜索页
    //@作者：
    public function  search()
    {

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