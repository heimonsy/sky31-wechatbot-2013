<?php


class UserInfoTool
{
	function __construct($mid) {
		
	}
	
	
	public static function getUserInfo($cont, $time, $token, $ch) {
		//抓取消息列表
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/message?t=message/list&count=40&day=7&token={$token}&lang=zh_CN");
		$str = curl_exec($ch);
		$pattern='/list : \050(.*?)\051\056msg_item/';
		preg_match($pattern, $str, $matchs);
		//var_dump($matchs[1]);
		$data  = json_decode($matchs[1]);
		$items = $data->msg_item;
		
		//筛选消息列表
		$tag = false;
		$fid = false;
		foreach ($items as $v) {
			if($v->date_time == $time && $v->content==$cont ) {
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
			//获取用户信息
			curl_setopt($ch, CURLOPT_POSTFIELDS, array("random"=>self::my_rand(),"ajax"=>1,"fakeid"=>$fid, "lang"=>"zh_CN", "t"=>"ajax-getcontactinfo", "token"=>$token));
			curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/getcontactinfo");
			$json = json_decode(curl_exec($ch));
			if($json == NULL)
				throw new OtherException("用户信息json解释失败。", "");
			$json = $json->contact_info;
			$nickName = $json->nick_name;
			$username = $json->user_name;
				
			return array("fakeid"=>$fid, "nickName"=>addslashes($nickName), "username"=>$username);
				
		} else
			throw new OtherException("消息没有检索到,:".$cont, "");

		return false;
	}
	
	/***********************
	 * 模拟登录微信平台
	 ************************/
	function loginToWxMp($user, $pw) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR,  "");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "");
		curl_setopt($ch, CURLOPT_REFERER,    "https://mp.weixin.qq.com/");
		curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/");
		//四秒钟，因为微信只能支持5秒响应时间，超过5秒则无反应
		//curl_setopt($ch, CURLOPT_TIMEOUT,    4);
		//设置这个是因为连接都是https
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POST,    true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('username'=>$user,'pwd'=>$pw, 'f'=>'json' , 'imgcode'=>''));
		curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN");
	
		$str = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE)."";
		if($code[0] != 2)
			throw new OtherException("无法登录mp.weixin,httpCode:".$code, "");
		if(!preg_match("/token=(.+?)\"/i", $str, $matchs))
			throw new OtherException("无法登录，可能是账号密码错误。", "");
		
		$token = $matchs[1];
		return array( "ch"=>$ch, "token"=>$token );
	}
	
	public static function storeImg($fakeid, $uid, $token, $ch) {
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_URL, "https://mp.weixin.qq.com/cgi-bin/getheadimg?token={$token}&fakeid={$fakeid}&lang=zh_CN");
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
	
	/*********************
	 * 生成小于零的随机数
	*********************/
	public static function my_rand() {
		$r =  mt_rand()/mt_rand();
		$r = abs($r - floor($r));
		return $r;
	}
}
