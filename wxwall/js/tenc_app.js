// JavaScript Document
/*
 * Heister
 *
 */

var t_show;
//允许的发表时间隔
var times=5000;
var intimes=false;
var t_times;

$(document).ready(function(){
	
	$(".listdetail:first").hide();
	

	//url:http://rc.qzone.qq.com/myhome/100624075
	
	$("#fxbutton").click(function(){
		//
		toShare(pt);
	});
	
	$("#tjbutton").click(function(){
		inviteFriends(pt);
	});
	
	window.setInterval("remainwordnum()",400);
	t_show = window.setInterval("shownew();",1000);
	
	$("#sendMsg").click(function(){
	});
	
	$("#textarea").keypress(function(event){
		var handle=document.getElementById("textarea");
		if(handle.value.length>=140) 
		 	handle.value=handle.value.substring(0, 140);//window.event.keyCode=0;
	});
	
	$("#textarea").focus(function(){
		$("#issuccess").hide("slow");
	})
	
	$("#sendMsg").click(function(){
		var content=document.getElementById("textarea").value;
		
		if(intimes)
		{
			showInfo("请勿频繁发表","#F00")
			return ;
		}
		
		if(content.length==0)
		{
			showInfo("内容不能为空","#F00")
			return ;
		}
		
		if(content.length>140) content=content.substring(0, 140);
		
		window.clearInterval(t_show);
		//上传数据
		$.ajax({
			type    : "POST",
			url     : "tenc_msg.php?method=liuyan&openid="+radio_openid+"&openkey="+"radio_openkey",
			data    : {neirong:content},
			success : function(data,st){
				if( data=='1' ) 
				{
					showInfo("发表成功","#0CF")
					document.getElementById("textarea").value="";
					shownew();
					t_times=window.setInterval("clearTimes();",times);
					intimes=true;
					
				}
				else if(data=='0')
				{
					showInfo("发表失败","#F00")
				}else if(data=='3'){
					showInfo("有高度敏感词","#F00")
				}
				t_show = window.setInterval("shownew();",1000);
			}
		});
	});
	
});


function getinfo(){
	
	var lastid=$(".messageid:first").val();
	$.getJSON('tenc_msg.php?method=getmsg&id='+lastid+'&pt=' + encodeURI(pingtai),function(data){
        if(data=='0')  return ;
		var str  = '<div class="listdetail">';
		str += '<input type="hidden" value="' + data.id + '" class="messageid" />';
		str += '<div class="userPic">';
		str += '<img src="'+ data.pic_url +'" height="50" width="50" /></div>';
		str += '<div class="msgBox">';
		str += '<font color="#999999">'+ data.from +' </font>';
		str += '<font color="#006a92"><strong> '+ data.name +'：&nbsp;</strong></font>   ';
		str += data.message;
		//var dt = makeTime(new Date( data.time*1000 ));
		str += '<font color="#999999">&nbsp;&nbsp;'+ data.time +'</font>';
		str += '</div></div>';   
		var pre=$("#lylist").html();
		$("#lylist").html(str+pre);
		$(".listdetail:first").hide();
    });
}

function shownew(){
	
	if($(".listdetail").size()>26) $(".listdetail:last").remove();
	
	$(".listdetail:first").slideDown(500,function(){
		getinfo();
	})
}

function showhide()
{
	if($(".listdetail").size()>10) $(".listdetail:last").remove();
	$(".listdetail:first").slideDown("slow");
}

function checkMsg()
{
	var handle = document.getElementById("textarea");
	if(handle.value.length<140)
	{
	}
}

function postMsg()
{
	
}

function remainwordnum()
{
	var handle=document.getElementById("textarea");
	var num=140-handle.value.length;
	if(num<0) $(".wordnum").html(0);
	else $(".wordnum").html(140-handle.value.length);
}

function clearTimes()
{
	window.clearInterval(t_times);
	intimes = false;
}

function showInfo(content,color)
{
	$("#issuccess").hide();
	$("#issuccess").css("color",color);
	$("#issuccess").html(content);
	$("#issuccess").show('slow');
}

function makeTime( dt )
{
	var str="";
	str +=(dt.getMonth()+1);
	str += "-";
	str += dt.getDate();
	str += "&nbsp;";
	str += (dt.getHours()+1);
	str += ":";
	str += (dt.getMinutes());
	return str;
}

function toShare(pf,tourl)
{	
	if(pf=='qzone'){
		fusion2.dialog.share({
				url   : "http://rc.qzone.qq.com/myhome/100624075",
				desc  :"四季电台，期待你的聆听~",
				summary  :"躲在某一时间，想念一段时光的掌纹。躲在某一地点，聆听四季的声音。三翼校园四季网络电台，期待你的聆听。NJ：小high，咖啦，曦林，黄小炜，CoCo，小宇，养乐多，默涵，少颖",
				title    :"四季电台",
				pics     :"http://qpic.cn/J99vS3WBn"
			});
	}
}

function inviteFriends(pf)
{
	if(pf != 'tapp')
	{
		fusion2.dialog.invite({
		 	msg :"邀请您一起收听四季电台:三翼校园四季网络电台，期待你的聆听",
		});
	}
	else
	{
		fusion2.dialog.invite({
		 	
		});
	}
}
