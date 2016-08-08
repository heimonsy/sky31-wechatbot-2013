<?php
session_start();
require_once("../include/common.php");
require_once("../lib/functions.php");
if( !isLogin() ) header("Location:login.php");
 
$wxdb = MyDB::getWxdb();
 
if( isset($_GET['word']) )
{
	//alert($_GET['word']);
	$word = $_GET['word'];
	$word = str_replace(' ', '', $word);
	if( ($sid=TalkSubjectExtends::haveSubject($word))==false ){
		$sql = "insert into `wx_subject` 
					(`sid`, `word`) 
					values
					(null, '{$word}')";
		$r = $wxdb->query($sql);
		if( !$r ) return "内部错误".mysql_error();
		$sid = mysql_insert_id( $wxdb->link );
	}
	echo "<script>window.parent.location.href=\"subview.php?sid=".$sid."&tag=all\"</script>";
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
.jinrixiangda { height:auto; width:600px; margin:0 auto; line-height:30px; text-align:center; padding-top:20px; font-size:16px;}

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
.t_i { height:28px; line-height:24px; width:200px;}
.t_s { height:28px; width:80px;}
.rmht { height:auto; width:600px; margin:10px auto; line-height:40px; font-size:16px;}
</style>
</head>
<body>
<div id="h_padding" style="padding-top:30px; width:100%; height:1px;"></div>
<div class="path">当前位置：<a href="right.index.php">后台首页</a> >> <a href="#">话题定制</a></div>
<div class="title">话题定制</div>
<div class="jinrixiangda">
	<form action="" method="get">
    	<input class="t_i" type="text" name="word" /><br />
        <input class="t_s" type="submit" name="submit" value="定制">
    </form>
</div>
<?php
$sql = "select `word` from ( select count(`sid`) as `num`,`sid` from `wx_rsubmsg` group by `sid` order by `num`)as `aa` inner join `wx_subject` on `aa`.`sid`=`wx_subject`.`sid` limit 4 ";
$r = $wxdb->query($sql);

?>
<div class="rmht">热门话题：
<?php
while( ($m=mysql_fetch_assoc($r))!=NULL )
{
	echo "&nbsp;<a href=\"?word=".urlencode($m['word'])."\">".$m['word']."</a>&nbsp;";
}
?>
</div>
</body>
</html>