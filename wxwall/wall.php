<?php
session_start();
require_once("inc.php");
require_once("../core/include/common.php");

if( !isset( $_GET['sid'] ) || !is_numeric($_GET['sid']) )
	exit("<b>错误</>");


$sid = $_GET['sid'];

$_SESSION['lastid']=$lastid = 0;

$msgList = getByLast( $sid, 1, $lastid);
$word = getWord($sid);
$word = $word['word'];

//var_dump($msgList);
//echo count($msgList);
$lastid=$_SESSION['lastid'] = @$msgList[0]['rsid'];
require_once("weixin/template/index.html");
?>