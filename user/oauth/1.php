<?php
session_start();
require_once '../../core/include/common.php';

error_reporting(E_ALL);

$sql = "select `snum`,`name`,`pw` from `stu_info`";
$wxdb = MyDB::getWxdb();
$r =$wxdb->query($sql);
if($r) {
	while(($m=mysql_fetch_assoc($r))!=NULL) {
		if(Encodes::decode($m['snum'])=="2011960509"){
			echo Encodes::decode($m['name']);
		}
	}
}else 
	echo mysql_error();

exit();
$uid = 1695;
$ch = new MyUrlFetch();
$fields = array(
		'username'=>"2011961037", 'password'=>"shixuehudie",
		'identity'=>'student', 'role'=>'1');
$ch->setPostArray($fields);
$str = iconv("GB2312", "UTF-8",
		$ch->post("http://202.197.224.134:8083/jwgl/logincheck.jsp"));
$code = $ch->getHttpCode()."";
if($code[0]=='2') {
	if(strpos($str, "密码错误") == FALSE) {
		$ch->post("http://202.197.224.134:8083/jwgl/index1.jsp");
		$str = @iconv("GBK", "UTF-8//IGNORE",
				$ch->get("http://202.197.224.134:8083/jwgl/xk/xk1_kb_gr.jsp?xq1=01"));

	} else {
		self::$error_info = '密码错误哦。点击链接重新绑定：'.Oauth::getBindUrl($uid);
		return false;
	}
}
var_dump($str);
