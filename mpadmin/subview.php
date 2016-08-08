<?php
session_start();
require_once("inc.php");
require_once("../include/common.php");
if( !isLogin() ) header("Location:login.php");

if( !isset( $_GET['sid'] ) || !is_numeric($_GET['sid']) )
	exit("<b>错误</>");
	
if( !isset( $_GET['tag'] ) )
	$tag='all';
else
	$tag=$_GET['tag'];

$sid = $_GET['sid'];
$currentPage = 1;

$r = getWord($sid);
if( $r===NULL ) exit("<b>sid错误</b>");
$word = $word = $r['word'];

//获取当前页的List
$msgList = getPageList($currentPage, $sid, $tag , ADMIN_PAGE_SIZE);
$lastid =  isset($msgList[0]['lastid']) ? $msgList[0]['lastid'] : 0 ;
$sumNums = isset($msgList[0]['sum']) ? $msgList[0]['sum'] : 0 ;
$sumPages = ceil($sumNums/ADMIN_PAGE_SIZE);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="css/s.css" />
<script type="text/javascript">
	var lastid = <?php echo $lastid; ?>;
	var currentPage = <?php echo $currentPage; ?>;
	var sumNums     = <?php echo $sumNums; ?>;
	var sumPages    = <?php echo $sumPages; ?>;
	var tag         = "<?php echo $tag; ?>";
	var sid         = <?php echo $sid; ?>;
	var pageSize    = <?php echo ADMIN_PAGE_SIZE; ?>;
</script>
<script type="text/javascript" src="js/jquery-1.7.2.min.js" ></script>

<script type="text/javascript" src="js/view.js" ></script>
<script id="json-msgList" type="json/text">
<?php echo json_encode($msgList) ?>
</script>
<title>话题管理</title>
</head>

<body>
<div class="nav_bg">
	<div class="nav">
    	<span class="l">三翼校园微信上墙管理平台</span>
        <span class="m">#<?php echo $word; ?>#</span>
    </div>
</div>
<div class="main_bg">
<div class="main">
    	<div class="title">用户消息管理&nbsp;&nbsp;<a href="wall.php?sid=<?php echo $sid;?>">进入微信墙界面</a></div>
        <div class="cnt">
            <div class="left">
            	<ul>
                	<li id="all_li" class="<?php if( $tag=='all' ) echo 'hover'; ?>" ><a href="subview.php?sid=<?php echo $sid;?>&tag=all">全部消息</a></li>
                    <li class="<?php if( $tag=='0' ) echo 'hover'; ?>" ><a href="subview.php?sid=<?php echo $sid;?>&tag=0">未通过</a></li>
                    <li class="<?php if( $tag=='1' ) echo 'hover'; ?>"><a href="subview.php?sid=<?php echo $sid;?>&tag=1">已通过</a></li>
                </ul>
            </div>
           
            <div class="right">
            	<div class="clear_b"></div>
            	<div class="r_t">消息列表</div>
                <div class="pg_ud"><a href="#top" class="pre_page">上一页</a>&nbsp;&nbsp;&nbsp;1 / <?php echo $sumPages; ?>&nbsp;&nbsp;&nbsp;<a href="#top" class="next_page">下一页</a></div>
                <div class="list_t" id="list_t">
                	<div class="msg">消息</div>
                    <div class="time">时间</div>
                    <div class="opt">通过</div>
                </div>
                
                <div id="msg_all">
                	<div class="jdt"><img src="images/13221820.gif" height="32" width="32" /></div>
                </div><!---msg_all--->
                <div class="pg_ud">&nbsp;&nbsp;&nbsp;1 / <?php echo $sumPages; ?><a href="#top" class="next_page">下一页</a></div>
                <div class="blank"></div>
       	</div>
    </div>
</div>

<div class="bottom_bg">
    <div class="bottom">
    	<div class="logo"><img src="images/logov7.png" /></div>
        <div class="cpy">Copyright &copy; 2004-2011 湘潭大学三翼工作室 All Right Reserved </div>
    </div>
</div>

</body>
</html>