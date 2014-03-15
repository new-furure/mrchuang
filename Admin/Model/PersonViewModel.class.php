<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class PersonViewModel extends ViewModel {   
	public $viewFields = array(     
		'person'=>array('user_id','user_sex','user_location'),    // '_type'=>'LEFT'，因为有效位是在user表中的，所以不能用left join。
		'user'=>array('user_nickname', 'user_email','user_effective','user_type','user_time',
		'_on'=>'person.user_id=user.user_id'),   
	); 
}
?>