<?php
session_start();
require_once("../include/common.php");
require_once("../lib/functions.php");
if( !isLogin() ) header("Location:login.php");
 
$wxdb = MyDB::getWxdb();
 
if( isset($_GET['update']) )
{
	$ch=loginToWxMp(SKY31_USERNAME, SKY31_PW);
	//var_dump($ch);
	//exit();
	$str = $ch[1];
	$ch  = $ch[0];
	preg_match("/token=(.+?)\"/i", $str, $match);
	//print_r($match);
	//exit();
	curl_setopt($ch, CURLOPT_POST, false);
	//https://mp.weixin.qq.com/cgi-bin/operate_appmsg?sub=list&type=10&subtype=3&t=wxm-appmsgs-list-new&pagesize=10&pageidx=0&token=2000159436&lang=zh_CN
	curl_setopt($ch, CURLOPT_URL,"https://mp.weixin.qq.com/cgi-bin/operate_appmsg?sub=list&type=10&subtype=3&t=wxm-appmsgs-list-new&pagesize=10&pageidx=0&token=".$match[1]."&lang=zh_CN");
	
	$str = curl_exec($ch);
	//var_dump($str);
	//exit();
	//echo($str);
	//exit();
	curl_close( $ch );
	$str = str_replace("\n", '', $str);
	$str = str_replace("\r", '', $str);
	$reg = "/<script id=\"json-msgList\" type=\"json\">(.+?)<\/script><script>/i";
	if(preg_match($reg, $str, $matchs)){
		$json = json_decode( str_replace('/cgi-bin/proxy?url=','',$matchs[1]) );
		$json =	$json->list[0]->appmsgList;
		
		$json = addslashes(json_encode($json));
		
		$sql = "update `news_today` set `json`='{$json}'";
		$r = $wxdb->query($sql);
		if( !$r )
			alert("更新失败，请重试", "today.php");
		else
			alert("更新成功", "today.php");
	}
	else{
		alert("更新失败，请重试", "today.php");
	}
	exit();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style>
body{ background-color:#9eb6d8; font-family:"微软雅黑";}
.path { height:34px; width:auto; margin-left:20px; margin-right:20px; border-bottom:#eee 1px dashed; line-height:34px; font-size:14px; text-indent:5px;}
.content {height:auto; width:600px; margin:0 auto; line-height:28px;}
.title { width:600px; margin:0 auto; line-height:28px; text-align:center; font-weight:bold; height:auto; border-bottom:#fff dashed 1px;}
a { color:#00F; text-decoration:none;}
a:hover{ text-decoration:underline;}
#wzid { width:150px;}
form { padding:0px; margin:0px; width:auto; height:auto;}
.jinrixiangda { height:30px; width:600px; margin:0 auto; line-height:30px; text-align:center; padding-top:20px; font-size:16px;}

.news_list { height:auto; width:720px; background-color:#FFF; margin:0 auto; margin-top:14px; border:#999 solid 1px;}
.news_list .head { height:300px; width:700px; margin:20px auto 10px auto; position:relative;}
.news_list .head .tit { width:700px; height:38px; line-height:38px; position:absolute; bottom:0px; background-image:url(templates/images/1.png); font-size:18px; font-weight:bold; color:#FFF; text-indent:10px;}
.news_list .head .tit a{ color:#FFF;}
.news_list .line { height:0px; border-top:#999 solid 1px; overflow:hidden; width:720px;}
.lis { height:100px; width:700px; margin:0 auto;}
.lis .left { float:left; height:80px; margin-top:10px; margin-bottom:10px; line-height:40px; font-size:18px; width:600px;}
.lis .left a{ color:#000;}
.lis .right { float:right; margin-right:10px; width:80px; height:80px; margin-top:10px; margin-bottom:10px;}
a img { border:none;}
</style>
</head>
<body>
<div id="h_padding" style="padding-top:30px; width:100%; height:1px;"></div>
<div class="path">当前位置：<a href="right.index.php">后台首页</a> >> <a href="#">今日新闻</a></div>
<div class="title">今日新闻</div>
<div class="jinrixiangda">
	<a href="?update=today">点击此处更新</a>
</div>
<?php
$sql = " select `json` from `news_today`";
$r = $wxdb->query($sql);
$r = mysql_fetch_assoc($r);
$r = json_decode($r['json']);
//print_r($r);
?>
<div class="news_list">
	<div class="head">
    	<a href="<?php echo $r[0]->url; ?>" target="_blank"><img src="../data/pic/getpic.php?url=<?php echo urlencode($r[0]->imgURL); ?>" height="300" width="700" /></a>
	    <div class="tit"><a href="<?php echo $r[0]->url; ?>" target="_blank"><?php echo $r[0]->title; ?></a></div>
    </div>
<?php
$l = count($r);
for($i=1;$i<$l;$i++){
?>
    <div class="line"></div>
    <div class="lis">
    	<div class="left"><a href="<?php echo $r[$i]->url; ?>" target="_blank"><?php echo $r[$i]->title; ?></a></div>
        <div class="right"><a href="<?php echo $r[$i]->url; ?>" target="_blank"><img src="../data/pic/getpic.php?url=<?php echo urlencode($r[$i]->imgURL); ?>" height="80" width="80"/></a></div>
    </div>
    
<?php
}
?>
</div>

</body>
</html>