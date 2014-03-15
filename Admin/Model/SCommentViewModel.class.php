<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class SCommentViewModel extends ViewModel {   
	public $viewFields = array(     
		'second_comment'=>array('second_comment_id','user_id','second_comment_time','second_comment_content'), 
		'article'=>array('article_type','_on'=>'second_comment.article_id=article.article_id'),
		'comment'=>array('content'=>'substr(comment_content,1,10)','_on'=>'second_comment.comment_id=comment.comment_id'),
	); 
}
?>