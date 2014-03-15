<?php
namespace Admin\Model;
use Think\Model\ViewModel;
//举报二级评论
class RSCommentViewModel extends ViewModel {   
	public $viewFields = array(     
		'report_second_comment'=>array('user_id','second_comment_id','report_second_comment_type','report_second_comment_content'),    // '_type'=>'LEFT'，因为有效位是在user表中的，所以不能用left join。
		'second_comment'=>array('second_comment_id','second_comment_content',
		'_on'=>'report_comment.comment_id=comment.comment_id'),   
	); 
}
?>