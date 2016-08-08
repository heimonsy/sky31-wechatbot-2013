<?php 
include_once '../../include/common.php';

if( isset($_GET['oa']) )
	$oa=$_GET['oa'];
else
	exit("ERROR");
	
$uid = Oauth::valid($oa);
if( !$uid ) exit("OA_ERROR!");

$wxdb = MyDB::getWxdb();
if( Oauth::haveBind($uid) ){
	$bind = Oauth::haveBind($uid);
	$user = Encodes::decode($bind['snum']);
	$pw   = Encodes::decode($bind['pw']);
	
	
	$year = intval(date("Y"));
	$month =  intval(date("n"));
	if( $month <7 ) $xq = ($year-1)."02";
	else $xq = $year."01";
	echo "";
	
	$ch = PostLogin::loginToStuM($user , $pw);
	//echo "http://202.197.224.134:8083/jwgl/cj/cj1_paiming.jsp?xq1=$xq&xh=$user";

	curl_setopt($ch, CURLOPT_URL, "http://202.197.224.134:8083/jwgl/cj/cj1_paiming.jsp?xq1=$xq&xh=$user");
	
	$str = curl_exec($ch);
	$str = iconv("GB2312","UTF-8",$str);
	
}else{
	$oauth = Oauth::getOauth($uid);
	$url = OAUTH_URL."?oa=".$oauth;
	$str="您还没有绑定个人信息。\n<a href=\"$url\">点此进行绑定</a>";
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>三翼校园</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />

<style>
* { margin:0px; padding:0px;}
#header
{
	height:42px;
	background-color:#69a8d5;
	color:#FFF; line-height:42px; text-align:center; font-size:20px;
	background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #69a9d5), color-stop(1, #497fb4)); /* Saf4+, Chrome */

}
#content
{
	height:auto; border:#999 solid 1px; border-bottom:none;
	margin-left:10px; margin-right:10px; margin-top:12px; margin-bottom:12px;
}
#notice
{
	height:auto;
	line-height:32px; font-size:18px;
}
.ftit
{
	line-height:20px; font-size:16px;
}

.finp
{
	border-radius:8px;
	height:26px;
	width:auto;
}


.fsub {
	margin-top:10px; margin-left:30px;
	height:32px; width:100px;
	border-radius:2px;
	color: #d9eef7;
	border: solid 1px #0076a3;
	background: #0095cd;
	background: -webkit-gradient(linear, left top, left bottom, from(#00adee), to(#0078a5));
	background: -moz-linear-gradient(top,  #00adee,  #0078a5);
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#00adee', endColorstr='#0078a5');
}
.fsub:hover {
	background: #007ead;
	background: -webkit-gradient(linear, left top, left bottom, from(#0095cc), to(#00678e));
	background: -moz-linear-gradient(top,  #0095cc,  #00678e);
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#0095cc', endColorstr='#00678e');
}
.fsub:active {
	color: #80bed6;
	background: -webkit-gradient(linear, left top, left bottom, from(#0078a5), to(#00adee));
	background: -moz-linear-gradient(top,  #0078a5,  #00adee);
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#0078a5', endColorstr='#00adee');
}

#footer
{
	height:52px;
	background-color:#333;
	line-height:52px; color:#FFF; font-size:16px; text-align:center;
	background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #404040), color-stop(1, #0c0c0c)); /* Saf4+, Chrome */
}
#content .tit { height:32px; text-align:center; line-height:32px; font-size:20px; color:#06F; text-align:center; border-bottom:#999 solid 1px; font-weight:bold;}
.cnt { padding-left:10px; padding-right:10px; line-height:24px; border-bottom:#999 solid 1px; font-size:18px;}
</style>
</head>

<body>
<div id="header">你的排名
</div>
<div id="content">

<?php
echo $str;
?>
</div>
    <div id="footer">三翼校园 Copyright © 2004-2013
    </div>
</body>
</html>
