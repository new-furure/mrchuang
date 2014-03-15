<?php
/*
+---------------------------------------------------
+后台模块
+配置模块
+功能：
+1、显示配置列表
+2、编辑配置
+3、添加配置
+4、删除配置项
+5、写入配置文件，后期也许会需要，先写了。
+
+最后修改2014、2、17
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class ConfigController extends CommonController {
	//显示配置数据列表
	public function index()
	{
		$Config=M('config');
		$count=$Config->count();
		$Page= new \Org\Util\PageW($count,10);
		$show= $Page->show();
		$list= $Config->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	//编辑配置
	public function editConfig()
	{
		$id=I('get.id');
		$record=M('config')->find($id);
		if($record==NULL)
		{
			$this->error('不存在该项');
		}
		else
		{
			$this->assign('list',$record);
			$this->display();
		}
	}
	//处理编辑请求。
	public function runEditConfig()
	{
		if(!IS_POST) 
		{
			$this->error('页面不存在');
		}
		$Config=M('config');
		$data['config_id']=I('post.id');
		$data['config_name']=I('post.name');
		$data['config_info']=I('post.info');
		$data['config_content']=I('post.content');
		$data['config_time']=date('Y-m-d H:i:s',time());
		if($Config->save($data)!==false)
		{
			$this->success("修改成功",U('Config/index'));
		}
		else
		{
			$this->error("修改失败");
		}
	}
	//添加配置。
	public function addConfig()
	{
		$this->display();
	}
	//处理添加请求
	public function runAddConfig()
	{
		if(!IS_POST) 
		{
			$this->error('页面不存在');
		}
		$data['config_name']=I('post.name');
		$data['config_content']=I('post.content');
		$data['config_info']=I('post.info');
		if(M('config')->add($data))
			$this->success("添加配置成功",U('Config/index'));
		else
			$this->error("添加失败",U('Config/index'));
	}
	//删除配置
	public function delConfig()
	{
		$id=I('get.id');
		if(M('config')->delete($id))
		{
			$this->success("删除成功");
		}
		else
		{
			$this->success("删除失败");
		}
	}
	//如果后期需要，该函数负责将把上述这些配置写入到某个配置文件中去。
	public function rewriteConfig()
    {
		$Config=M('Config');
		$configpath=C('cfg_path');//配置文件存放的位置
		//if(file_exists($configpath.'webconfig.php'))
		//	$this->error("ok");
		if(!is_writeable($configpath.'webconfig.php'))
		{
			$this->error("'{$configpath}'error",U('Config/index'));
		}
		$datalists=$Config->order('config_id asc')->select();
		$str="<?php \n return array(\n";
		foreach($datalists as $datalist)
		{
			$str.="\t'".$datalist['config_name']."'=>'".$datalist['config_content']."',\n";
		}
		$str.=");\n?>\n";
		file_put_contents($configpath.'webconfig.php',$str);
		$this->success("配置文件写入成功");
		//F('webconfig',$data,$configpath);//为什么我写的就是乱码呢？？//查看原来的代码可知是原来的函数代码本身包含了写入格式。
	}
}