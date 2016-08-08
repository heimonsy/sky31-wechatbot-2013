// JavaScript Document

$(document).ready(function(e) {
	msgList = jQuery.parseJSON(jQuery('#json-msgList').html());
	insertMsgList(msgList);
	setPageChanges();
	setMsgHover();
	//alert(123);
	//alert(typeof $(".list_msg:first") );
	//window.scrollbars.visible=true;
	$("#msg_all").css("height","0px");
	mhl = msgList.length*119+"px";
	$("#msg_all").animate({height:mhl}, "slow", function(){
		$("#msg_all").css("height","auto");
	});
	window.setInterval("haveNewMsg()", 2000);
});


function setMsgHover()
{	
	//document.getElementById();;
	
	$(".list_msg").mouseover(function(e) {
		var id = this.id.substr(3);
		//alert(msgList[id].tag);
		if( msgList[id].tag==0 )
			$(this).css("background-color", "#f1f1f1");
		else
			$(this).css("background-color", "#9dfb81");
			
    });
	$(".list_msg").mouseout(function(e) {
        //this.
		var id = this.id.substr(3);
		if( msgList[id].tag==0 )
			$(this).css("background-color", "#fff");
		else
			$(this).css("background-color", "#ccffbc");
    });
}

function insertMsgList(msgList)
{
	var h="";
	var l=msgList.length;
	h+="<div id=\"new_notice\"></div>";
	//$("#msg_all").height( l*118 );
	for( var i=0;i<l;i++ ){
		if( msgList[i].tag==0 )
			h+="<div class=\"list_msg\" id=ls_"+i+">";
		else 
			h+="<div class=\"list_msg\" id=ls_"+i+" style='background-color:#ccffbc;'>";
		h+="<div class=\"_img\"><img src=\"../data/pic/getpic.php?pid="+msgList[i].pid+"\" height=\"56\" width=\"56\" /></div>";
		h+="<div class=\"_cont\"><div class=\"_msgc\">";
		h+="<span class=\"_name\">"+msgList[i].nname+"：</span>";
		h+= msgList[i].cnt;
		h+="</div></div>";
		
		h+="<div class=\"_time\">"+msgList[i].time+"</div>";
		h+="<div class=\"_opt\" >";
		if( msgList[i].tag==0 )
			h+="<a href=\"#\" onclick=\"return setTag( this, "+i+" ,1);\" ><img src=\"images/nc.png\" height=\"28\" width=\"28\" />";
		else
			h+="<a href=\"#\" onclick=\"return setTag( this, "+i+", 0);\" ><img src=\"images/c.png\" height=\"28\" width=\"28\" />";
		h+="</a></div>";
		h+="</div>";
	}
	$("#msg_all").html( h );
	
}

function setPageChanges()
{
	var h =""
	if( currentPage<=1 )
		h+="上一页";
	else
		h+="<a href=\"#top\" class=\"pre_page\" onclick=\"getPrePage()\">上一页</a>";
	
	h+="&nbsp;&nbsp;&nbsp;"+currentPage+" / "+sumPages+"&nbsp;&nbsp;&nbsp;";

	if( currentPage>=sumPages )
		h+="下一页";
	else
		h+="<a href=\"#top\" class=\"next_page\" onclick=\"getNextPage()\">下一页</a>";
		
	$(".pg_ud").html(h);
}

function getPrePage()
{
	$("#msg_all").html( "<div class=\"jdt\"><img src=\"images/13221820.gif\" height=\"32\" width=\"32\" /></div>" );
	$.getJSON("msgservice.php?method=prep&cp="+currentPage+"&sid="+sid+"&tag="+tag,function(data){
        if( data=="" )  return ;
		else{
			///alert(123);
			msgList=data;
			currentPage--;
			sumNums=data[0].sum;
			sumPages = Math.ceil(sumNums/pageSize);
			//alert(currentPage);
			setPageChanges();
			insertMsgList( data );
			//window.alert(123);
			setMsgHover();
			$("#msg_all").css("height","0px");
			mhl = msgList.length*119+"px";
			$("#msg_all").animate({height:mhl}, "slow", function(){
				$("#msg_all").css("height","auto");
			});
			//alert(123);
			//alert(typeof $(".list_msg:first") );
			//$(".list_msg:first").animate({height:"118px"});
		}
    });
}

function getNextPage()
{
	$("#msg_all").html( "<div class=\"jdt\"><img src=\"images/13221820.gif\" height=\"32\" width=\"32\" /></div>" );
	$.getJSON("msgservice.php?method=next&cp="+currentPage+"&sid="+sid+"&tag="+tag,function(data){
        if( data=="" )  return ;
		else{
			msgList=data;
			currentPage++;
			sumNums=data[0].sum;
			sumPages = Math.ceil(sumNums/pageSize);
			//alert(currentPage);
			setPageChanges();
			insertMsgList( data );
			setMsgHover();
			$("#msg_all").css("height","0px");
			mhl = msgList.length*119+"px";
			$("#msg_all").animate({height:mhl}, "slow", function(){
				$("#msg_all").css("height","auto");
			});
		}
    });
}

function haveNewMsg(){
	//document.write("sdfsdf");

	$.getJSON("msgservice.php?method=newnums&cp="+currentPage+"&sid="+sid+"&tag="+tag+"&lastid="+lastid,function(data){
        if(data=='0')  return ;
		else if( data.nums!=0) {
			if( tag!=1 ){
				var obj = $("#new_notice");
				str = "<a href=\"#\" onclick=\"getNewMsg(); return false;\" >有"+data.nums+"条新消息</a>";
				obj.html( str );
			}else{
				var obj = $("#all_li");
				obj.html( "<a href=\"subview.php?sid="+sid+"&tag=all\">全部消息</a>&nbsp;<span style=\"color:#F00; font-weight:bold;\">new</span>" );
			}
		}
    });
	//window.setTimeout("haveNewMsg", 3000);
}

function getNewMsg()
{
	$("#msg_all").html( "<div class=\"jdt\"><img src=\"images/13221820.gif\" height=\"32\" width=\"32\" /></div>" );
	$.getJSON("msgservice.php?method=gnew&sid="+sid+"&tag="+tag,function(data){
        if( data=="" )  return ;
		else{
			msgList=data;
			currentPage=1;
			sumNums=data[0].sum;
			lastid = data[0].lastid;
			sumPages = Math.ceil(sumNums/pageSize);
			setPageChanges();
			insertMsgList( data );
			setMsgHover();
			$("#msg_all").css("height","0px");
			mhl = msgList.length*119+"px";
			$("#msg_all").animate({height:mhl}, "slow", function(){
				$("#msg_all").css("height","auto");
			});
		}
    });
}

function setTag( e, i , sTag)
{
	$.getJSON("msgservice.php?method=set&cp="+currentPage+"&sid="+sid+"&tag="+tag+"&stag="+sTag+"&rsid="+msgList[i].rsid,function(data){
        if(data=='0')  return ;
		if( data.res==1 ){
			msgList[i].tag=sTag;
			if( sTag==1 ){
				$("#ls_"+i).css( "background-color" , "#ccffbc");
				$(e).attr("onclick","return setTag( this, "+i+" ,0);");
				//alert( $(this).html() );
				$(e).html( "<img src=\"images/c.png\" height=\"28\" width=\"28\" />" );
			}
			else{
				$("#ls_"+i).css( "background-color" , "#FFF");
				//alert( $(e).html() );
				$(e).attr("onclick","return setTag( this,"+i+" ,1);");
				$(e).html( "<img src=\"images/nc.png\" height=\"28\" width=\"28\" />" );
			}
			
		}
		if( tag!='all' ){
			//alert($(e).parent("._opt").parent(".list_msg").attr("id"));
			$(e).parent("._opt").parent(".list_msg").hide("normal", function(){
				var par = $(this).parent("#msg_all");
				$(this).remove();
				if(typeof data.next.cnt!="undefined" ){
					msgList.splice(i,1);
					msgList.push(data.next);
					insertMsgListHide(msgList);
					//par.children(".list_msg:last").css( "display", "inherit" );
					
					par.children(".list_msg:last").animate({height:"118px"}, function(){
						sumNums--;
						sumPages = Math.ceil(sumNums/pageSize);
						setPageChanges();
						//insertMsgList( msgList );
						setMsgHover();
					});
					
					//alert( msgList[1].cnt );

					/*
					msgList.push( data.next );
					sumNums--;
					sumPages = Math.ceil(sumNums/pageSize);
					setPageChanges();
					insertMsgList( msgList );
					setMsgHover();
					*/
				}
			});

		}
    });
	
	return false;
}


function insertMsgListHide(msgList)
{
	var h="";
	var l=msgList.length;
	h+="<div id=\"new_notice\"></div>";
	for( var i=0;i<l;i++ ){
		if( msgList[i].tag==0 )
			if( i!=l-1 ){
				//alert(i);
				h+="<div class=\"list_msg\" id=ls_"+i+">";
			}
			else
				h+="<div class=\"list_msg\" id=ls_"+i+" style=\"overflow:hidden; height:0px;\">";
		else 
			if( i!=l-1 )
				h+="<div class=\"list_msg\" id=ls_"+i+" style='background-color:#ccffbc;' >";
			else
				h+="<div class=\"list_msg\" id=ls_"+i+" style='background-color:#ccffbc; overflow:hidden; height:0px;' >";
			
		h+="<div class=\"_img\"><img src=\"../data/pic/getpic.php?pid="+msgList[i].pid+"\" height=\"56\" width=\"56\" /></div>";
		h+="<div class=\"_cont\"><div class=\"_msgc\">";
		h+="<span class=\"_name\">"+msgList[i].nname+"：</span>";
		h+= msgList[i].cnt;
		h+="</div></div>";
		
		h+="<div class=\"_time\">"+msgList[i].time+"</div>";
		h+="<div class=\"_opt\" >";
		if( msgList[i].tag==0 )
			h+="<a href=\"#\" onclick=\"return setTag( this, "+i+" ,1);\" ><img src=\"images/nc.png\" height=\"28\" width=\"28\" />";
		else
			h+="<a href=\"#\" onclick=\"return setTag( this, "+i+", 0);\" ><img src=\"images/c.png\" height=\"28\" width=\"28\" />";
		h+="</a></div>";
		h+="</div>";
	}
	$("#msg_all").html( h );
}
