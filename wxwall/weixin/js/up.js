var control_v=true; //控制暂停和开始
var cho_control_v=false;
var new_msg = new Array();
var pml = 0;
var show_count=0;
var interval;
var th,th1;//
var user_list=Array();

$(document).ready(function(e) {
	msg_list = jQuery.parseJSON( $("#json-msgList").html() );
	showNext(); //显示最初的几条msg
	interval=window.setInterval("showNext()", 8000);
});


/*******************
 *
 * 主要播放相关的地方
 *
 *******************/
function control(c){
	//2:自动，1：设置开始，0：设置暂停
	if(c==2){
		if(control_v==true) control(0);
		else control(1);
		
	}else if(c==1){
		//进入播放模式
		control_v=true;
		$("#control_a").html("||暂停");
		showNext();
		interval=window.setInterval("showNext()", 8000);
	}else{
		//进入暂停模式
		control_v=false;
		$("#control_a").html(">>播放");
		window.clearInterval(interval);
	}
}

function showNext(){
	if(!control_v) return ;
	var h=$(".msgs:first").html();
	$(".msgs:first").animate({opacity:0,paddingTop:"400px"},"slow",function(){
		setMsgContent();
		$(".msgs:first").css("padding-top","0px");
		$(".msgs:first").css("margin-top","-430px");
		//$(".msgs:first").css("height","0px");
		//$(".msgs:first").animate({height:"430px",opacity:1},"slow");
		$(".msgs:first").animate({opacity:1,marginTop:"0px"},"slow");
	});
}


//插入内容
function setMsgContent(msgList, start)
{
	var l=0, str="", msg_array=new Array();
	if(new_msg.length!=0){
		var i=0, ml=msg_list.length-1;
		for(;i<new_msg.length&&i<3;i++){
			msg_array.push(new_msg[0]);
			msg_list.unshift(new_msg[0]);
			new_msg.shift();
		}
		for(t=i;t<3;t++)
			msg_array.push(msg_list[t]);
		
	}else{
		for(i=0;i<3;i++)
			msg_array.push(msg_list[(i+pml)%msg_list.length]);
		//指向正常播放的数字下标
		pml=(pml+3)%msg_list.length;
	}
	
	while( l<3){
		str+="<div class=\"msgs\"><div class=\"smsg\"><div class=\"img\"><img src=\""+msg_array[l].purl+"\" width=\"140\" height=\"140\"/></div>";
		//if( msg_array[l].cnt.length+msgList[l].nname.length>74 ) msg_array[l].cnt=msg_array[l].cnt.substring(0,70-msg_array[l].name.length);
		str+="<div class=\"msg\"><b>"+msg_array[l].nname+"</b>&nbsp;&nbsp;"+msg_array[l].cnt;
		str+="<span class=\"time\"><font style=\"font-weight:bold; color:#333;\">发布于</font> "+msg_array[l].time+"</span>";
		str+="</div></div>";
		l++;
	}
	show_count++;
	if(show_count==2){
		show_count=0;
		getNewMsg();
	}
	$(".msgs:first").html(str);
}


function getNewMsg()
{
	$.getJSON("msgservice.php?method=wall&sid="+sid+"&tag=1&lid="+lastid, function(data){
		if( (typeof data.msgs)=='undefined' ) return ;
		else{
			var l =data.msgs.length;
			for(var i=0;i<l;i++){
				new_msg.push(data.msgs[i]);
			}
			if(l!=0)lastid=data.msgs[l-1].rsid;
		}
	});
}

/******************
 *
 * 抽奖等其他功能部分
 *
 ******************/
 function backto()
{
	$("#weixin").html(th);
	control(1);
	return false;
}
function showEw()
{
	th=$("#weixin").html();
	control(0);
	str = "<div class=\"er_img\"><a href=\"#\" onclick=\"backto()\" title='点击回到播放页面'><img src=\"weixin/images/erwei.jpg\" /></a><div class=\"wxhhh\">微信号：isky31</div></div>"
	$("#weixin").html( str );
	return false;
}


function openChou(){
	control(0);
	var str1 = "<div class=\"jiang\"> <div id=\"cha\"><a href=\"#\" onclick=\"return closeChou()\"></a></div><div class='load_info'>正在加载用户信息<br/><img height='50' width='50' src='images/load.gif' /></div></div>";
	//"<a onclick=\"return cho()\" href=\"#\"><img width=\"134\" height=\"50\" src=\"weixin/images/start.png\"></a>"
	th1 = $("#weixin").html();
	$("#weixin").html(str1);
	getAllUser();
	return false;
}

function getAllUser(){
	$.getJSON("msgservice.php?method=userlist&sid="+sid+"&tag=1", function(data){
		if( (typeof data.msgs)=='undefined' ){
			$("#weixin").html("<div class=\"jiang\"> <div id=\"cha\"><a href=\"#\" onclick=\"return closeChou()\"></a></div><div class='load_info'><span style='color:red;'>无法连接网络</span><br/></div></div>");
			return ;
		}else {
			user_list = data.msgs;
			$("#weixin").html("<div class=\"jiang\"> <div id=\"cha\"><a href=\"#\" onclick=\"return closeChou()\"></a></div><div id=\"u_div\"><div id=\"jiang_pg\"><img width=\"140\" height=\"140\" src=\"weixin/images/1.gif\"></div> <div id=\"user_info\">微信号：<br>昵称：</div><div class=\"u_inf\">?????<br/>?????</div></div><div class=\"butt\"><a onclick=\"return cho_control(2)\" href=\"#\"><img width=\"134\" height=\"50\" src=\"images/start.png\"></a></div></div>");
		}
	});

	
	return false;
}

function closeChou(){
	//$(this).click(openChou());
	$("#weixin").html(th1);
	control(1);
	//window.top.location.reload();
	return false;
}

function cho_control(c){
	//2:自动，1：设置开始，0：设置暂停
	if(c==2){
		if(cho_control_v==true) cho_control(0);
		else cho_control(1);
	}else if(c==1){
		//进入翻滚模式
		cho_control_v=true;
		$(".butt").html("<a onclick=\"return cho_control(2)\" href=\"#\"><img width=\"134\" height=\"50\" src=\"images/stop.png\"></a>");
		fanGun();
	}else{
		//停止翻滚停模式
		cho_control_v=false;
		$(".butt").html("<a onclick=\"return cho_control(2)\" href=\"#\"><img width=\"134\" height=\"50\" src=\"images/start.png\"></a>");
	}
}
//指向user_list下标，用来循环
var uli = 0;
function fanGun(){
	if(cho_control_v==false) return ;
	
	str = "<div id=\"jiang_pg\"><img width=\"140\" height=\"140\" src=\""+user_list[uli].purl+"\"></div> <div id=\"user_info\">微信号：<br>昵称：</div><div class=\"u_inf\">"+user_list[uli].wxn+"<br>"+user_list[uli].nname+"</div>";
	
	$("#u_div").animate({paddingTop:"160px",opacity:0},50,function(){
		$("#u_div").html(str);
		$("#u_div").css("padding-top","40px");
		$("#u_div").animate({paddingTop:"100px",opacity:1}, 50, function(){
			uli++;
			if(uli>=user_list.length ) uli=0;
			window.setTimeout("fanGun()",100);
		});
	});
}

var last_rand=-1
function getRand(m){
	n=Math.random();
	while(n<=m) n*=10;
	t = Math.floor(n/10);
	if(last_rand==t) last_rand=getRand(m);
	else last_rand=t;
	return last_rand;
}