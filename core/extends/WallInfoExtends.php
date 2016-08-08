<?php


class WallInfoExtends extends BaseExtends
{
	
	public static function getKeyWordPatterns() {
		//﹟
		return "/^微信墙$/i";
	}
	
	public function analyse($matchs=NULL) {
		
		$content = "回复 #+话题+#+内容 (没有+号)即可发表消息上墙。\n热门话题：\n".WallExtends::getRecentSubject();
		
		$this->responseMsg = WxMsgFactory::setTextMsg(
				$this->receivedMsg->fromUserName, 
				$this->receivedMsg->toUserName, 
				time(), $content);
		return $this->responseMsg;
	}
	
}