<?php

require_once 'include/common.php';
error_reporting(E_ALL|E_NOTICE);
mkdir("2011");
mkdir("2011/05");
$res = fopen("2011/05/1.jpg","w");
fclose($res);
exit();
//echo md5("wx20130517");

$word = "#你好#dhhhghjfg";
$rs = UserInfoTool::loginToWxMp("ceo@sky31.com", "595568ec41c753e222ab34211e531b35");
$ch = $rs['ch'];
$token = $rs['token'];

$info = UserInfoTool::getUserInfo($word, "1377997011", $token, $ch);




var_dump($info);

exit();
echo PaymentCheckExtends::getFromBB("2011550320", "2011550320");

exit();
$str1 = " 郭子仟sdkfjasldf2348931(&UKLHKJHGLIOIU<>?><?L:./,./;'l;3!@#$#^%*&$#";
echo $sc=MyMcrypt::encrypt($str1);
echo "\n";

echo $str2=MyMcrypt::decrypt($sc);
if(strcmp($str1, $str2)==0)
	echo ".\nyes";
else echo ".\nno";