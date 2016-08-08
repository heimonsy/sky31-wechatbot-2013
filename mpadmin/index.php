<?php
session_start();
require_once("../include/common.php");
require_once("../lib/functions.php");
if( !isLogin() ) header("Location:login.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>微信管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="" />
<meta name="Description" content="" />
<STYLE type=text/css> 
*{ padding:0px; margin:0px;}
body {
	SCROLLBAR-FACE-COLOR: #4a6ea5; SCROLLBAR-HIGHLIGHT-COLOR: #ffffff; SCROLLBAR-SHADOW-COLOR: #ffffff;
	SCROLLBAR-3DLIGHT-COLOR: #9EB6D8; SCROLLBAR-ARROW-COLOR: #9EB6D8; SCROLLBAR-TRACK-COLOR: #9EB6D8;
	SCROLLBAR-DARKSHADOW-COLOR: #4a6ea5; SCROLLBAR-BASE-COLOR: #000000;
}
#rightframe{
	border-left:#000 solid 2px;
	border-top:#000 solid 2px;}
</STYLE>
</head>
<body style="margin: 0px; background-color:#9EB6D8;" scroll="no">
<table border="0" cellPadding="0" cellSpacing="0" height="100%" width="100%" style="table-layout: fixed;">
  <tr>
    <td width="185px"><iframe frameborder="0" id="leftframe" name="leftframe" src="left.php" scrolling="yes" style="height: 100%; visibility: inherit; width: 100%; z-index: 1;overflow: auto;"></iframe></td>
    <td><iframe frameborder="0" id="rightframe" name="rightframe" src="right.index.php" scrolling="yes" style="height: 100%; visibility: inherit; width: 100%; z-index: 1;overflow: auto;"></iframe></td>
  </tr>
</table>
</body>
</html>