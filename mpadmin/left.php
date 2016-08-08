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
<title>微信管理</title>
<style>

body{ background-color:#9eb6d8; position:relative;}

ul,li{ list-style-type:none; margin:0px; padding:0px; border:0 none;}

*{padding0px; margin:0px;}
#all{ width:auto; height:auto; margin:0px; padding:0px; position:relative;}
#mylogo{
	width:132px; height:60px; margin:10px auto 0px auto; font-size:16px; line-height:30px; text-align:center; color:#fff; font-weight:bold; overflow:hidden;}
#user{
	width:auto; height:auto; text-align:center; font-size:14px; font-weight:bolder; line-height:28px; color:#00F;}
#content{width:auto; height:auto; margin:10px 10px 10px 10px; border:#4a6ea5 solid 1px;}
#cpr{ 
	height:auto; width:auto;  border:#06F solid 1px; background-color:#FFF;
	margin:7px 7px 7px 7px; font-size:12px; line-height:20px;
}
.c_title{
	font-size:15px;text-align:center; line-height:24px; font-weight:bolder; color:#FFF; background-color:#4a6ea5;
	border:#fff solid 1px;
	}
.list { width:auto; height:auto; overflow:hidden;}
.list li{ height:25px; text-align:center; line-height:25px;background-color:#fff;}

.list a{ display:block; text-decoration:none; padding:2px; color:#145099; font-size:15px;}
.list a:hover{ background-color:#efefef; text-decoration:none;}
</style>
</head>

<body>
<div id="all">
  <div id="mylogo">三翼校园<br/>微信管理系统</div>
   <!--<div id="user"><span style="color:#F00;"></span>你好！<br /> 欢迎登陆！</div>-->
   <div id="content">
        <div class="c_title">今日湘大</div>
        <div class="list">
            <ul>
                <li><a href="today.php" target="rightframe">更新新闻</a></li>
                <!--
                <li><a href="add.news.php" target="rightframe">添加新闻</a></li>
                <li><a href="admin.news.php" target="rightframe">新闻管理</a></li>
                -->
            </ul>
        </div>
		<div class="c_title">乐活（暂定）</div>
		<div class="c_title">话题</div>
        <div class="list">
        <ul>
            <li><a href="subject.php" target="rightframe">话题定制</a></li>
        </ul>
        <div class="c_title">四季电台</div>
        <div class="list">
        <ul>
            <li><a href="add.weather.php" target="rightframe">天气预报</a></li>
         </ul>
        </div>
     
      <!--<div class="c_title">留言板管理</div>
      <div class="list">
      	<ul>
            <li><a href="admin.msg.php" target="rightframe">管理留言</a></li>
         </ul>
      </div>
      -->
      <div class="c_title">外卖</div>
        <div class="list">
        <ul>
         <li><a href="add.area.php" target="rightframe">地域信息管理</a></li>
            <li><a href="add.take.php" target="rightframe">店家信息管理</a></li>
         </ul>
        </div>
      <div class="c_title">树洞(暂定)</div>
      <div class="c_title">版权信息</div>
      <div id="cpr">
      	 版权所有：三翼工作室<br />
         设计制作：Heister<br />
         联系方式：250661052@qq.com<br />
         系统版本：V0.10
      </div>
   </div>

</div>
</body>
</html>