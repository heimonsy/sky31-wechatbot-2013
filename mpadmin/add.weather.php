<?php
session_start();
require_once("../include/common.php");
require_once("../lib/functions.php");
if( !isLogin() ) header("Location:login.php");

if( isset($_GET['update']) )
{
	$ch=loginToWxMp(SKY31_USERNAME, SKY31_PW);
	$str = $ch[1];
	$ch  = $ch[0];
	preg_match("/token=(.+?)\"/i", $str, $match);
	curl_setopt($ch, CURLOPT_POST, false);
	curl_setopt($ch, CURLOPT_URL,"https://mp.weixin.qq.com/cgi-bin/filemanagepage?t=wxm-file&lang=zh_CN&token=".$match[1]."&type=3&pagesize=10&pageidx=0");
	$str = curl_exec($ch);
	$str = str_replace("\n", '', $str);
	$str = str_replace("\r", '', $str);
	$reg = "/<script type=\"json\" id=\"json-fileList\">(.+?)<\/script>/i";
	if(preg_match($reg, $str, $matchs)){

		$matchs[1]=str_replace("\t","", $matchs[1]);
		$json = json_decode($matchs[1]);
		$json = $json[0];
		//echo $json->id;
		//exit();
		curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_URL,	"https://mp.weixin.qq.com/cgi-bin/getvoicedata?token=".$match[1]."&msgid=".$json->id."&fileid=".$json->id."&source=file");
		
		$f = fopen("uploads/today.mp3", "w+");
		$str = curl_exec($ch);
		fwrite($f, $str);
		fclose($f);
		$wxdb = MyDB::getWxdb();
		$json = addslashes( json_encode($json) );
		$sql  = "update `weather_today` set `json`='{$json}'";
		$r    = $wxdb->query($sql);
		if( $r )
			alert("更新成功", "add.weather.php");
		else
			alert( "更新失败，请重试 ".mysql_error(), "add.weather.php" );
	}else alert("更新失败，请重试", "add.weather.php");
	
	exit();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style>
body{ background-color:#9eb6d8;}
.path { height:34px; width:auto; margin-left:20px; margin-right:20px; border-bottom:#eee 1px dashed; line-height:34px; font-size:14px; text-indent:5px;}
.content {height:auto; width:600px; margin:0 auto; line-height:28px;}
a { color:#00F; text-decoration:none;}
a:hover{ text-decoration:underline;}
.add_content { width:500px; height:150px; margin:0 auto; border:#fff solid 1px;}
.add_content .title { height:32px; text-align:center; line-height:32px; font-size:16px;}
.filelist { width:500px; height:auto; margin:0 auto; margin-top:10px; border:#fff solid 1px;}
.filelist .title{ height:32px; text-align:center; line-height:32px; font-size:16px;}
.filelist .list{}
#h_title { height:34px; text-align:center; line-height:34px; font-size:16px; margin:10px auto;}
.update { height:34px; line-height:34px; text-align:center; font-size:14px; margin:10px auto;}
.today { height:80px; margin:34px auto; width:420px; overflow:hidden; text-align:center; font-size:14px; line-height:26px;}
</style>
</head>
<body>
<div id="h_padding" style="padding-top:30px; width:100%; height:1px;"></div>
<div class="path">当前位置：<a href="right.index.php">后台首页</a> >> <a href="#">添加素材</a></div>

<div id="h_title">天气播报更新</a>
</div>

<div class="update">
<a href="?update=today">点击此处更新</a>
</div>
<?php
$wxdb= MyDB::getWxdb();
$sql = "select `json` from `weather_today`";
$r = $wxdb->query($sql);
$r = mysql_fetch_assoc($r);
if( $r!==NULL ){
	$json=json_decode($r['json']);
?>
<div class="today">
	<?php echo $json->fileName; ?>
	<br/>
    <audio controls="controls">
      <source src="uploads/today.mp3" type="audio/mp3" />
      您的浏览器不支持播放！
    </audio>
</div>
<?php }?>
</body>
</html>