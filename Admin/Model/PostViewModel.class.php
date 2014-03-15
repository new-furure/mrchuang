<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class PostViewModel extends ViewModel {   
	public $viewFields = array(     
		'post'=>array('article_id','circle_id','post_type'),    // '_type'=>'LEFT'，因为有效位是在user表中的，所以不能用left join。
		'article'=>array('article_title', 'article_time','article_effective','article_type','user_id',
		'_on'=>'post.article_id=article.article_id'), 
		'circle'=>array('circle_name',
		'_on'=>'post.circle_id=circle.circle_id'), 
	); 
}
?>