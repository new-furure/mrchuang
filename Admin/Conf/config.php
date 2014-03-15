<?php
return array(
	//'配置项'=>'配置值'
	'APP_STATUS'=> 'debug',
	'URL_MODEL' => URL_COMPAT,
	'TMPL_PARSE_STRING'=>array(	//改变public和plugin
        '__PUBLIC__'=>__ROOT__.'/Admin/Public',
		'GROUP_NAME'=>'Admin',
    ),
	// 添加数据库配置信息
	// 'DB_TYPE'   => 'mysql', // 数据库类型
	// 'DB_HOST'   => '127.0.0.1', // 服务器地址
	// 'DB_NAME'   => 'testtest', // 数据库名
	// 'DB_USER'   => 'root', // 用户名
	// 'DB_PWD'    => '314159', // 密码
	// 'DB_PORT'   => 3306, // 端口
	// 'DB_PREFIX' => '', // 数据库表前缀
	// 'SHOW_PAGE_TRACE' =>true, // 显示页面Trace信息

	//个人配置
	'CFG_PATH' =>'./Admin/Conf/',
);
?>