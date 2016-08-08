<?php
session_start();
require_once("../include/common.php");
require_once("../lib/functions.php");
if( !isLogin() ) header("Location:login.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style>
body{ background-color:#9eb6d8;}
</style>
</head>
<body>
<div id="h_padding" style="padding-top:50px; width:100%; height:1px;"></div>
<table border="1" bordercolor="#4a6ea5"cellPadding="0" cellSpacing="0" height="auto" width="100%">
	<tr >
   	<td height="30px" width="100%" colSpan=2 align="center">三翼校园微信管理系统</td>
   </tr>
  	<tr>
   	<td width="10%" height="30" align="center">作者：</td>
      <td width="90%" style="text-indent:10px">三翼技术研发部</td>
   </tr>
   <tr>
   	<td width="10%" height="30" align="center">联系方式：</td>
      <td width="90%" style="text-indent:10px">Email:250661062@qq.com</td>
   </tr>
</table>
<br />
</body>
</html>