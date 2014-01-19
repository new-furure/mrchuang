<?php
define( 'APP_DEBUG', TRUE );
define( 'THINK_PATH', './ThinkPHP/' );
define( "WEB_ROOT", dirname( __FILE__ )."/" );

define( 'APP_NAME', 'Admin' );
define( 'APP_PATH', './Admin/' );
define( "RUNTIME_PATH", WEB_ROOT . "Runtime/Admin/" );

require THINK_PATH . "ThinkPHP.php";

?>
