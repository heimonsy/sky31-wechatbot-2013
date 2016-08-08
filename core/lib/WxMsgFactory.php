<?php
/**
 * 信息工厂类
 *
 */
class WxMsgFactory
{
	/**
	 * 上一次操作产生的消息
	 */
	private static $producedMsg = null;
	
	public static $receivedMsg;
	
	/**
	 * 通过xml String加载数据
	 * @param $postStr
	 */
	public static function loadFromXml($postStr) {
		//初始化数据
		$postObj = simplexml_load_string(
				$postStr,'SimpleXMLElement', LIBXML_NOCDATA);

		if( $postObj->MsgType==MSG_TEXT ) {
			self::setTextMsg(
					$postObj->ToUserName,
					$postObj->FromUserName,
					$postObj->CreateTime,
					addslashes( trim($postObj->Content) )
					
			);

		}else if( $postObj->MsgType==MSG_VOICE ) {
			self::setVoiceMsg(
					$postObj->ToUserName,
					$postObj->FromUserName,
					$postObj->CreateTime,
					$postObj->MsgId
			);

		}else if( $postObj->MsgType==MSG_EVENT ){
			self::setEventMsg(
					$postObj->ToUserName,
					$postObj->FromUserName,
					$postObj->CreateTime,
					$postObj->Event,
					$postObj->EventKey
			);
			
		}else if( $postObj->MsgType==MSG_IMAGE ){
			self::setImageMsg(
					$postObj->ToUserName,
					$postObj->FromUserName,
					$postObj->CreateTime,
					$postObj->PicUrl
			);
			
		}else{
			self::setOtherMsg(
					$postObj->ToUserName,
					$postObj->FromUserName,
					$postObj->CreateTime,
					$postStr,
					$postObj
			);
		}
		return self::$producedMsg;
	}

	public static function setTextMsg($toUserName, $fromUserName,  $time ,$content)
	{
		self::$producedMsg = new WxTextMsg($toUserName, $fromUserName, $time);
		self::$producedMsg->setMsg($content);
		return self::$producedMsg;
	}

	public static function setNewsMsg($toUserName, $fromUserName, $time , $newsList)
	{
		foreach ($newsList as $m)
		{
			$article[] = new WxNewsItem(
					$m['title'],
					$m['description'],
					$m['pic_url'],
					$m['src']
			);
		}
		//$article[] = new WxNewsItem( "这是标题","这是描述","http://202.197.225.101/mobile/images/1.jpg","http://www.sky31.com" );
		self::$producedMsg = new WxNewsMsg($toUserName, $fromUserName, $time);
		self::$producedMsg->setMsg($article);
	}
	
	public static function setSingleNews($toUserName, $fromUserName, $time, $title, $description, $picUrl, $src)
	{
		$news = array( array('title'=>$title, 'description'=>$description, 'pic_url'=>$picUrl, 'src'=>$src) );
		self::setNewsMsg($toUserName, $fromUserName, $time, $news);
	}

	public static function setMusicMsg($toUserName, $fromUserName, $time ,$title, $description, $musicUrl, $HQMusicUrl)
	{
		self::$producedMsg = new WxMusicMsg($toUserName, $fromUserName, $time);
		self::$producedMsg->setMsg($title, $description, $musicUrl, $HQMusicUrl);
	}

	public static function setOtherMsg($toUserName, $fromUserName, $time, $textTpl, $postObj)
	{
		self::$producedMsg = new WxOtherMsg($toUserName, $fromUserName, $time);
		self::$producedMsg->setMsg($textTpl, $postObj);
	}

	public function setVoiceMsg($toUserName, $fromUserName, $time, $msgId){
		self::$producedMsg = new WxVoiceMsg( $toUserName, $fromUserName, $time );
		self::$producedMsg->setMsg( $msgId );
	}
	
	public static function setEventMsg( $toUserName, $fromUserName,$time, $event, $eventKey){
		self::$producedMsg = new WxEventMSg($toUserName, $fromUserName, $time);
		self::$producedMsg->setMsg( $event, $eventKey);
	}
	
	public static function setImageMsg( $toUserName, $fromUserName,$time, $picUrl) {
		self::$producedMsg = new WxImageMsg($toUserName, $fromUserName, $time);
		self::$producedMsg->setMsg($picUrl);
	}
	
	/**
	 * 
	 * @return WxBaseMsg 上一次操作产生得到信息
	 * 
	 */
	public static function getMsg()
	{
		return self::$producedMsg;
	}

}