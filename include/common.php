<?php
header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate");
date_default_timezone_set('PRC');
error_reporting(0);
define('BASE_PATH', str_replace("\\", '/', substr(__FILE__, 0, -18)));
define("PIC_PATH",  BASE_PATH."data/pic/");
require_once "config.php";

/*************************************
 *
 * 包含lib库
 *
 *************************************/
//echo __FILE__;
//require_once BASE_PATH."lib/functions.php";
require_once BASE_PATH."lib/MyDB.php";
require_once BASE_PATH."lib/Mysql.php";
require_once BASE_PATH."lib/Notices.php";
require_once BASE_PATH."lib/WxMsg.php";
require_once BASE_PATH."lib/WxMsgFactory.php";
require_once BASE_PATH."lib/WxService.php";
require_once BASE_PATH."lib/WxUser.php";
require_once BASE_PATH."lib/Oauth.php";
require_once BASE_PATH."lib/Encodes.php";
require_once BASE_PATH."lib/PostLogin.php";
require_once BASE_PATH."lib/ErrorListen.php";
require_once BASE_PATH."lib/Debug.php";
require_once BASE_PATH."lib/functions.php";
/*************************************
 *
 * 包含扩展库
 *
 *************************************/
require_once BASE_PATH."extends/BaseExtends.php";
require_once BASE_PATH."extends/BaseStatusExtends.php";
require_once BASE_PATH."extends/LehuoExtends.php";
require_once BASE_PATH."extends/NewsFuncs.php";
require_once BASE_PATH."extends/OtherFuncs.php";
require_once BASE_PATH."extends/RadioExtends.php";
require_once BASE_PATH."extends/SimsimiFunc.php";
require_once BASE_PATH."extends/TakeAwayFuncs.php";
require_once BASE_PATH."extends/VerifyExtends.php";
require_once BASE_PATH."extends/BindExtends.php";
require_once BASE_PATH."extends/CourseExtends.php";
require_once BASE_PATH."extends/TalkSubjectExtends.php";
require_once BASE_PATH."extends/StudentExtends.php";

/*************************************
 * 
 * 一些初始化的操作
 * 
 *************************************/

MyDB::setWxdb($wxdb_config);
