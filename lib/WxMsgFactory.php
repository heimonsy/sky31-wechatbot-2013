<?php
/**
 * 信息工厂类
 *
 */
class WxMsgFactory
{
	private $producedMsg=null;

	/**
	 * 构造函数
	 * @param $postStr
	 */
	public function __construct($postStr=null)
	{
		if( $postStr!=null ) $this->loadFromXml($postStr);
	}

	/**
	 * 通过xml String加载数据
	 * @param $postStr
	 */
	public function loadFromXml($postStr)
	{
		//初始化数据
		$postObj            = simplexml_load_string(
				$postStr,'SimpleXMLElement', LIBXML_NOCDATA);

		if( $postObj->MsgType==MSG_TEXT )
		{
			$this->setTextMsg(
					$postObj->ToUserName,
					$postObj->FromUserName,
					$postObj->CreateTime,
					addslashes( trim($postObj->Content) )
					
			);

		}else if( $postObj->MsgType==MSG_VOICE ){
			$this->setVoiceMsg(
					$postObj->ToUserName,
					$postObj->FromUserName,
					$postObj->CreateTime,
					$postObj->MsgId
			);

		}else if( $postObj->MsgType==MSG_EVENT ){
			$this->setEventMsg(
					$postObj->ToUserName,
					$postObj->FromUserName,
					$postObj->CreateTime,
					$postObj->Event,
					$postObj->EventKey
			);
			
		}else if( $postObj->MsgType==MSG_IMAGE ){
			$this->setImageMsg(
					$postObj->ToUserName,
					$postObj->FromUserName,
					$postObj->CreateTime,
					$postObj->PicUrl
			);
			
		}else{
			$this->setOtherMsg(
					$postObj->ToUserName,
					$postObj->FromUserName,
					$postObj->CreateTime,
					$postStr,
					$postObj
			);
		}
	}

	public function setTextMsg($toUserName, $fromUserName,  $time ,$content)
	{
		$this->producedMsg = new WxTextMsg($toUserName, $fromUserName, $time);
		$this->producedMsg->setMsg($content);
	}

	public function setNewsMsg($toUserName, $fromUserName, $time , $newsList)
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
		$this->producedMsg = new WxNewsMsg($toUserName, $fromUserName, $time);
		$this->producedMsg->setMsg($article);
	}
	
	public function setSingleNews($toUserName, $fromUserName, $time, $title, $description, $picUrl, $src)
	{
		$news = array( array('title'=>$title, 'description'=>$description, 'pic_url'=>$picUrl, 'src'=>$src) );
		$this->setNewsMsg($toUserName, $fromUserName, $time, $news);
	}

	public function setMusicMsg($toUserName, $fromUserName, $time ,$title, $description, $musicUrl, $HQMusicUrl)
	{
		$this->producedMsg = new WxMusicMsg($toUserName, $fromUserName, $time);
		$this->producedMsg->setMsg($title, $description, $musicUrl, $HQMusicUrl);
	}

	public function setOtherMsg($toUserName, $fromUserName, $time, $textTpl, $postObj)
	{
		$this->producedMsg = new WxOtherMsg($toUserName, $fromUserName, $time);
		$this->producedMsg->setMsg($textTpl, $postObj);
	}

	public function setVoiceMsg($toUserName, $fromUserName, $time, $msgId){
		$this->producedMsg = new WxVoiceMsg( $toUserName, $fromUserName, $time );
		$this->producedMsg->setMsg( $msgId );
	}
	
	public function setEventMsg( $toUserName, $fromUserName,$time, $event, $eventKey){
		$this->producedMsg = new WxEventMSg($toUserName, $fromUserName, $time);
		$this->producedMsg->setMsg( $event, $eventKey);
	}
	
	public function setImageMsg( $toUserName, $fromUserName,$time, $picUrl){
		$this->producedMsg = new WxImageMsg($toUserName, $fromUserName, $time);
		$this->producedMsg->setMsg($picUrl);
	}

	public function getMsg()
	{
		return $this->producedMsg;
	}

}