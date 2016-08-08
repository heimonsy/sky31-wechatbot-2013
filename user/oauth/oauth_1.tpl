<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>三翼校园</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" /> 
    <link rel="stylesheet" href="css/index.css" />
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
	height:auto;
	margin-left:10px; margin-right:10px; padding-top:12px; padding-bottom:12px;
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

</style>
</head>

<body>
	<div id="header">绑定您的学号
    </div>
    <div id="content">
    	<div id="notice"><?php echo $notice; ?> 教务管理系统初始密码为 666666</div>
        <div id="dfom">
        	<form action="" method="post">

            	<span class="ftit">学号：</span><br/>
            	<input class="finp" type="text" name="stuNum" /><br/>
				<span class="ftit">密码：</span><br/>
            	<input class="finp" type="password" name="pw" /><br/>
                <!--<span class="ftit">昵称：</span><br/>
            	<input class="finp" type="text" name="nickName" /><br/>-->
                <input class="fsub" type="submit" name="submit" value="提交" />
            </form>
        </div>
    </div>
    <div id="footer">三翼校园 Copyright © 2004-2013
    </div>
</body>
</html>
