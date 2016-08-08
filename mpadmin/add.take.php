<?php
session_start();
require_once("../include/common.php");
require_once("../lib/functions.php");
if( !isLogin() ) header("Location:login.php");

$wxdb = MyDB::getWxdb();
if( isset($_POST['submit']) )
{
	$name = $_POST['name'];
	$tel = $_POST['tel'];
	$adr = $_POST['adr'];
	$areaId = $_POST['areaId'];
	$sort = $_POST['sort'];

	$sql = "insert into `takeaway` (`id`,`name`,`tel`,`adr`,`aid`,`sort` ) values(null,'{$name}','{$tel}','{$adr}','{$areaId}','{$sort}')";
	if($wxdb->query($sql)) 
		alert("添加成功","add.take.php");
	else 
		alert("添加失败".mysql_error(),"add.teak.php");
    	
}

function getAreaNameById($id)
{
	global $wxdb;
	$sql =  "select `name` from `take_area` where `id`='{$id}'";
	$r =$wxdb->query($sql);
	$r = mysql_fetch_assoc($r);
	return $r['name'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/my.js" type="text/javascript"></script>
<style>
body{ background-color:#9eb6d8;}
.path { height:34px; width:auto; margin-left:20px; margin-right:20px; border-bottom:#eee 1px dashed; line-height:34px; font-size:14px; text-indent:5px;}
.content {height:auto; width:600px; margin:0 auto; line-height:28px;}
a { color:#00F; text-decoration:none;}
a:hover{ text-decoration:underline;}
.add_content { width:500px; height:auto; margin:0 auto; border:#fff solid 1px;}
.add_content .title { height:32px; text-align:center; line-height:32px; font-size:16px;}
.filelist { width:auto; height:auto; padding-left:14px; padding-right:14px; margin-top:16px;}
.filelist .title{ height:32px; text-align:center; width:auto; line-height:32px; font-size:16px; border-bottom:#FFF solid 2px;}
.filelist .area{ height:28px; line-height:28px; text-indent:20px; border-bottom:#FFF solid 2px;}

</style>
</head>
<body>
<div id="h_padding" style="padding-top:30px; width:100%; height:1px;"></div>
<div class="path">当前位置：<a href="right.index.php">后台首页</a> >> <a href="#">添加外卖</a></div>

<div class="add_content">
	<div class="title">添加外卖</div>
	<div class="df">
    	<table cellpadding="0" cellspacing="2" border="0" width="100%">
        <form method="post" enctype="multipart/form-data">
        	<tr >
            	<td align="right" width="25%" height="35">店名：</td>
                <td><input type="text" name="name"/> 如：金翰林</td>
            </tr>
            <tr >
            	<td align="right" width="25%" height="35">区域：</td>
                <td>
                	<select name="areaId" >
                    <?php 
					$sql="select * from `take_area`;";
					$r = $wxdb->query($sql);
					while( ($m=mysql_fetch_assoc($r))!=NULL ){
					?>
                    	<option value="<?php echo $m['id']; ?>" ><?php echo $m['name']; ?></option>
                    <?php }?>
                    </select>
                    
                </td>
            </tr>
            <tr>
            	<td align="right" height="35">详细地址：</td>
                <td><input type="text" name="adr" value=""/> </td>
            </tr>
            <tr>
            	<td align="right" height="35">电话：</td>
                <td><input type="text" name="tel" value=""/> 统一用空格分开</td>
            </tr>
            <tr>
            	<td align="right" height="35">排序：</td>
                <td><input type="text" name="sort" value="1"/> 数字</td>
            </tr>
            <tr>
            	<td align="right" height="35"></td>
                <td><input type="submit" value="提交" name="submit"/></td>
            </tr>
        </form>    
        </table>
    </div>
</div>

<div class="filelist">
	<div class="title">店家列表</div>
    <div class="area"><a href="#" id="area_all">全部</a>
     <?php 
		$sql="select * from `take_area`;";
		$r = $wxdb->query($sql);
		while( ($m=mysql_fetch_assoc($r))!=NULL ){
	?>
    	<a href="#" id="area_<?php echo $m['id'];?>" ><?php echo $m['name']; ?></a>
        
    <?php } ?>
    </div>
    <div class="c" id="d_list">
    	<table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr align="center">
        	<td width="15%" height="26">店名</td>
            <td width="10%">区域</td>
            <td width="40%">地址</td>
            <td width="20%">电话</td>
            <td width="5%">排序</td>
            <td width="10%">操作</td>
        </tr>
        
        <?php 
		$sql="select * from `takeaway`;";
		$r = $wxdb->query($sql);
		while( ($m=mysql_fetch_assoc($r))!=NULL ){
	?>
    <tr align="center">
        	<td width="15%" height="26"><?php echo $m['name']; ?></td>
            <td width="10%"><?php echo getAreaNameById($m['aid']); ?></td>
            <td width="40%"><?php echo $m['adr']; ?></td>
            <td width="20%"><?php echo $m['tel']; ?></td>
            <td width="5%"><?php echo $m['sort']; ?></td>
            <td width="10%"></td>
            </tr>
       <?php }?>
        
        </table>
    </div>
</div>

</body>
</html>