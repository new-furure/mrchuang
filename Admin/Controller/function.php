<?php
	//暂时写一些函数，留作提醒
	function getConfig()
	{
		$config=M('config')->getField('name,content');
		C($config);//合并配置参数到全局配置
		//调用次函数后，可以直接用C函获取配置信息。
	}
?>