<?php
include "../../core/include/common.php";
error_reporting(E_ALL|E_NOTICE);

if(isset($_GET['mid']))
	$mid = $_GET['mid'];
else
	throw new OtherException("mid没有给出参数", "");
$db = MyDB::getWxdb();

$res = getMsg($mid);
$uid = $res['uid'];  $msgStr = $res['content'];
$sql = "select `status` from `get_info_logs` where `uid`='{$uid}'";
$r   = $db->query($sql);
if(!$r)
	throw new CURDException("get_info_logs 获取状态失败", mysql_error($db->link));
$res = mysql_fetch_assoc($r);
if($res == NULL) {
	$sql = "insert into `get_info_logs`  values (NULL, '{$uid}', 1)";
	$insertRes = $db->query($sql);
	if(!$insertRes)
		throw new CURDException("insert get_info_logs 出错。", "", mysql_error($db->link));

} else if($res['status'] == 1)
	exit("runing");
else {
	$sql = "update `get_info_logs` set `status`=1 where `uid` = '{$uid}'";
	$updateRes = $db->query($sql);
	if(!$updateRes)
		throw new CURDException("update get_info_logs 出错。uid=".$uid, "", mysql_error($db->link));
	//sleep(10);
}
try {
	
	$msg = WxMsgFactory::loadFromXml($msgStr);
	$rs  = UserInfoTool::loginToWxMp(SKY31_USERNAME, SKY31_PW);
	$ch  = $rs['ch'];
	$token = $rs['token'];
	$info  = UserInfoTool::getUserInfo($msg->content, $msg->time, $token, $ch);
	UserInfoTool::storeImg($info['fakeid'], $uid, $token, $ch);
	$sql = "update `wx_user` set `fid`='{$info['fakeid']}', `wxn`='{$info['username']}', `nname`='{$info['nickName']}' where `uid`='{$uid}'";
	$r = $db->query($sql);
	if(!$r)
		throw new CURDException("更新用户信息失败, uid=".$uid, "", mysql_error($db->link));
	
}catch (Exception $e) {
	ErrorLogs::writeToLog("uncatched_exception_record", $e->getFile(), $e->getLine(), $e->getMessage(), ErrorLogs::FATAL_ERROR);
}


$sql = "update `get_info_logs` set `status`=0 where `uid`='{$uid}'";
$r = $db->query($sql);
if(!$r)
	throw new CURDException("!!! 更新用户状态为 0 失败, uid=".$uid, "", mysql_error($db->link));


function getMsg($mid) {
	if(!is_numeric($mid))
		throw  new OtherException("mid不是一个数字.", "");
	$db = MyDB::getWxdb();
	$sql = "select `fc`,`fuid` from `wx_msg_rec` where `id` = {$mid} ";
	$r = $db->query($sql);
	if(!$r)
		throw new CURDException("wx_msg_rec ,", "", mysql_error($db->link));
	$res = mysql_fetch_assoc($r);
	if($res == NULL)
		throw new CURDException("没有找到信息，可能是mid错误：", "", mysql_error($db->link));
	return array('uid'=>$res['fuid'], 'content'=>$res['fc']);
}