<?php
define("ADMIN_PAGE_SIZE", 20);
define("SHOW_PAGE_SIZE", 3);


function getPageList( $cp , $sid, $tag ,$pageSize)
{
	if( $cp<0 ) return 0;
	if( $tag=='all' )
		$sql  = "select `nname`,`wx_pic`.`pid` ,`rsid`,`cnt`,`tag`,`wx_rsubmsg`.`time`,`sum`,`lastid` from `wx_rsubmsg`
inner join `wx_user` on `wx_rsubmsg`.`uid`=`wx_user`.`uid`
inner join `wx_pic` on `wx_user`.`pid`=`wx_pic`.`pid` 
inner join ( select count(*) as `sum` from `wx_rsubmsg` where `sid`='{$sid}' ) as `stb`
inner join ( select max(`rsid`) as `lastid` from `wx_rsubmsg` where `sid`='{$sid}' ) as `las`
where `sid`='{$sid}' order by `rsid` desc limit ".(($cp-1)*$pageSize).",".($pageSize);
	else
		$sql ="select `nname`,`wx_pic`.`pid` ,`rsid`,`cnt`,`tag`,`wx_rsubmsg`.`time`,`sum`,`lastid` from `wx_rsubmsg`
inner join `wx_user` on `wx_rsubmsg`.`uid`=`wx_user`.`uid`
inner join `wx_pic` on `wx_user`.`pid`=`wx_pic`.`pid` 
inner join ( select count(*) as `sum` from `wx_rsubmsg` where `sid`='{$sid}' and `tag`='{$tag}' ) as `stb`
inner join ( select max(`rsid`) as `lastid` from `wx_rsubmsg` where `sid`='{$sid}' ) as `las`
where `sid`='{$sid}' and `tag`='{$tag}' order by `rsid` desc limit ".(($cp-1)*$pageSize).",".($pageSize);
	
	
	$wxdb = MyDB::getWxdb();
	
	$r = $wxdb->query( $sql );
	if( !$r ) exit( mysql_error() );
	
	$res=array();
	while( ($m=mysql_fetch_assoc($r))!=NULL ){
		$m['time']=date("H:i",$m['time']);
		$res[]=$m;
	}
	return $res;
}

function getWord( $sid )
{
	$wxdb = MyDB::getWxdb();
	$sql = "select `word` from `wx_subject` where `sid`='{$sid}'";
	$r  = $wxdb->query($sql);
	if( !$r ) exit( mysql_error() );
	$r  = mysql_fetch_assoc($r);
	return $r;
}

function setTag( $rsid,$tag)
{
	$sql = "update `wx_rsubmsg` set `tag`='{$tag}' where `rsid`='{$rsid}'";
	$wxdb = MyDB::getWxdb();
	
	$r = $wxdb->query($sql);
	return $r;
}

function getNextOne($cp , $sid, $tag ,$pageSize)
{
	$sql ="select `nname`,`wx_pic`.`pid` ,`rsid`,`cnt`,`tag`,`wx_rsubmsg`.`time`,`sum`,`lastid` from `wx_rsubmsg`
inner join `wx_user` on `wx_rsubmsg`.`uid`=`wx_user`.`uid`
inner join `wx_pic` on `wx_user`.`pid`=`wx_pic`.`pid` 
inner join ( select count(*) as `sum` from `wx_rsubmsg` where `sid`='{$sid}' and `tag`='{$tag}' ) as `stb`
inner join ( select max(`rsid`) as `lastid` from `wx_rsubmsg` where `sid`='{$sid}' ) as `las`
where `sid`='{$sid}' and `tag`='{$tag}' order by `rsid` desc limit ".($cp*$pageSize).", 1";

	$wxdb = MyDB::getWxdb();
	
	$r = $wxdb->query($sql);
	if( !$r ) exit(mysql_error());
	$r = mysql_fetch_assoc($r);
	$r['time']=date("H:i",$r['time']);
	return $r;
}

function getByLast( $sid, $tag ,$lastid)
{
	$sql ="select * from (select `nname`,`wx_pic`.`pid` ,`rsid`,`cnt`,`tag`,`wx_rsubmsg`.`time` from `wx_rsubmsg`
inner join `wx_user` on `wx_rsubmsg`.`uid`=`wx_user`.`uid`
inner join `wx_pic` on `wx_user`.`pid`=`wx_pic`.`pid` 
where `sid`='{$sid}' and `tag`='{$tag}' and `rsid`>'{$lastid}' limit 15) as `cc` order by `rsid` desc";
	$wxdb = MyDB::getWxdb();
	$r = $wxdb->query($sql);
	if( !$r ) exit( mysql_error() );
	$res = NULL;
	while( ($m=mysql_fetch_assoc($r))!=NULL ){
		$m['cnt'] = htmlspecialchars( str_replace( "/:", "",$m['cnt']));
		$m['time'] = date("H:i",$m['time']);
		$res[]=$m;
	}
	 return $res;
}

function getUserInfo($sid,$tag)
{
	$sql = "select `uid`,`pid`,`nname`,`wxn`,`fid` from `wx_user` where `uid` in (select `uid` from `wx_rsubmsg` where `sid`='{$sid}' and `tag` ='{$tag}') ORDER BY rand() limit 25";
	
	$wxdb = MyDB::getWxdb();
	$r = $wxdb->query($sql);
	if( !$r ) exit( mysql_error() );
	$res = NULL;

	while( ($m=mysql_fetch_assoc($r))!=NULL ){
		$res[]=$m;

	}
	return $res;
}

function wall_is_login(){
	if( $_SESSION['isAdmin']==true && isset($_SESSION['adminName']) )
		return true;
	else 
		return false;
}
function inject_check($sql_str){
		return preg_match('/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file
	|outfile/i', $sql_str);
}


function wall_login($user,$pw){
	
}
	
