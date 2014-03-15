<?php
/*
+---------------------------------------------------
+后台模块
+验证码模块
+功能：
+1、设置验证码。
+
+最后修改2014、2、17
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller {
	public function verify()
	{
		$config=array('fontSize'=>14,    // 验证码字体大小    
		'length'=>4,     // 验证码位数    
		'useNoise'=>false, // 关闭验证码杂点
		'useCurve'=>false,
		);
		$Verify =new \Think\Verify($config);
		$Verify->entry();
	}
}
?>