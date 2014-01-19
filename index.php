<?php
define('APP_DEBUG', TRUE);
define('THINK_PATH', './ThinkPHP/');
define("WEB_ROOT", dirname(__FILE__) . "/");
define('SITE_PATH', getcwd());//网站当前路径


define('APP_NAME', 'Home');
define('APP_PATH', './Home/');
define("RUNTIME_PATH", SITE_PATH . "/Runtime/Home/");

require(THINK_PATH . "ThinkPHP.php");

?>