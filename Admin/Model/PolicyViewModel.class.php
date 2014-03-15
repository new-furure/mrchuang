<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class PolicyViewModel extends ViewModel {   
	public $viewFields = array(     
		'policy'=>array('article_id','user_id'),    // '_type'=>'LEFT'，因为有效位是在user表中的，所以不能用left join。
		'article'=>array('article_title', 'article_time','article_profile','article_effective','article_type','article_up_number','article_down_number',
		'_on'=>'policy.article_id=article.article_id'), 
		'user'=>array('user_nickname', 
		'_on'=>'policy.user_id=user.user_id'),
	); 
}
?>