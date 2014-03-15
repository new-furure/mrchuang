<?php
/*
+---------------------------------------------------
+后台模块
+清除缓存管理
+功能：
+1、清除缓存
+
+2014、2、16最后更改
+---------------------------------------------------
*/
namespace Admin\Controller;
use Think\Controller;
class CacheController extends CommonController {
	public function index(){
		$this->del_cache();
	}
	private function del_cache() { 
		header("Content-type: text/html; charset=utf-8");
		//清文件缓存
		$dirs = array('./Runtime/');
		@mkdir('./Runtime',0777,true);
		//清理缓存
		foreach($dirs as $value) {
			$this->rmdirr($value);
		}
		$this->success('系统缓存清除成功！',U('Index/main'));  
	} 
	public function rmdirr($dirname) {
		if (!file_exists($dirname)) {
		return false;
		}
		if (is_file($dirname) || is_link($dirname)) {
			return unlink($dirname);
		}
		$dir = dir($dirname);
		if($dir){
			while (false !== $entry = $dir->read()) {
				if ($entry == '.' || $entry == '..') {
					continue;
				}
		//递归
				$this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
			}
		}
		$dir->close();
		return rmdir($dirname);
	}
}
?>