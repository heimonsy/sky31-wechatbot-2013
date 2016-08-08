<?php
class Simsimi
{
	/**
	 * 根据用户输入的字符，返回simsimi的回复内容
	 * @author heister
	 * @access public
	 * @param  $str:string
	 * @return string
	 */
	public static function getSimsimi($str)
	{
		$ch = curl_init("http://www.simsimi.com/talk.htm");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR,  "cookie.txt");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
		curl_setopt($ch, CURLOPT_REFERER,    "http://www.simsimi.com/talk.htm");
		//四秒钟，因为微信只能支持5秒响应时间，超过5秒则无反应
		curl_setopt($ch, CURLOPT_TIMEOUT,    4);
		curl_setopt($ch, CURLOPT_URL, "http://www.simsimi.com/func/req?msg=".urlencode($str)."&lc=ch");
	
		$str = curl_exec($ch);
	
		if( $str=="" || preg_match("/小黄鸡|微信号|微(.*)信/", $str) )
			$str = "{\"response\":\""."难得这么高兴，来来，喝了这杯"."\",\"id\":24389944,\"result\":100,\"msg\":\"OK.\"}";
		return $str;
	}
}