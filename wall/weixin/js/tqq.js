var msgList=[];
var pm=0;
var th="";
var th1="";
var interval = "";
var userlist = [];
var ctimes=0;

$(document).ready(function(e) {
	
	msgList = jQuery.parseJSON( $("#json-msgList").html() );
	c();
	interval=window.setInterval("c()",8000);
});

function change()
{
	window.clearInterval(interval);
	var str1 = "<div class=\"jiang\"> <div id=\"cha\"><a href=\"#\" onclick=\"return cha()\"></a></div><div id=\"u_div\"><div id=\"jiang_pg\"><img width=\"140\" height=\"140\" src=\"weixin/images/1.gif\"></div> <div id=\"user_info\">微信号：<br>昵称：</div><div class=\"u_inf\">?????<br/>?????</div></div><div class=\"butt\"><a onclick=\"return cho()\" href=\"#\"><img width=\"134\" height=\"50\" src=\"weixin/images/start.png\"></a></div></div>";
 
	th1 = $("#weixin").html();
	$("#weixin").html(str1);

}

function cho()
{
	//userList = jQuery.parseJSON( $("#json-msgList").html() );
	$.getJSON("msgservice.php?method=cho&sid="+sid+"&tag=1", function(data){
		if( (typeof data.msgs)=='undefined' )
			return ;
		chnum = data.nums;
		userList = data.msgs;
		fint = window.setInterval("fff()",150);
	});

	
	return false;
}

var tf=0;
var upi=0;
var fint=null;
var chnum=-1;
function fff()
{
	tf++;
	str = "<div id=\"jiang_pg\"><img width=\"140\" height=\"140\" src=\"../data/pic/getpic.php?pid="+userList[upi].pid+"\"></div> <div id=\"user_info\">微信号：<br>昵称：</div><div class=\"u_inf\">"+userList[upi].wxn+"<br>"+userList[upi].nname+"</div>";
	
	
	$("#u_div").animate({paddingTop:"160px",opacity:0},50,function(){
		$("#u_div").html(str);
		$("#u_div").css("padding-top","40px");
		$("#u_div").animate({paddingTop:"100px",opacity:1}, 50, function(){
			if( tf>userList.length && upi==chnum ) {
				tf=0;
				window.clearInterval(fint);
				return ;
			}
			upi++;
			if( upi>=userList.length ) upi=0;
		});
	});
}

function pause(s){
	if( s==null ) return ;
	window.setTimeout("pause(null)", s);
}

function cha(){
	$("#weixin").html(th1);
	interval = window.setInterval("c()",8000);
	return false;
}

function setMsgContent(msgList, start)
{
	var l=0, str="";
	while( l<3){
		/*
		str+="<li> <div class=\"img\"><img src=\"../data/pic/getpic.php?pid="+msgList[start].pid+"\" width=\"140\" height=\"140\"/></div>";
		str+="<div class=\"msg\"> <p><b>"+msgList[start].nname+"</b>&nbsp;&nbsp;&nbsp;"+msgList[start].cnt+"</p>";
		str+="<p class=\"time\">发布于<span>"+msgList[start].time+"</span></p></div></li>";
		*/
		str+="<div class=\"msgs\"><div class=\"smsg\"><div class=\"img\"><img src=\"../data/pic/getpic.php?pid="+msgList[start].pid+"\" width=\"140\" height=\"140\"/></div>";
		if( msgList[start].cnt.length+msgList[start].nname.length>74 ) msgList[start].cnt=msgList[start].cnt.substring(0,70-msgList[start].name.length);
		str+="<div class=\"msg\"><b>"+msgList[start].nname+"</b>&nbsp;&nbsp;"+msgList[start].cnt;
		str+="<span class=\"time\"><font style=\"font-weight:bold; color:#333;\">发布于</font> "+msgList[start].time+"</span>";
		str+="</div></div>";
		start++;
		if(start>=msgList.length) 
			start=0;	  
		l++;
	}
	$(".msgs:first").html(str);
	return start;
}

function getMsg()
{
	window.clearInterval(interval);
	$.getJSON("msgservice.php?method=wall&sid="+sid+"&tag=1&lid="+lastid, function(data){
		if( (typeof data.msgs)=='undefined' ) return ;
		var l =data.msgs.length, ml=msgList.length;
		for(var i=l-1;i>=0;i--){
			if( ml<15){
				msgList.unshift( data.msgs[i] )
				ml++;
			}else{
				msgList.unshift( data.msgs[i] );
				msgList.pop();
			}
			
		}
		//msgList = data.msgs;
		//;
		lastid=msgList[0].rsid;
		pm+=l;
		pm%=ml;
		interval=window.setInterval("c()",8000);
	});
}

function c()
{
	ctimes++;
	var h=$(".msgs:first").html();
	$(".msgs:first").animate({opacity:0,paddingTop:"400px"},"slow",function(){
		pm=setMsgContent(msgList,pm);
		$(".msgs:first").css("padding-top","0px");
		$(".msgs:first").css("margin-top","-430px");
		//$(".msgs:first").css("height","0px");
		//$(".msgs:first").animate({height:"430px",opacity:1},"slow");
		$(".msgs:first").animate({opacity:1,marginTop:"0px"},"slow");
	});
	
	if( ctimes==5 ){
		pm=0;
		ctimes=0;
		getMsg();
	}
}

function backto()
{
	$("#weixin").html(th);
	interval = window.setInterval("c()",8000);
	return false;
}

function showEw()
{
	th=$("#weixin").html();
	window.clearInterval(interval);
	str = "<div class=\"er_img\"><a href=\"#\" onclick=\"backto()\"><img src=\"weixin/images/erwei.jpg\" /></a><div class=\"wxhhh\">微信号：isky31</div></div>"
	$("#weixin").html( str );
	return false;
}