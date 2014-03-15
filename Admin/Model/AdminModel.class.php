<?php
namespace Admin\Model;//额，实验了好久都不成功，原来是这个地方写成了home\model
use Think\Model;
class AdminModel extends Model{
	protected $_validate = array(
		array('admin_name','require','用户名必须填写',0,'',3),
		array('admin_name','','帐号名称已经存在！',0,'unique',3), //验证是否唯一
		array('admin_passwd','require','密码必须填写',0,'',3),					
		//array('admin_passwd','/^.{5,}$/','密码必须5位数以上',0,'regex',3),//已md5处理，所以放在外面判断。 
	);
	
	/*//个人感觉不太需要啊。。
	protected $_auto = array(   
		array('admin_passwd','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理
	);*/
}