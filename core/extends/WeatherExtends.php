<?php

class WeatherExtends extends BaseExtends
{
	public static function getKeyWordPatterns() {
		return "/天气/i";
	}
	
	public function analyse($matchs=NULL) {
		@$str = 'http://api.ajaxsns.com/api.php?key=free&appid=0&msg=' . urlencode('天气湘潭');
        $json = json_decode(file_get_contents($str));
        $str  = str_replace('{br}', "\n", $json->content);
		/*
		$ch = curl_init("http://www.weather.com.cn/data/cityinfo/101250201.html");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$str = curl_exec($ch);
		$obj = json_decode($str);
		$obj = $obj->weatherinfo;
		$content = "湘潭今日天气：".$obj->weather."\n";
		$content .= "今日温度：".$obj->temp2.'~'.$obj->temp1."\n";
		
		$this->responseMsg = WxMsgFactory::setTextMsg(
				$this->receivedMsg->fromUserName, 
				$this->receivedMsg->toUserName,
				time(), $content);
		
		return $this->responseMsg;
		*/
		$this->responseMsg = WxMsgFactory::setTextMsg(
				$this->receivedMsg->fromUserName, 
				$this->receivedMsg->toUserName,
				time(), $str);
		return $this->responseMsg;
	}
}