<?php
class OtherFuncs
{
	/**
	 * 获取天气信息
	 * @param $strMsg $user
	 * @return boolean
	 */
	public static function getWeather()
	{
		$time = time(); 

		//$wxdb = MyDB::getWxdb();

// 		$sql = "select `json` from `weather_today`";
// 		$r = $wxdb -> query($sql);
// 		$r = mysql_fetch_assoc($r);
// 		$r = json_decode($r['json']);
		$mon = date("n", $time);
		$day = date("j", $time);
		return array("name"=>$mon."月".$day."日天气","fileUrl"=>ROOT_URL."mpadmin/uploads/today.mp3?time=".time()."mon=".$mon."day=".$day );
	}
	
	/**
	 * 树洞
	 * @param $strMsg $user
	 * @return boolean
	 */
	public static function throwToTreeHole( $strMsg, $user )
	{
		$wxdb = MyDB::getWxdb();
		
		$time = time();
		$sql = "insert into `trho_msg` (`id`,`msg_content`,`tag_show`,`platform`,`from_user_id`,`time` ) values(null, '{$strMsg}', true,'weixin' ,'{$user->userId}', '{$time}' )";
		$r = $wxdb->query($sql);
		return $r;
	}
	
	
	/**
	 *
	 * 翻译,通过有道词典的api
	 * @param $content 
	 * @return $res;
	 */
	public static function translation($content)
	{
		$content = urlencode($content);
		$urlStr="http://fanyi.youdao.com/openapi.do?keyfrom=SanYiXiaoYuan&key=172730072&type=data&doctype=json&version=1.1&q=".$content;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlStr);
		//设置curl_exec不是直接输出
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		$returnStr = curl_exec($ch);
		//$returnStr ='{"translation":["为什么"],"basic":{"phonetic":"hwai","explains":["int. 哎呀！什么？","adv. 为什么"]},"query":"why","errorCode":0,"web":[{"value":["为什么","如何","为什麽","目的"],"key":"WHY"},{"value":["李宇春","何必有我","终于能够站在梦想舞台","为什麽是我"],"key":"Why Me"},{"value":["为什么","爱像过客不闻不问","爱总让你一点不剩","像过客不闻不问"],"key":"And why"},{"value":["何需等待","何需守候","迫不及待","唱片名"],"key":"Why Wait"},{"value":["为什么发白","为什麽发白"],"key":"Why Lighe"},{"value":["嗨"],"key":"why interj"},{"value":["他为什么喊"],"key":"Why Howling"},{"value":["为什么离开","什么离开"],"key":"Why Leaving"},{"value":["纯真年代"],"key":"Wonder Why"},{"value":["阅读魔法"],"key":"Super Why"}]}';
		$resultObj = json_decode($returnStr);
		$res="";
		if( $resultObj->errorCode==0 )
		{
			@$res="查询：".$resultObj->query."\n";
			if( isset($resultObj->basic->phonetic) ) $res.="读音：[ ".$resultObj->basic->phonetic." ]\n";
			@$res.="翻译：".implode(';', $resultObj->translation );
			if( @trim($resultObj->basic->explains)!="" )
				@$res.="\n词典：\n  ".implode("\n  ", $resultObj->basic->explains);
			if( @trim($resultObj->web[0]->value)!="" )
				@$res.="\n网络释义：\n  ".implode("; ", $resultObj->web[0]->value);
	
		}else $res="翻译出错";
	
		return $res;
	}
}