<?php
// header("Content-type: text/html; charset=utf-8");
// header("Cache-Control: no-cache, must-revalidate");
// date_default_timezone_set('PRC');
require_once '../include/common.php';

//echo iconv('UTF-8', 'GB2312', '百度');
Debug::start();
getUserInfo_("课表", "1365521813");
Debug::echoTimes();

function getUserInfo_( $cont, $time)
{
	$ch=loginToWxMp_("250661062@qq.com", md5("woyaofei") );
	$str = $ch[1];
	$ch  = $ch[0];
	if( preg_match("/token=(.+?)\"/i", $str, $match) )
		$mpToken = $match[1];
	else 
		exit("Can't get token!");
	
	curl_setopt($ch, CURLOPT_POSTFIELDS, "keyword=".urlencode($cont)."&count=100&frommsgid=10000000&token=".$mpToken."&ajax=1");
	curl_setopt($ch, CURLOPT_URL, "http://mp.weixin.qq.com/cgi-bin/getmessage?t=ajax-message");
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
		print_r(array( 'fakeid'=>$match[0]->fakeId ,'nname' => $match[0]->nickName ) );
	}else{
		echo "Can't Find";
	}
	//echo iconv('UTF-8', 'GB2312', $str);
}

function loginToWxMp_($username, $pw)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR,  "");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "");
	curl_setopt($ch, CURLOPT_REFERER,    "http://mp.weixin.qq.com/");
	curl_setopt($ch, CURLOPT_URL, "http://mp.weixin.qq.com/");
	//四秒钟，因为微信只能支持5秒响应时间，超过5秒则无反应
	//curl_setopt($ch, CURLOPT_TIMEOUT,    4);
	curl_setopt($ch, CURLOPT_POST,    true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('username'=>$username,'pwd'=>$pw, 'f'=>'json' , 'imgcode'=>''));
	curl_setopt($ch, CURLOPT_URL, "http://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN");
	$str = curl_exec($ch);
	return array( $ch, $str );
}