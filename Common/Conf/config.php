<?php
return array(
	// sae上传文件
	'FILE_UPLOAD_TYPE' => 'Sae',
	// 添加数据库配置信息
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => '', // 数据库表前缀

	// 远程
	// 'DB_HOST'   => 'db4free.net', // 服务器地址
	// 'DB_NAME'   => 'newfuturedb', // 数据库名
	// 'DB_USER'   => 'newfuture', // 用户名
	// 'DB_PWD'    => 'c8714e', // 密码

	//本地
	'DB_HOST'   => 'localhost', // 服务器地址
	'DB_NAME'   => 'mrchuang', // 数据库名
	'DB_USER'   => 'root', // 用户名
	'DB_PWD'    => '', // 密码



	//网站配置
	'URL_CASE_INSENSITIVE' =>true, //不区分大小写
	'SHOW_PAGE_TRACE' =>true, // 显示页面Trace信息
<<<<<<< HEAD
	'MODULE_ALLOW_LIST' => array( 'Home', 'Admin' ), //默认模块Home
	'URL_MODEL' =>2, //URL重写
=======
	'MODULE_ALLOW_LIST' => array('Home','Admin'),//默认模块Home
 	'URL_MODEL' =>2,	//URL重写
>>>>>>> 58744d35e33606f88e9d3d20596a1b855bb45e2d


	//验证有效期
	'VALIDATE_EFFECTIVE_TIME'=>10, //天

	/**
	 *   -------数据库id范围------
	 * //  ALTER TABLE `dynamic`       AUTO_INCREMENT =  1073741825  , -- [ 1073741825 , 2147483648 ]
	 * // ALTER TABLE `notice`         AUTO_INCREMENT =  805306369   , -- [ 805306369  , 1073741824 ]
	 * // ALTER TABLE `message`        AUTO_INCREMENT =  671088641   , -- [ 671088641  , 805306368  ]
	 * // ALTER TABLE `comment`        AUTO_INCREMENT =  536870913   , -- [ 536870913  , 671088640  ]
	 * // ALTER TABLE `second_comment` AUTO_INCREMENT =  268435457   , -- [ 268435457  , 536870912  ]
	 * // ALTER TABLE `article`        AUTO_INCREMENT =  201326593   , -- [ 201326593  , 268435456  ]
	 * // ALTER TABLE `user`           AUTO_INCREMENT =  134217729   , -- [ 134217729  , 201326592  ]
	 * // ALTER TABLE `circle`         AUTO_INCREMENT =  67108865    , -- [ 67108865   , 134217728  ]
	 * // ALTER TABLE `tag`            AUTO_INCREMENT =  66060289    , -- [ 66060289   , 67108864   ]
	 * // ALTER TABLE `config`         AUTO_INCREMENT =  66043905    , -- [ 66043905   , 66060288   ]
	 * // ALTER TABLE `admin`          AUTO_INCREMENT =  66027521    , -- [ 66027521   , 66043904   ]
	 * // ALTER TABLE `category`       AUTO_INCREMENT =  1           , -- [        1   , 6          ]
	 * //ID 范围配置
	 */
	//类别ID
	"MIN_CATEGORY_ID"=>0,
	"MAX_CATEGORY_ID"=>8,
	//管理员
	"MIN_ADMIN_ID"=>66027520,
	"MAX_ADMIN_ID"=>66043904,
	//配置
	"MIN_CONFIG_ID"=>66043904,
	"MAX_CONFIG_ID"=>66060288,
	//标签
	"MIN_TAG_ID"=>66060288,
	"MAX_TAG_ID"=>67108864,
	//圈子
	"MIN_CIRCLE_ID"=>67108864,
	"MAX_CIRCLE_ID"=>134217728,
	//用户
	"MIN_USER_ID"=>134217728,
	"MAX_USER_ID"=>201326592,
	//文章
	"MIN_ARTICLE_ID"=>201326592,
	"MAX_ARTICLE_ID"=>268435456,
	//二级评论
	"MIN_SECOMMENT_ID"=>268436456,
	"MAX_SECOMMENT_ID"=>536870912,
	//评论
	"MIN_COMMENT_ID"=>536870912,
	"MAX_COMMENT_ID"=>671088640,
	//消息
	"MIN_MESSAGE_ID"=>671088640,
	"MAX_MESSAGE_ID"=>805306368,
	//通知
	"MIN_NOTICE_ID"=>805306368,
	"MAX_NOTCIE_ID"=>1073741824,
	//动态
	"MIN_DYNAMIC_ID"=>1073741825,


	//****************************/
	// 用户状态标志位
	//user_type
	/***************************/
	//前两位 ***--
	"ACTIVE_BIT"=>1,   //*****-，第一位，标记激活位，
	"ORG_BIT"   =>2,   //****-*，第二位，标记是否为组织
	// 后3位7互斥标记 ---**
	"NO_ORG"         => 0, //无组织个人
	"STARTUP"        => 1, //初创企业
	"ENTERPRISE"     => 2, //认证企业
	"VC"             => 3, //风险投资
	"INCUBATOR"      => 4, //孵化器
	"INVITEE"        => 5, //邀请企业
	"GOVERNMENT"     => 6, //政府部门

	//个人用户在组织中的关系user_status
	"PENDING_USER" =>1, //待验证
	"INVITE_USER"  =>2, //邀请加入
	"NORMAL_USER"  =>3, //普通成员
	"ADMIN_USER"   =>4, //管理员成员

	//认证状态
	"UNCERTIFY"=>0, //未认证
	"CERTYFING"=>1, //认证中，申请认证
	"CERTYFIED"=>2, //已认证


	//文章类型
<<<<<<< HEAD
	"PROJECT_TYPE" =>1, //项目
	"POLICY_TYPE"  =>2, //政策
	"QUESTION_TYPE"=>3, //问题
	"IDEA_TYPE"    =>4, //创意
	"TALK_TYPE"    =>5, //时光机状态说说随便理解
	"POST_TYPE"    =>6, //圈子帖子
	"VC_TYPE"      =>7, //风投库
	"INCUBATOR_TYPE"=>8, //孵化器库


=======
	"PROJECT_TYPE" =>0, //项目
	"POLICY_TYPE"  =>1, //政策
	"QUESTION_TYPE"=>2, //问题
	"POST_TYPE"    =>5, //圈子帖子
>>>>>>> 58744d35e33606f88e9d3d20596a1b855bb45e2d
	//圈子帖子类型
	"OUT_POST"    =>0, //跨圈
	"IN_POST"     =>1, //圈内
	"CHAT_POST"   =>2, //群聊贴
	"NORMAL_POST" =>3, //普通帖子
	//评论类型
	//"SECOND_COMMENT"  =>0单独表
<<<<<<< HEAD
	"PROJECT_IMPROVE"   =>1, //项目改善
	"PROJECT_COMMENT"   =>2, //项目评论
	"POLICY_COMMENT"    =>3, //政策回复
	"QUESTION_COMMENT"  =>4, //问题回复
	"IDEA_COMMENT"     =>5, //创意评论
	"TALK_COMMENT"      =>6, //时光机状态回复
	"VC_COMMENT"        =>7, //风投回复
	"INCUBATOR_COMMENT" =>8, //孵化器回复
	"CIRCLE_POST_COMMENT"=>15, //圈子评论
=======
	"PROJECT_IMPROVE" =>1, //项目改善
	"PROJECT_COMMENT" =>2, //项目评论
	"POLICY_COMMENT"  =>3, //政策回复
	"QUESTION_COMMENT"=>4, //问题回复
	"CIRCLE_POST_COMMENT"    =>5, //圈子评论
>>>>>>> 58744d35e33606f88e9d3d20596a1b855bb45e2d

	/**
	 * //notice_type
	 * 11、新评论 12、项目新完善 13、问题新回答
	 * 21、关注的文章（项目）、22（政策）、23（问题）、24（完善） 以及 25（人）的动态
	 * 31、申请加入圈子通知 32、其他圈子通知（解散、退出等无需操作的提醒）
	 * 41、收藏
	 * 51、系统消息*/
	"NEW_COMMENT"   =>11,
	"NEW_IMPOVE"    =>12,
	"NEW_ANSWER"    =>13,
	"FOUCUS_PROJECT"=>21,
	"FOCUS_POLICY"  =>22,
	"FOCUS_QUESTION"=>23,
	"FOCUS_IMPROVE" =>24,
	"CIRCLE_APPLY"  =>31,
	"CIRCLE_QUIT"   =>32,
	"COLLECT_NOTICE"=>41,
	"SYSTEM_NOTICE" =>51,


	//发送邮件配置
	'MAIL_ADDRESS'=>'mr_chuang_com@163.com', // 邮箱地址
	'MAIL_SMTP'=>'smtp.163.com', // 邮箱SMTP服务器
	'MAIL_LOGINNAME'=>'mr_chuang_com@163.com', // 邮箱登录帐号
	'MAIL_PASSWORD'=>'mrchuang123', // 邮箱密码
	'MAIL_CHARSET'=>'UTF-8', //编码
	'MAIL_AUTH'=>true, //邮箱认证
	'MAIL_HTML'=>true, //true HTML格式 false TXT格式
	//邮件类型
<<<<<<< HEAD
	"ACTIVE_MAIL"     =>1, //激活邮件
	"PASSWORD_MAIL"   =>2, //修改密码
	"INVITE_ORG_MAIL" =>3, //组织邀请邮件
	"CREATE_ORG_MAIL" =>4, //组织创建邮件
	"SEND_INVITE_CODE"=>5, //发送邀请码

	"REG_IS_ON"           =>true,  //是否开启注册
	"IS_CODE_NEED"        =>true,  //是否需要邀请码
	"IS_EMAIL_VALIDATE_ON"=>false, //是否需要邮箱验证

=======
	"ACTIVE_MAIL"=>1,//激活邮件
	"PASSWORD_MAIL"=>2, //修改密码
	"INVITE_ORG_MAIL"=>3, //组织邀请邮件
	"CREATE_ORG_MAIL"=>4,//组织创建邮件
	"SEND_INVITE_CODE"=>5,//发送邀请码

	"REG_IS_ON"=>true,//是否开启注册
	"IS_CODE_NEED"=>true,//是否需要邀请码
	
>>>>>>> 58744d35e33606f88e9d3d20596a1b855bb45e2d

);
