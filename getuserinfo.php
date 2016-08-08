<?php
ignore_user_abort(true);
set_time_limit(30);
require_once 'include/common.php';

$f = fopen("1.log",'w');
if( !isset($_GET['mid'] ) ){
	//fwrite($f,"error 1");
	exit("error 1");
}
$mid = $_GET['mid'];
fwrite($f,date("H:i:s", time()));
fclose( $f );

$sql = "select `fuid`,`fc` from `wx_msg_rec` where `id`='{$mid}'";
$wxdb = MyDB::getWxdb();
$r = $wxdb->query($sql);
if( !$r ) exit(mysql_error());
$r = mysql_fetch_assoc($r);
if($r==NULL ) exit("ERROR");
$mf = new WxMsgFactory( $r['fc'] );
$msg = $mf->getMsg();

$uid = $r['fuid'];
$info = getUserInfo_( preg_replace("/#.*?#/i","", $msg->content) , $msg->time, $uid);

if( $info==false ) die("false cant");

$sql ="update `wx_user` set `fid`='{$info['fakeid']}', `wxn`='{$info['wxn']}' , `pid`='{$info['pid']}',`nname`='{$info['nname']}' where `uid`='{$uid}'";

$r = $wxdb->query($sql);

if( !$r ) die(mysql_error());
//print_r($info);



/***************************
 * 
 * functions
 * 
 * 
 ***************************/



function getUserInfo_( $cont, $time ,$uid)
{
	$wxdb = MyDB::getWxdb();
	
	$ch=loginToWxMp_( SKY31_USERNAME, SKY31_PW );
	$str = $ch[1];
	$ch  = $ch[0];
	if( preg_match("/token=(.+?)\"/i", $str, $match) )
		$mpToken = $match[1];
	else
		exit("Can't get token!");

	curl_setopt($ch, CURLOPT_POSTFIELDS, "keyword=".urlencode($cont)."&count=100&frommsgid=10000000&token=".$mpToken."&ajax=1");
	curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/getmessage?t=ajax-message");
	$str = curl_exec($ch);
	//echo $str;
	$tag = true;
	$json = json_decode($str);
	$match = array();
	foreach ($json as $v){
		if( $v->dateTime == $time){
			if( count($match)==0 )
				array_push( $match, $v);
			else{
				$tag=false;
				break;
			}
		}
	}
	if( $tag &&  count($match)==1 ){
		curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".$mpToken."&ajax=1");
		curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/getcontactinfo?t=ajax-getcontactinfo&lang=zh_CN&fakeid=".$match[0]->fakeId);
		$str = curl_exec($ch);
		$json = json_decode($str);
		//print_r($json);
		$time = time();
		$sql = "insert into `wx_pic` ( `pid`,`uid`, `url`,`time` ) values ( null, '{$uid}' , '{$match[0]->fakeId}', '$time') ";
		$r = $wxdb->query($sql);
		$pid = mysql_insert_id( $wxdb->link );
		
		storeImg($ch, $mpToken, $match[0]->fakeId ,$pid);
		return array( 'fakeid'=>$match[0]->fakeId ,'nname' => $match[0]->nickName , 'wxn' => $json->Username, 'pid'=>$pid);
		
	}else{
		return  false;
	}
	//echo iconv('UTF-8', 'GB2312', $str);
}

function loginToWxMp_($username, $pw)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR,  "");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "");
	curl_setopt($ch, CURLOPT_REFERER,    "https://mp.weixin.qq.com/");
	curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/");
	//四秒钟，因为微信只能支持5秒响应时间，超过5秒则无反应
	//curl_setopt($ch, CURLOPT_TIMEOUT,    4);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_POST,    true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('username'=>$username,'pwd'=>$pw, 'f'=>'json' , 'imgcode'=>''));
	curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN");
	$str = curl_exec($ch);
	return array( $ch, $str );
}

function storeImg($ch, $mpToken, $fakeid, $pid)
{
	curl_setopt($ch, CURLOPT_POST,    false);
	curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/getheadimg?token=".$mpToken."&fakeid=".$fakeid);
	$fcont = curl_exec($ch);
	
	if( $pid>999999 ){
		$str = $pid."";
		$l = strlen($str);
	}else{
		$str = str_pad($pid, 7,'0', STR_PAD_LEFT );
		$l   = 7;
	}
	$dir_2 = substr( $str, 0, $l-6)."/";
	$dir_1 = substr( $str, $l-6, 3)."/";
	$fpath = $dir_2.$dir_1.substr( $str, $l-3, 3).".jpg";
	
	$fs  = fopen("data/pic/".$fpath, "w");
	fwrite($fs, $fcont);
	fclose($fs);
	return true;
}
