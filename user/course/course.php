<?php 
include_once '../../core/include/common.php';

if( isset($_GET['oa']) )
	$oa=$_GET['oa'];
else
	exit("ERROR");
	
$uid = Oauth::valid($oa);
if( !$uid ) exit("OA_ERROR!");




$sql = "select `uid`,`wek`,`cnm`,`kname`,`tname`,`place`,`week` from `stu_kb`  inner join `stu_course` ON `stu_kb`.`cid`=`stu_course`.`cid`  inner join `stu_room` on `stu_course`.`rid`=`stu_room`.`rid` where `uid`='$uid'";

$wxdb = MyDB::getWxdb();
$r = $wxdb->query($sql);

while( ($m=mysql_fetch_assoc($r))!=null ){
	$arr[ $m['wek'] ][ $m['cnm'] ][] = array( $m['kname'], $m['place'], $m['tname'], $m['week']);
}

//print_r($arr);
$bind = Oauth::haveBind($uid);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>三翼校园微信：课表查询</title>
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
<div id="header"><?php echo Encodes::decode($bind['name']);?> 的课表
</div>
<div style="height:32px; line-height:32px; text-align:center;">点击右上角分享给好友吧~</div>
<div id="content">
<?php

$num_to_week=array( "星期一","星期二","星期三","星期四","星期五","星期六","星期日" );

foreach( $arr as $wek =>$v ){
	
	echo "<div class=\"tit\">".$num_to_week[$wek]."</div>";
?>
	
    <div class="cnt">
	<?php 
	foreach( $v as $cnm => $vv ){
		echo "<b>".CourseExtends::$cnm_to_word[$cnm]."</b><br />";
		foreach( $vv as $vvv )
		foreach( $vvv as $vvvv )
			echo $vvvv."<br />";
		echo "<br />";
	}
	
	?></div>
<?php }?>

</div>
<div style="height:38px; line-height:38px; font-size:18px; padding-top:10px; padding-left:10px; padding-right:10px;">
微信搜索关注 <span style="color:#ff0000;">isky31</span> 或者 <span style="color:#ff0000;">湘潭大学三翼校园</span> 你也来查课表吧
</div>
<div style="height:2px; width:auto;"></div>
<br /><br /><br />
    <div id="footer">三翼校园 Copyright © 2004-2013
    </div>
</body>
</html>
