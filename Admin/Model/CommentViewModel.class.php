<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class CommentViewModel extends ViewModel {   
	public $viewFields = array(     
		'comment'=>array('comment_id','user_id','comment_time','comment_content','comment_type','comment_up_number','comment_down_number'),    // '_type'=>'LEFT'，因为有效位是在user表中的，所以不能用left join。
		'article'=>array('article_title','article_type','_on'=>'comment.article_id=article.article_id'),   
	); 
}
?>