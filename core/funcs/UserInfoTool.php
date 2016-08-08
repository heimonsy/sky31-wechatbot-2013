<?php


class UserInfoTool
{
	function __construct($mid) {
		
	}
	
	
	
	public static function getUserInfo($cont, $time, $token, $ch) {
		//echo "keyword=".urlencode($cont)."&count=100&frommsgid=10000000&token=".$token."&ajax=1";
		
		//curl_setopt($ch, CURLOPT_POSTFIELDS, "keyword=".urlencode($cont)."&count=100&frommsgid=10000000&token=".$token."&ajax=1");
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/message?t=message/list&action=search&keyword=".urlencode($cont)."&count=1000&token=".$token."&lang=zh_CN");
		$str = curl_exec($ch);
		$pattern="/list \072 \((.+)\)\056msg_item/";
		preg_match($pattern, $str, $matchs);
		
		$json = json_decode($matchs[1]);
		$items = $json->msg_item;
		$tag = false;
		$fid = false;
		foreach ($items as $v) {
			if($v->date_time == $time) {
				if($tag == false) {
					$fid = $v->fakeid;
					$tag = true;
				} else if($fid == $v->fakeid)
					continue;
				else
					throw new OtherException("含有多个时间", "");
			}
					
		}
		if($tag) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, array("fakeid"=>$fid, "lang"=>"zh_CN", "t"=>"ajax-getcontactinfo", "token"=>$token));
			curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/getcontactinfo");
			$json = json_decode(curl_exec($ch));
			if($json == NULL)
				throw new OtherException("用户信息json解释失败。", "");
			$nickName = $json->NickName;
			$username = $json->Username;
			
			return array("fakeid"=>$fid, "nickName"=>addslashes($nickName), "username"=>$username);
			
		} else 
			throw new OtherException("消息没有检索到,:".$cont, "");
		//echo "\n\n\n";
		//var_dump($json);
		return false;
	}
	
	
	public static function loginToWxMp($username, $pw)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR,  "");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "");
		curl_setopt($ch, CURLOPT_REFERER,    "https://mp.weixin.qq.com/");
		curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/");
		//四秒钟，因为微信只能支持5秒响应时间，超过5秒则无反应
		//curl_setopt($ch, CURLOPT_TIMEOUT,    4);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POST,    true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('username'=>$username,'pwd'=>$pw, 'f'=>'json' , 'imgcode'=>''));
		curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN");
		
		$str = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE)."";
		if($code[0] != 2)
			throw new OtherException("无法登录mp.weixin,httpCode:".$code, "");
		if(!preg_match("/token=(.+?)\"/i", $str, $matchs))
			throw new OtherException("无法登录，可能是账号密码错误", "");
		$token = $matchs[1]; 
		return array( "ch"=>$ch, "token"=>$token );
	}
	
	public static function storeImg($fakeid, $uid, $token, $ch) {
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/getheadimg?token={$token}&fakeid={$fakeid}");
		$fcont = curl_exec($ch);
		if($fcont == NULL)
			throw new OtherException("获取头像", "");
		$path = self::getImgPath($uid);
		file_put_contents($path, $fcont);
		return true;
	}
	
	public static function getImgPath($uid) {
		$path = USER_DATA_PATH."img_uh/".$uid.".jpg";
		return $path;
	}
	
	public static function getHeadPic($uid) {
		return WX_USER_URL."data/img_uh/".$uid.".jpg";
	}
}
