<?php
session_start();
require_once("inc.php");
require_once("../core/include/common.php");

if( !isset( $_GET['sid'] ) || !is_numeric($_GET['sid']) )
	exit("<b>错误</>");


$sid = $_GET['sid'];


$max = getPageList(1, $sid, 1 ,40);
$max  = @$max[30]['rsid'];

$lastid = $_SESSION['lastid']= $max;
if( $lastid==NULL ) $lastid=$_SESSION['lastid']=1;

$msgList = getByLast( $sid, 1, $lastid);
$word = getWord($sid);
$word = $word['word'];


$lastid=$_SESSION['lastid'] = @$msgList[0]['rsid'];
require_once("weixin/template/index.html");
?>