<?php
//include "../include/common.php";

function getScore($uid){
	$wxdb = MyDB::getWxdb();
	if( Oauth::haveBind($uid ) ){
		 $bind = Oauth::haveBind($uid);
		 $user = Encodes::decode($bind['snum']);
		 $pw   = Encodes::decode($bind['pw']);
		 $ch = PostLogin::loginToStuM($user , $pw);
		 curl_setopt($ch, CURLOPT_URL, "http://202.197.224.134:8083/jwgl/cj/cj1_cjLiebiao.jsp?xq=0&xkjc=&type=null&xkdl2=&xh={$user}&bh=null");
		 $str = curl_exec($ch);
		 $str = iconv("GB2312","UTF-8",$str);
		 $str = str_replace("\n", "", $str);
		 $str = str_replace("\r", "", $str);
		 $str = str_replace(" ", "", $str);
		 $str = str_replace("&nbsp;", "", $str);
		 $str = str_replace("style=\"color:red\"", "", $str);
		 
		 $r = preg_match_all("/<tr>(.*?)<\/tr>/u", $str, $matchs);
		 $matchs = $matchs[1];
		 $str = "翼宝为你查询到的成绩：\n\n";
		 $count = count($matchs);
		 $pre = -1;
		 for($i = $count-1; $i >=0; $i--){
			preg_match_all("/<td>(.*?)<\/td>/u", $matchs[$i], $m);
			$m = $m[1];
			if( $pre!=-1 && ($pre!=intval($m[6]) || $m[6]=="" ) ) break;
			else $pre = intval($m[6]);
			
			$str.=($m[0]."【".substr($m[1],0,6)."】\n平时：".$m[3]." 考试：".$m[4]." 期评：".$m[5]."\n------------\n");
		 }
		 $oauth = Oauth::getOauth($uid);
		 return $str."<a href=\"http://wx.sky31.com/data/student/students.php?oa=$oauth\">点此查看排名</a>";
	}else{
		$oauth = Oauth::getOauth($uid);
		$url = OAUTH_URL."?oa=".$oauth;
		return "您还没有绑定个人信息。\n点击：".$url." 进行绑定。\n绑定成功后回复 成绩 可以查询成绩哦。;";
	}
}

//echo getScore(6);
