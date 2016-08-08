<?php
session_start();
require_once("inc.php");
require_once("../include/common.php");
if( !isLogin() ) header("Location:login.php");


if( !isset( $_GET['method'] ) || !isset($_GET['sid']) || !isset($_GET['tag']) )
	exit("ERROR1");

$method = $_GET['method'];
$sid    = $_GET['sid'];
$tag    = $_GET['tag'];



if( $method=="newnums" ){
	$lastid = $_GET['lastid'];
	$res = getNewNums( $sid, $lastid);
	echo json_encode($res);
	exit();

}else if( $method == "wall" ){
	$lastid=@$_SESSION['lastid'];
	$res = getByLast( $sid, 1, $lastid);
	if( $res==NULL )
		echo json_encode(array("lastid"=>$_SESSION['lastid'],"nums"=>0,'msgs'=>array()));
	else
		echo json_encode( array("lastid"=>$_SESSION['lastid'], 'nums'=>count( $res ), 'msgs'=>$res) );
	
}else if( $method == "cho" ){
	//echo "fffff";
	$res = getUserInfo( $sid, 1);
	srand(time());
	$cn = rand(0, count($res)-1);
	if( $res==NULL )
		echo json_encode(array("nums"=>$cn,'msgs'=>array()));
	else
		echo json_encode( array( 'nums'=>$cn, 'msgs'=>$res) );
	
}else if( $method=='prep' ) {
	$cp = $_GET['cp'];
	$str = getPageList( $cp-1 , $sid, $tag , ADMIN_PAGE_SIZE);
	exit( json_encode($str) );
	
}else if( $method=='next' ) {
	$cp = $_GET['cp'];
	$str = getPageList( $cp+1 , $sid, $tag , ADMIN_PAGE_SIZE);
	exit( json_encode($str) );
	
}else if( $method=='set' ){
	$rsid=$_GET['rsid'];
	$sTag=$_GET['stag'];
	$cp = $_GET['cp'];
	
	if( $tag!='all' )
		$r = getNextOne( $cp, $sid, $tag, ADMIN_PAGE_SIZE);
	else
		$r = NULL;
	setTag($rsid, $sTag );
	
	echo json_encode( array('res'=>1, "next"=>$r) );

}
else if( $method=='gnew' ){
	$msgList = getPageList(1, $sid, $tag , ADMIN_PAGE_SIZE);
	echo json_encode($msgList);
}


function getNewNums( $sid, $lastid )
{
	$wxdb = MyDB::getWxdb();
	$sql  = "select count(`rsid`) as `nums` from `wx_rsubmsg` where `sid`='{$sid}' and `rsid`>'{$lastid}'";
	$r = $wxdb->query($sql);
	$r = mysql_fetch_assoc($r);
	if( $r==NULL )
		return 0;
	else
		return $r;
	
}