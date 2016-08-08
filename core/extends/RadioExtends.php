<?php
class RadioExtends extends BaseExtends
{
	public static function getKeyWordPatterns() {
		return "/电台|四季电台/i";
	}
	function analyse($matchs=NULL)
	{
		return $this->responseMsg;
		if($this->receivedMsg->msgType == MSG_TEXT ){
			if($matchs==NULL){
				$content = "【四季电台】：";
			}
			
		}else{
			$content = "你目前在【四季电台】栏目下 你回复的信息类型本栏目尚不支持。 回复 0 或 退出 退出当前的栏目。";
			$this->responseMsg =  WxMsgFactory::setTextMsg(
					$this->receivedMsg->fromUserName, 
					$this->receivedMsg->toUserName, 
					time(), 
					$content
					);
			return $this->responseMsg;
		}
	}

	
	function analyseBase()
	{
		$msgFactory = new WxMsgFactory();
		if( $this->receivedMsg->content=='退出' ){
			$this->user->setStatus( STAT_BASE );
			$content = "成功退出！\n";
			$content .= Notices::COMMON_HELP;
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
					);
			
		}else if( $this->receivedMsg->content=='帮助' ){
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					Notices::RADIO_LM.Notices::COMMON_EXIT
					);
		}else{
			//debug( "content", $this->receivedMsg->content  );
			switch ( $this->receivedMsg->content ){
				case 1: case '青春祭':
					$this->user->setStatus( STAT_RADIO.RADIO_YOUTH );
					$content = $this->getRadioListContent("【青春祭】", YOUTH_INDEX);
					break;
					
				case 2: case '春':
					$this->user->setStatus( STAT_RADIO.RADIO_SPRING );
					$content = $this->getRadioListContent("【春·绘声绘影】", SPRING_INDEX);
					break;
					
				case 3: case '夏';
					$this->user->setStatus( STAT_RADIO.RADIO_SUMMER );
					$content = $this->getRadioListContent("【夏·爱的发声】", SUMMER_INDEX);
					break;
					
				case 4: case '秋';
					$this->user->setStatus( STAT_RADIO.RADIO_AUTUMN );
					$content = $this->getRadioListContent("【秋·小情小调】", AUTUMN_INDEX);
					break;
					
				case 5: case '冬';
					$this->user->setStatus( STAT_RADIO.RADIO_WINTER );
					$content = $this->getRadioListContent("【冬·你说我说】", WINTER_INDEX);
					break;
					
				default:
					$content = '输入有误。\n'.Notices::COMMON_EXIT;
					break;
			}
				$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
					);
		}
		
		
		return $msgFactory->getMsg();
	}
	
	/**
	 *获取电台的xml对象
	 * @return $xmlObj
	 */
	function getRadioXml()
	{
		$xmlObj = simplexml_load_file('http://radio.sky31.com/player/xml/mp3_player.xml');
		return $xmlObj;
	}
	
	/**
	 * 
	 * @param string $xml
	 * @param int $index
	 * @return string
	 */
	function getListFromXml($xml, $index)
	{
		$xml = $xml->album[$index];
		//print_r($xml);
		$list_nums = count($xml->song);
		$res="";
		for($i=0;$i<9 && $i<$list_nums;$i++)
		{
			$att = $xml->song[$i]->attributes();
			$res.=($i+1);
			$res.=", ";
			$res.=$att->name;
			$res.="\n";
		}
		return $res;
	}

	
	/**
	  获取列表内容
	 * @param $lmName $INDEX
	 * @return $content
	 */
	function getRadioListContent($lmName, $INDEX)
	{
		$content="欢迎进入 ".$lmName.", 节目列表如下：\n";
		$xml=$this->getRadioXml();
		$content.=$this->getListFromXml($xml, $INDEX);
		$content.=Notices::RADIO_CHOISE;
		$content.=Notices::COMMON_EXIT."回复\"栏目\" 返回电台栏目。";
		return $content;
	}

	function getRadioObj($xml, $index, $songNum)
	{
		//因为-1
		$songNum--;
		if( $songNum>=0 &&$songNum<9 && $songNum>count($xml->album[$index]->song) ) return false;
		return $xml->album[$index]->song[intval($songNum)]->attributes();
	}

	function getRadioFile($radioObj)
	{
		return $radioObj->buyLink."/".$radioObj->downloadSource;
	}

	/**
	 * 根据用户的输入，获取并向用户返回msg
	 * @param $receivedMsg $index $songNum
	 */
	function getRadioFileMsg($receivedMsg, $index, $songNum)
	{
		$msgFactory  = new WxMsgFactory();
		
		if( $songNum=="退出"|| $songNum=="0" ){
			$this->user->setStatus( STAT_BASE );
			$content = "成功退出！\n";
			$content .= Notices::COMMON_HELP;
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
			);
			
		}else if( $songNum=='栏目' ){
			$this->user->setStatus( STAT_RADIO.RADIO_BASE );
			$content =  "欢迎收听 四季电台:\n";
			$content.=Notices::RADIO_LM;
			$content.=Notices::COMMON_EXIT;
			$msgFactory -> setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
			);
			
		}else if(is_numeric( $songNum ) && $radioObj=$this->getRadioObj($this->getRadioXml(), $index, $songNum) ){
			//正常的情况，返回节目
			$url= $this->getRadioFile($radioObj);
			$msgFactory->setMusicMsg(
				$receivedMsg->fromUserName,
				$receivedMsg->toUserName,
				time(),
				$radioObj->name,
				"四季电台",
				$url,
				$url
			);
	
		}else{//指令无效的情况
	
			$content = "您输入的指令有无效！\n";
			$content .= $this->getRadioListContent( Notices::getRadioLmTitle($index), $index );
			$msgFactory->setTextMsg(
				$receivedMsg->fromUserName,
				$receivedMsg->toUserName,
				time(),
				$content
				
			);
	
		}
		return $msgFactory->getMsg();
	}
	
}