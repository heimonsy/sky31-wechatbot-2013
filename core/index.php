<?php
require_once 'include/common.php';
error_reporting(E_ALL);
$postStr="<xml><ToUserName><![CDATA[gh_f3fadd8dfeb9]]></ToUserName>
<FromUserName><![CDATA[oYeDBjrIxjgl0haUk56FO-npcW9s]]></FromUserName>
<CreateTime>1364752972</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[成绩]]></Content>
<MsgId>5854629251579379899</MsgId>
</xml>";

try{
	Debug::start();
	$s = new WxService($postStr);
	
	$s->setResponseMsg();
	
	
	if( Debug::usedTimes()>4.7 )
		ErrorLogs::writeToLog($s->user->uid, __FILE__, __LINE__,
				 'mid:'.$s->receivedMsgMid." , 超时 ".Debug::usedTimes(), ErrorLogs::WARRING);
	
	if(ErrorLogs::noError())
		$s->returnMsg();
	

}catch (CURDException $e){
	ErrorLogs::writeToLog($s->user->uid, $e->getFile(), $e->getLine(), 'mid:'.$s->receivedMsgMid." , CURD错误：".$e->getMessage(), ErrorLogs::FATAL_ERROR);
	
	sendSorry($e->getMsgToUser());
	
}catch (OtherException $e){
	ErrorLogs::writeToLog($s->user->uid, $e->getFile(), $e->getLine(), 'mid:'.$s->receivedMsgMid." , 其他错误：".$e->getMessage(), ErrorLogs::FATAL_ERROR);
	$notice = "<font color='red'>$e->getMsgToUser()</font>";
	sendSorry($e->getMsgToUser());
}

MyDB::close();
