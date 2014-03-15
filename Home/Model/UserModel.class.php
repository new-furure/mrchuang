<?php

namespace Home\Model;
use Think\Model;

/**
*用户User表Model
* @author NewFuture
***************************/
class  UserModel extends Model
{
	//自动验证 array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
	protected $_validate = array(
		//email 格式验证
		array("user_email","email","Email格式不对！",self::EXISTS_VALIDATE/*0存在时验证*/,"",self::MODEL_BOTH/*3都验证*/,),
		//注册重复密码
		array("re_passwd","user_passwd","两次密码不一致！",0/*self::EXISTS_VALIDATE*/,"confirm"),
		//注册邮箱唯一性验证
		array("user_email","","邮箱已经注册!",0/*存在字段时验证*/,"unique",1/*self::MODEL_INSTERT*/,),
		//
		);

	//自动填充
	protected $_auto = array(
    );
}