<?php
header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-cache");
date_default_timezone_set('PRC');
define('BASE_PATH', str_replace("\\", '/', substr(__FILE__, 0, -18)));
define("PIC_PATH",  BASE_PATH."data/pic/");
define("USER_DATA_PATH", BASE_PATH."../user/data/");
require_once "config.php";
require_once 'router_table.php';




/*************************************
 *
 * 包含lib库
 *
 *************************************/
//echo __FILE__;
//require_once BASE_PATH."lib/functions.php";
require_once BASE_PATH."lib/Mysql.php";
require_once BASE_PATH."lib/MyDB.php";
require_once BASE_PATH."lib/ErrorLogs.php";
require_once BASE_PATH."lib/Router.php";
require_once BASE_PATH."lib/WxMsg.php";
require_once BASE_PATH."lib/WxMsgFactory.php";
require_once BASE_PATH."lib/WxService.php";
require_once BASE_PATH."lib/WxUser.php";
require_once BASE_PATH."lib/Oauth.php";
require_once BASE_PATH."lib/Encodes.php";
require_once BASE_PATH."lib/MyMcrypt.php";
require_once BASE_PATH."lib/PostLogin.php";
require_once BASE_PATH."lib/Debug.php";
require_once BASE_PATH."lib/functions.php";
require_once BASE_PATH."lib/BaseExtends.php";
require_once BASE_PATH."lib/MyUrlFetch.php";
require_once BASE_PATH."lib/OtherException.php";
require_once BASE_PATH."lib/CURDException.php";
/*************************************
 *
 * 包含扩展库
 *
 *************************************/
require_once BASE_PATH."funcs/Course.php";
require_once BASE_PATH."funcs/UserInfoTool.php";

/*************************************
 * 
 * 一些初始化的操作
 * 
 *************************************/
function __autoload($className) {
	include_once BASE_PATH."extends/".$className.".php";
}

set_error_handler("my_error_handler", E_ALL | E_ERROR);
//register_shutdown_function("shutdown_error");
set_exception_handler("uncatched_exception_record");

