<?php

require_once 'include/common.php';
error_reporting(E_ALL);


echo PaymentCheckExtends::getFromBB("2011550320", "2011550320");

exit();
$str1 = " 郭子仟sdkfjasldf2348931(&UKLHKJHGLIOIU<>?><?L:./,./;'l;3!@#$#^%*&$#";
echo $sc=MyMcrypt::encrypt($str1);
echo "\n";

echo $str2=MyMcrypt::decrypt($sc);
if(strcmp($str1, $str2)==0)
	echo ".\nyes";
else echo ".\nno";