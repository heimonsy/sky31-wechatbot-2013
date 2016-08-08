<?php
session_start();
require_once("inc.php");
require_once("../include/common.php");

if( !isset( $_GET['sid'] ) || !is_numeric($_GET['sid']) )
	exit("<b>错误</>");

$sid = $_GET['sid'];
$msgList = getByLast( $sid, 1, 0);

$lastid = @$msgList[0]['rsid'];
require_once("weixin/template/index.html");
?>