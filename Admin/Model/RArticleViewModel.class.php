<?php
namespace Admin\Model;
use Think\Model\ViewModel;
//举报文章
class RArticleViewModel extends ViewModel {   
	public $viewFields = array(     
		'report_article'=>array('article_id','user_id','report_article_type','report_article_content'),    // '_type'=>'LEFT'，因为有效位是在user表中的，所以不能用left join。
		'article'=>array('article_id','article_title','article_profile','article_effective','article_type',
		'_on'=>'report_article.article_id=article.article_id'),   
	); 
}
?>