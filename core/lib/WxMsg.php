<?php
/**
 * 信息基础类
 * @author Heister
 * @version 1.0
 * qq 250661062
 */
class WxBaseMsg
{
	public $fromUserName;
	public $toUserName;
	public $funcFlag = 0;
	public $msgType  =null;
	public $time;
	public $msgId;

	public function __construct($toUserName, $fromUserName, $time, $funcFlag=0)
	{
		$this->toUserName   = $toUserName;
		$this->fromUserName = $fromUserName;
		$this->time         = $time;
		$this->funcFlag     = $funcFlag;
	}

	public function setMsg(){}

	public function __toString()
	{
		return "MsgBaseType";
	}

	public function setFuncFlag($flag)
	{
		$this->funcFlag = $flag;
	}
}

class WxTextMsg extends WxBaseMsg
{
	public $content;

	public function __construct($toUserName, $fromUserName, $time, $funcFlag=0)
	{
		parent::__construct($toUserName, $fromUserName, $time, $funcFlag);
		$this->msgType = MSG_TEXT;
	}
	/**
	 *  设置文本类型的消息
	 */
	public function setMsg($content)
	{
		$this->content=$content;
	}

	public function  __toString()
	{
		$textTpl="<xml>".
			"<ToUserName><![CDATA[".$this->toUserName."]]></ToUserName>" .
			"<FromUserName><![CDATA[".$this->fromUserName."]]></FromUserName>". 
			"<CreateTime>".$this->time."</CreateTime>".
			"<MsgType><![CDATA[".$this->msgType."]]></MsgType>".
			"<Content><![CDATA[".$this->content."]]></Content>".
			"<FuncFlag>".$this->funcFlag."</FuncFlag>".
			"</xml>";
		return $textTpl;
	}

}

class WxImageMsg extends WxBaseMsg
{
	public $picUrl;
	public $uid;
	
	function __construct($toUserName, $fromUserName, $time, $funcFlag=0)
	{
		parent::__construct($toUserName, $fromUserName, $time, $funcFlag);
		$this->msgType = MSG_IMAGE;
	}
	
	public function setMsg( $picUrl )
	{
		$this->picUrl = $picUrl;
	}
	
	public function storePic($uid)
	{
		$time = $this->time;
		$wxdb = MyDB::getWxdb();
		$sql  = "insert into `wx_pic` (`pid`,`uid`,`url`,`time`) values ( null, '{$uid}', '{$this->picUrl}', $time)";
		$r = $wxdb->query($sql);
		$this->pid = mysql_insert_id($wxdb->link); 
		return $r;
	}
	public function __toString()
	{
		$textTpl="<xml><ToUserName><![CDATA[".$this->toUserName."]]></ToUserName>
<FromUserName><![CDATA[".$this->fromUserName."]]></FromUserName>
<CreateTime>".$this->time."</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<PicUrl><![CDATA[".$this->picUrl."]]></PicUrl>
<FuncFlag>".$this->funcFlag."</FuncFlag>
</xml>";
		return $textTpl;
	}
}

class WxNewsMsg extends WxBaseMsg
{
	public $articleCount;
	public $newsItems;


	public function __construct($toUserName, $fromUserName, $time, $funcFlag=0)
	{
		parent::__construct($toUserName, $fromUserName, $time, $funcFlag);
		$this->msgType = MSG_NEWS;
	}
	/**
	 *  设置文本类型的消息
	 */
	public function setMsg($newsItems)
	{
		$this->newsItems = $newsItems;
		$this->articleCount = count($newsItems);
	}

	public function  __toString()
	{
		$textTpl="<xml>
<ToUserName><![CDATA[".$this->toUserName."]]></ToUserName>
<FromUserName><![CDATA[".$this->fromUserName."]]></FromUserName> 
<CreateTime>".$this->time."</CreateTime>
<MsgType><![CDATA[".$this->msgType."]]></MsgType>
<ArticleCount>".$this->articleCount."</ArticleCount>
<Articles>";

		for($i=0;$i<$this->articleCount;$i++)
		{
			$textTpl.="
<item>
<Title><![CDATA[".$this->newsItems[$i]->title."]]></Title>
<Description><![CDATA[".$this->newsItems[$i]->description."]]></Description>
<PicUrl><![CDATA[".$this->newsItems[$i]->picUrl."]]></PicUrl>
<Url><![CDATA[".$this->newsItems[$i]->url."]]></Url>
</item>";
		}

		$textTpl.="
</Articles>
<FuncFlag>".$this->funcFlag."</FuncFlag>
</xml>";

		return $textTpl;
	}

}


class WxNewsItem
{
	public $title;
	public $description;
	public $picUrl;
	public $url;

	public function __construct($title, $description, $picUrl, $url)
	{
		$this->title = $title;
		$this->description = $description;
		$this->picUrl = $picUrl;
		$this->url = $url;
	}
}

class WxMusicMsg extends WxBaseMsg
{
	public $title;
	public $description;
	public $musicUrl;
	public $HQMusicUrl;

	public function __construct($toUserName, $fromUserName, $time, $funcFlag=0)
	{
		parent::__construct($toUserName, $fromUserName, $time, $funcFlag);
		$this->msgType = MSG_MUSIC;
	}

	public function setMsg($title, $description,$musicUrl, $HQMusicUrl)
	{
		$this->title = $title;
		$this->description = $description;
		$this->musicUrl = $musicUrl;
		$this->HQMusicUrl = $HQMusicUrl;
	}

	public function __toString()
	{
		$textTpl="<xml>
			<ToUserName><![CDATA[".$this->toUserName."]]></ToUserName>
			<FromUserName><![CDATA[".$this->fromUserName."]]></FromUserName>
			<CreateTime>".$this->time."</CreateTime>
			<MsgType><![CDATA[".$this->msgType."]]></MsgType>
			<Music>
			<Title><![CDATA[".$this->title."]]></Title>
			<Description><![CDATA[".$this->description."]]></Description>
			<MusicUrl><![CDATA[".$this->musicUrl."]]></MusicUrl>
			<HQMusicUrl><![CDATA[".$this->HQMusicUrl."]]></HQMusicUrl>
			</Music>
			<FuncFlag>0</FuncFlag>
			</xml>";
		return $textTpl;
	}

}

class WxOtherMsg extends WxBaseMsg
{
	public $textTpl;
	public $postObj;

	public function __construct($toUserName, $fromUserName, $time, $funcFlag=0)
	{
		parent::__construct($toUserName, $fromUserName, $time, $funcFlag);
		$this->msgType = MSG_OTHER;
	}

	public function setMsg($textTpl,$postObj)
	{
		$this->textTpl = $textTpl;
		$this->postObj = $postObj;
	}

	public function __toString()
	{
		return $this->textTpl;
	}

}
class WxEventMSg extends WxBaseMsg
{
	public $event;
	public $eventKey;
	public $content;
	
	public function __construct($toUserName, $fromUserName, $time, $funcFlag=0)
	{
		parent::__construct($toUserName, $fromUserName, $time, $funcFlag);
		$this->msgType = MSG_EVENT;
	}
	/**
	 *
	 * 
	 * 
	 */
	public function setMsg( $event, $eventKey )
	{
		$this->content = $event;
		$this->event = $event;
		$this->eventKey = $eventKey;
	}
}

class WXVoiceMsg extends WxBaseMsg
{
	public $content;

	public function __construct($toUserName, $fromUserName, $time, $funcFlag=0)
	{
		parent::__construct($toUserName, $fromUserName, $time, $funcFlag);
		$this->msgType = MSG_VOICE;
	}

	public function setMsg($msgId)
	{
		$this->msgId = $msgId;
		//$this->content = trim($this->getTextFromAudio($msgId));

	}

	public function setContent()
	{
		//延时是因为到mp平台获取信息存在延时，腾讯是先给开发者发送信息，再在mp上插入显示。有几百ms的误差。
		///usleep(500000);
		$this->content = trim($this->getTextFromAudio($this->msgId));
	}

	function getMediaFile($msgId)
	{	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR,  "cookie.txt.log");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt.log");
		curl_setopt($ch, CURLOPT_REFERER,    "http://mp.weixin.qq.com/");
		curl_setopt($ch, CURLOPT_URL, "http://mp.weixin.qq.com/");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array (
	         "Accept: text/html, application/xhtml+xml, */*",
	         "User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)",
	         "DNT: 1",
	         "Connection: Keep-Alive",
	     ));
		//四秒钟，因为微信只能支持5秒响应时间，超过5秒则无反应
		//curl_setopt($ch, CURLOPT_TIMEOUT,    4);
		curl_setopt($ch, CURLOPT_POST,    true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('username'=>'250661062@qq.com','pwd'=>'7cf5bc49b333fe77af16b0aeeb351eaa'));

		curl_setopt($ch, CURLOPT_URL, "http://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN");
		curl_exec($ch);
		curl_setopt($ch, CURLOPT_URL, 'http://mp.weixin.qq.com/cgi-bin/downloadfile?msgid='.$msgId);
		$str = curl_exec($ch);

		//如果没有获取到
		if( $str=='' ) return false;
		
		//ffmpeg -i 2.mp3 -ar 16000 -ac 1 output.wav
		$fnameMp3 = "audio/".$msgId.".mp3";
		$fnameWav = "audio/".$msgId.".wav";

		$fMp3 = fopen( $fnameMp3,"w+");
		fwrite($fMp3, $str);
		fclose($fMp3 );
		@unlink($fnameWav);
		$r=system("ffmpeg -i $fnameMp3 -ar 16000 -ac 1 $fnameWav", $ret);

		return $fnameWav;
	}

	function getTextFromAudio($msgId)
	{
		$t = 0;
		while( $t<=3 ){
			$fname = $this->getMediaFile($msgId);
			if( $fname==false ) $t++;
			else break;
			usleep(100000);
		}
		
		
		$str=shell_exec('java -cp ".;json-jena-1.0.jar;Msc.jar" MyMSC '.$fname);
		$str = iconv("GB2312","UTF-8",$str);
		return $str;
	}


}