<?php
namespace Admin\Model;
use Think\Model\ViewModel;
//举报评论
class RCommentViewModel extends ViewModel {   
	public $viewFields = array(     
		'report_comment'=>array('user_id','comment_id','report_comment_type','report_comment_content'),    // '_type'=>'LEFT'，因为有效位是在user表中的，所以不能用left join。
		'comment'=>array('comment_id','comment_content','comment_type',
		'_on'=>'report_comment.comment_id=comment.comment_id'),   
	); 
}
?>