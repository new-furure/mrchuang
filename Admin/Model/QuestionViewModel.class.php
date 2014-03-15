<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class QuestionViewModel extends ViewModel {   
	public $viewFields = array(     
		'question'=>array('article_id'),
		'article'=>array('article_title','article_type','user_id','article_profile','article_time','article_comment_number','article_hits','article_up_number','article_down_number','_on'=>'question.article_id=article.article_id'),   
	); 
}
?>