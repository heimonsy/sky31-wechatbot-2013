<?php

/**
  用于平台验证
 */
function checkSignature()
{
	$signature = $_GET["signature"];
	$timestamp = $_GET["timestamp"];
	$nonce = $_GET["nonce"];	

	$token = TOKEN;
	$tmpArr = array($token, $timestamp, $nonce);
	sort($tmpArr);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );
	
	if( $tmpStr == $signature ){
		return true;
	}else{
		return false;
	}
}
function valid()
{
	$echoStr = $_GET["echostr"];
	if(checkSignature()){
		echo $echoStr;
		exit;
	}else echo "error";
}



function alert( $msg, $href=null)
{
	if($href==null)
		echo "<script type=\"text/javascript\">alert(\"$msg\");</script>\n";
	else
		echo "<script type=\"text/javascript\">alert(\"$msg\");location.href=\"$href\";</script>";
}




/**
  

  电台相关函数


 */
/**
   获取电台的xml对象
 * @return $xmlObj
 */
function getRadioXml()
{
	$xmlObj = simplexml_load_file('http://radio.sky31.com/player/xml/mp3_player.xml');
	//return count($xml->album);
	return $xmlObj;
}
/*
 */
function getListFromXml($xml, $index)
{
	$xml = $xml->album[$index];
	//print_r($xml);
	$list_nums = count($xml->song);
	$res="";
	for($i=0;$i<9 && $i<$list_nums;$i++)
	{
		$att = $xml->song[$i]->attributes();
		$res.=($i+1);
		$res.=", ";
		$res.=$att->name;
		$res.="\n";
	}
	return $res;
}
/**
  获取列表内容
 * @param $lmName $INDEX
 * @return $content
 */
function getRadioListContet($lmName, $INDEX)
{
	$content="欢迎进入 ".$lmName.", 节目列表如下：\n";
	$xml=getRadioXml();
	$content.=getListFromXml($xml, $INDEX);
	$content.=Notices::RADIO_CHOISE;
	$content.=Notices::COMMON_EXIT;
	return $content;
}

function getRadioObj($xml, $index, $songNum)
{
	//因为-1
	$songNum--;
	if( $songNum>=0 &&$songNum<9 && $songNum>count($xml->album[$index]->song) ) return false;
	return $xml->album[$index]->song[intval($songNum)]->attributes();
}

function getRadioFile($radioObj)
{
	return $radioObj->buyLink."/".$radioObj->downloadSource;
}

/**
 * 根据用户的输入，获取并向用户返回msg
 * @param $receivedMsg $index $songNum
 */
function getRadioFileMsg($receivedMsg, $index, $songNum)
{
	$responseMsg = null;
	$msgFactory  = new WxMsgFactory();

	if(is_numeric( $songNum ) && $radioObj=getRadioObj(getRadioXml(), $index, $songNum) ){

		$url= getRadioFile($radioObj);
		$msgFactory->setMusicMsg(
			$receivedMsg->fromUserName,
			$receivedMsg->toUserName,
			time(),
			$radioObj->name,
			"四季电台",
			$url,
			$url
		);
		$responseMsg=$msgFactory->getMsg();

	}else{//指令无效的情况

		$content = "您输入的指令有无效！\n";
		$content .= getRadioListContet( Notices::getRadioLmTitle($index), $index );
		$msgFactory->setTextMsg(
			$receivedMsg->fromUserName,
			$receivedMsg->toUserName,
			$content,
			time()
		);
		$responseMsg=$msgFactory->getMsg();

	}
	return $responseMsg;
}





function mkDate($time)
{
	return date("Y-m-d H:i:s",$time);
}




function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

//模拟登陆微信平台
function loginToWxMp($username, $pw)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR,  "");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "");
	curl_setopt($ch, CURLOPT_REFERER,    "https://mp.weixin.qq.com/");
	curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/loginpage?t=wxm2-login&lang=zh_CN");
	//四秒钟，因为微信只能支持5秒响应时间，超过5秒则无反应
	//curl_setopt($ch, CURLOPT_TIMEOUT,    4);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	//$str = curl_exec($ch);
	
	//var_dump($str);
	//exit();
	
	curl_setopt($ch, CURLOPT_POST,    true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('username'=>$username,'pwd'=>$pw, 'f'=>'json' , 'imgcode'=>''));
	curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN");
	$str = curl_exec($ch);
	return array( $ch, $str );
}

//从公众平台获取用户信息
function getUserInfoFromMp($msgContent,$createTime)
{
	//模拟登陆，获取页面
	//$f = fopen("log.log","w+");
	$msgContent = urlencode($msgContent);
	$ch=loginToWxMp();
	curl_setopt($ch, CURLOPT_POST,    false);
	curl_setopt($ch, CURLOPT_URL, "http://mp.weixin.qq.com/cgi-bin/getmessage?t=wxm-message&lang=zh_CN&count=10&keyword=".$msgContent);
	$str = curl_exec($ch);

	//通过正则过滤无用数据
	$reg = null;
	//$reg = "/<script type=\"json\" id=\"json-msgList\">(.+)<\/script><script type=\"text\/javascript\">window\.WXM/";
	$str = str_replace("\n", '', $str);
	preg_match($reg, $str, $match);
	
	//获取fakeid和nickname
	$json = json_decode($match[1]);
	$len  = count($json);
	//fwrite($f, $match[1]);
	//print_r($json);
	$res=false;
	for( $i=0; $i<$len; $i++){
		if( $json[$i]->dateTime==$createTime ){
			//echo $json[$i]->fakeId;
			//echo $json[$i]->nickName;
			$res = array('fakeid'=>$json[$i]->fakeId,'nickname'=>$json[$i]->nickName);
			break;
		}
	}
	//fclose($f);
	curl_close($ch);
	return $res;
}


function debug($var,$value)
{
	echo "$var : $value<br />\n";
}


function isLogin()
{
	return isset( $_SESSION['admin'] ) && $_SESSION['admin']=='SURPER';
}


function replaceWarp($str) {
	return str_replace("\r", "", str_replace("\n", "", $str));
}

function uncatched_exception_record($e) {

	ErrorLogs::writeToLog("uncatched_exception_record", $e->getFile(), $e->getLine(), $e->getMessage(), ErrorLogs::FATAL_ERROR);
	sendSorry($e->getMsgToUser());
}

function my_error_handler($errno, $errstr, $errfile, $errline) {
	$error_name = array(E_ERROR=>"FATAL ERROR:",E_WARNING=>"WARNING:", E_NOTICE=>"E_NOTICE", E_PARSE=>"PARSE ERROR:", E_DEPRECATED=>"Run time notice:");
	ErrorLogs::writeToLog("my_error_handler", $errfile, $errline, $error_name[$errno]." , ".$errstr, ErrorLogs::FATAL_ERROR);
	sendSorry();
}

function shutdown_error() {

	$error_name = array(E_ERROR=>"FATAL ERROR:",E_WARNING=>"WARNING:", E_NOTICE=>"E_NOTICE", E_PARSE=>"PARSE ERROR:", E_DEPRECATED=>"Run time notice:", E_STRICT=>"E_STRICT:");
	$error = error_get_last();
	if($error===NULL) exit();
	ErrorLogs::writeToLog("shutdown_error", $error['file'], $error['line'], $error_name[$error['type']]." , ".$error['message'], ErrorLogs::FATAL_ERROR);
	sendSorry();
}

function sendSorry($text = NULL){
	static $tag = 0; //如果已经用过一次就不需要在用了
	if($tag == 0) $tag=1;
	else return ;
	
	if($text==NULL) $text = "服务器奔溃了T_T..，稍后再试吧"; 
	$msg = WxMsgFactory::$receivedMsg;
	if($msg==null) exit();
	$textTpl="<xml>
			<ToUserName><![CDATA[".$msg->fromUserName."]]></ToUserName>
			<FromUserName><![CDATA[".$msg->toUserName."]]></FromUserName> 
			<CreateTime>".time()."</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[".$text."]]></Content>
			<FuncFlag>0</FuncFlag>
			</xml>
		";
	echo $textTpl;
}

