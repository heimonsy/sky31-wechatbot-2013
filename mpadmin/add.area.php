<?php
session_start();
require_once("../include/common.php");
require_once("../lib/functions.php");
if( !isLogin() ) header("Location:login.php");

$wxdb = MyDB::getWxdb();
if( isset($_POST['submit']) )
{
	$areaName  = $_POST['areaName'];
	$sort = $_POST['sort'];
	$sql = "insert into `take_area` (`id`,`name`,`sort` ) values(null,'{$areaName}','{$sort}')";
	if($wxdb->query($sql)) 
		alert("添加成功","add.area.php");
	else 
		alert("添加失败".mysql_error(),"add.area.php");
    	
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

</style>
</head>
<body>
<div id="h_padding" style="padding-top:30px; width:100%; height:1px;"></div>
<div class="path">当前位置：<a href="right.index.php">后台首页</a> &nbsp;  >> <a href="#">添加地域</a></div>

<div class="add_content">
	<div class="title">添加地区信息</div>
	<div class="df">
	<form action="" method="post" enctype="multipart/form-data" >
    	<table cellpadding="0" cellspacing="2" border="0" width="100%">
        
        	<tr >
            	<td align="right" width="25%" height="35">地域名称</td>
                <td><input type="text" name="areaName"/> 如：金翰林</td>
            </tr>
            <tr>
            	<td align="right" height="35">排序:</td>
                <td><input type="text" name="sort" value="1"/> 数字</td>
            </tr>
            <tr>
            	<td align="right" height="35"></td>
                <td><input type="submit" value="提交" name="submit"/></td>
            </tr>
           
        </table>
     </form>
    </div>
</div>

<div class="filelist">
	<div class="title">地区信息列表</div>
    <div class="list">
    <table cellpadding="0" cellspacing="2" border="0" width="100%">
    	<tr align="center" height="32">
        	<td>地域名</td>
            <td>排序</td>
            <td>操作</td>
        </tr>
<?php
	$sql="select * from `take_area`;";
	$r = $wxdb->query($sql);
	while( ($m=mysql_fetch_assoc($r))!=NULL ){
?>
        <tr height="32" align="center">
        	<td><?php echo $m['name'];?></td>
            <td><?php echo $m['sort'];?></td>
            <td></td>
        </tr>
<?php }?>
    </table>
    </div>
</div>

</body>
</html>