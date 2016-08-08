<?php
session_start();
require_once("inc.php");
require_once("../include/common.php");
if( !isLogin() ) header("Location:login.php");

if( !isset( $_GET['sid'] ) || !is_numeric($_GET['sid']) )
	exit("<b>错误</>");

$lastid = @$_SESSION['lastid'];
if( $lastid=NULL ) $lastid=$_SESSION['lastid']=1;

$sid = $_GET['sid'];
$msgList = getByLast( $sid, 1, $lastid);
$word = getWord($sid);
$word = $word['word'];


$lastid=$_SESSION['lastid'] = @$msgList[0]['rsid'];
require_once("weixin/template/index.html");
?>