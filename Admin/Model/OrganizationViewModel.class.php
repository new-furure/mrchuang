<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class OrganizationViewModel extends ViewModel {   
	public $viewFields = array(     
		'organization'=>array('user_id','category_id','organization_certification_infomation','organization_certified'),     
		'user'=>array('user_nickname', 'user_email','user_effective','user_type',
		'_on'=>'organization.user_id=user.user_id'),   
	); 
}
?>