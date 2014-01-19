www
===
任何疑问均可联系future

关于数据库
  数据库配置，请在本地自行设置两份(一个是localhost测试用 ，一个是远程测试数据库)

测试服务器

	各个模块的对立测试服务器

	前端样式——style 分支—— 测试服务器http://mrchuang-style.azurewebsites.net/
	后台管理——admin 分支——测试服务器http://mschuang-adim.azurewebsites.net/
	发帖模块——article分支——测试服务器http://mrchuang-article.azurewebsites.net/
	用户默认—— user 分支——测试服务器 http://mrchuang-user.azurewebsites.net/ 
	消息模块——message分支---测试服务器http://mrchuang-message.azurewebsites.net/

	整体集成测试-主服务器

		master分支——测试服务器http://mrchuang.azurewebsites.net/

   【说明一点儿】这些在线测试连接的都是同一个数据库，请确保各部分测试不会破坏整个数据库（先localhost测试）
		万一数据库出问题，即时恢复，无重要数据直接sql导入
		
关于分支与master
  在分支上连接远程数据库测试通过之后
  再【把修改的】部分push到master上， 在主服务器上集成测试
  



目录结构说明：
www根目录
|
|--Home	      : 前台目录
|     |--Conf     : 配置目录
|     |--Lib      : 前台库目录
|     |   |--Action        :前控制器目录
|     |   |--Model         :模型目录
|     |   
|     |--Tpl       : 前台模板目录
|     |   |--User          :用户目录
|     |   |--Project       :项目
|     |   |--Policy        :政策
|     |   |--Question      :问答
|     |   |--Message       :信息中心
|     |   |--Index	   :默认目录
|     |   
|     |--Common    :前台公用目录
|
|--Admin      : 后台目录
|     |--Conf     : 配置目录
|     |--Lib      : 后台库目录
|     |   |--Action        :前控制器目录
|     |   |--Model         :模型目录
|     |   
|     |--Tpl       : 后台模板目录
|     |   |--User          :用户目录
|     |   |--Project       :项目
|     |   |--Policy        :政策
|     |   |--Filter        :垃圾信息目录
|     |   |--Message       :系统信息目录
|     |   |--Statistics	   :统计目录
|     |   
|     |--Common    : 后台公用目录
|
|--Common   ：公用函数及类库文件存放目录
|     |--Functions ：公用函数文件存放目录
|     |--Lib       ：公用类库文件存放目录
|     |--Extend    ：第三方扩展存放目录
|     |--config.php: 公用配置文件
|
|--Public    : 公用前台文件目录
|     |--Css       : 公用的CSS样式存放目录
|     |--Img       ：公用图片存放目录
|     |--Js        ：公用JS存放目录
|
|--Uploads   : 文件上传存放目录
|     |--Img       :	图片目录
|     |--Doc	   :    文档目录
|     |--Others    :    其他目录	
|
|--Runtime    : 临时文件存放目录
|     |--Admin     ：前台缓存
|     |--Home	   : 后台缓存
|
|--ThinkPHP : ThinkPHP核心框架包
|
|--index.php  ：前台入口文件（进入Home文件夹)
|
|--manage.php : 后台入口文件（不是admin.php,进入Admin文件夹）
|
|--.htaccess  : URL重写规则文件
