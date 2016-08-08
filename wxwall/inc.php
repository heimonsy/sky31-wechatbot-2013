<?php
define("ADMIN_PAGE_SIZE", 20);
define("SHOW_PAGE_SIZE", 3);


function getPageList( $cp , $sid, $tag ,$pageSize)
{
	if( $cp<0 ) return 0;
	if( $tag=='all' )
		$sql  = "select `wx_user`.`uid`,`nname`, `rsid`,`cnt`,`tag`,`wx_rsubmsg`.`time`,`sum`,`lastid` from `wx_rsubmsg`
inner join `wx_user` on `wx_rsubmsg`.`uid`=`wx_user`.`uid`
inner join ( select count(*) as `sum` from `wx_rsubmsg` where `sid`='{$sid}' ) as `stb`
inner join ( select max(`rsid`) as `lastid` from `wx_rsubmsg` where `sid`='{$sid}' ) as `las`
where `sid`='{$sid}' order by `rsid` desc limit ".(($cp-1)*$pageSize).",".($pageSize);
	else
		$sql ="select `wx_user`.`uid`,`nname`, `rsid`,`cnt`,`tag`,`wx_rsubmsg`.`time`,`sum`,`lastid` from `wx_rsubmsg`
inner join `wx_user` on `wx_rsubmsg`.`uid`=`wx_user`.`uid`
inner join ( select count(*) as `sum` from `wx_rsubmsg` where `sid`='{$sid}' and `tag`='{$tag}' ) as `stb`
inner join ( select max(`rsid`) as `lastid` from `wx_rsubmsg` where `sid`='{$sid}' ) as `las`
where `sid`='{$sid}' and `tag`='{$tag}' order by `rsid` desc limit ".(($cp-1)*$pageSize).",".($pageSize);
	
	//echo $sql;
	$wxdb = MyDB::getWxdb();
	
	$r = $wxdb->query( $sql );
	if( !$r ) exit( mysql_error() );
	
	$res=array();
	while( ($m=mysql_fetch_assoc($r))!=NULL ){
		$m['time']=date("H:i",$m['time']);
		$m['purl']=UserInfoTool::getHeadPic($m['uid']);
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

function countMsgNums($sid,$tag){
	$sql = "select count(*) from `wx_rsubmsg` where `sid`='{$sid}' and `tag`='{$tag}'";
	$wxdb = MyDB::getWxdb();
	$r = $wxdb->query($sql);
	$r = mysql_fetch_array($r);
	return $r[0];
}

function getNextOne($cp , $sid, $tag ,$pageSize)
{
	$sql ="select `wx_user`.`uid`,`nname` ,`rsid`,`cnt`,`tag`,`wx_rsubmsg`.`time`,`sum`,`lastid` from `wx_rsubmsg`
inner join `wx_user` on `wx_rsubmsg`.`uid`=`wx_user`.`uid`
inner join ( select count(*) as `sum` from `wx_rsubmsg` where `sid`='{$sid}' and `tag`='{$tag}' ) as `stb`
inner join ( select max(`rsid`) as `lastid` from `wx_rsubmsg` where `sid`='{$sid}' ) as `las`
where `sid`='{$sid}' and `tag`='{$tag}' order by `rsid` desc limit ".($cp*$pageSize).", 1";

	$wxdb = MyDB::getWxdb();
	
	$r = $wxdb->query($sql);
	if( !$r ) exit(mysql_error());
	$r = mysql_fetch_assoc($r);
	$r['time']=date("H:i",$r['time']);
	$r['purl']=UserInfoTool::getHeadPic($r['uid']);
	return $r;
}

function getByLast( $sid, $tag ,$lastid, $sort='desc')
{
	$sql ="select * from (select `wx_user`.`uid`, `nname`,`rsid`,`cnt`,`tag`,`wx_rsubmsg`.`time` from `wx_rsubmsg`
inner join `wx_user` on `wx_rsubmsg`.`uid`=`wx_user`.`uid`
where `sid`='{$sid}' and `tag`='{$tag}' and `rsid`>'{$lastid}') as `cc` order by `rsid` {$sort}";
	$wxdb = MyDB::getWxdb();
	$r = $wxdb->query($sql);
	if( !$r ) exit( mysql_error() );
	$res = NULL;
	while( ($m=mysql_fetch_assoc($r))!=NULL ){
		$m['cnt'] = htmlspecialchars( str_replace( "/:", "",$m['cnt']));
		$m['time'] = date("H:i",$m['time']);
		$m['purl']=UserInfoTool::getHeadPic($m['uid']);
		$res[]=$m;
	}
	 return $res;
}

function getUserInfo($sid,$tag)
{
	$sql = "select DISTINCT `wx_rsubmsg`.`uid`,`nname`,`wxn` from `wx_user`,`wx_rsubmsg` where `wx_rsubmsg`.`sid`='{$sid}' and `wx_user`.`uid`=`wx_rsubmsg`.`uid`";
	
	$wxdb = MyDB::getWxdb();
	$r = $wxdb->query($sql);
	if( !$r ) exit( mysql_error() );
	$res = NULL;

	while( ($m=mysql_fetch_assoc($r))!=NULL ){
		//echo $m['wxn']."\n";
		if($m['nname']==NULL) continue;
		$m['purl'] = UserInfoTool::getHeadPic($m['uid']);
		$res[] = $m;
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
	
