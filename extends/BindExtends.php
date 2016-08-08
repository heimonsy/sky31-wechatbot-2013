<?php


class BindExtends extends BaseExtends
{
	function analyse()
	{
		
		if( $this->receivedMsg->msgType==MSG_TEXT ){
			
			$this->responseMsg=$this->analyseText();
			
		}
		else if( $this->receivedMsg->msgType==MSG_IMAGE ){
			$r = $this->setUserAvatar();
			if( $r ) 
				$content="修改头像成功，回复‘头像’可以查看";
			else
				$content="修改失败，请尝试重新发送".mysql_error();
			
			$msgFactory = new WxMsgFactory();
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
					);
			$this->responseMsg = $msgFactory->getMsg();
		}else
		{
			
		}
	}
		
	function analyseText()
	{
		$msgFactory = new WxMsgFactory();
		if( $this->receivedMsg->content=='头像' ){
			$url = $this->user->getPicUrl();
			if( $url==false )
				$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					"您还没有绑定头像，在本栏目下回复一张图片即可绑定。"
					);
			else{
				$msgFactory->setSingleNews(
						$this->receivedMsg->fromUserName,
						$this->receivedMsg->toUserName,
						time(), 
						"您的头像。",
						"在本栏目下回复一张图片即可修改头像。".Notices::BIND_COMMON, 
						$url,
						ROOT_URL."/data/pic/getpic.php?pid=".$this->user->pid
						);
			}
			return $msgFactory->getMsg();
			
		}else if( $this->receivedMsg->content=='修改' ){
			
			$oauth = Oauth::getOauth($this->user->userId);
			$url = OAUTH_URL."?oa=".$oauth;
			$content="点击：".$url." 进行修改。";
			
			$msgFactory->setTextMsg(
				$this->receivedMsg->fromUserName,
				$this->receivedMsg->toUserName,
				time(),
				$content
				);
			return $msgFactory->getMsg();
			
		}else if( $this->receivedMsg->content=='退出'|| $this->receivedMsg->content=='0' ){
			$this->user->setStatus( STAT_BASE );
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					"成功退出。\n".Notices::COMMON_EXIT_HELP
			);
			return $msgFactory->getMsg();
			
		}else{
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					"错误的命令!\n您当前在绑定栏目下。".Notices::COMMON_EXIT
			);
			return $msgFactory->getMsg();
		}
	}
	
	function setUserAvatar()
	{
		$wxdb = MyDB::getWxdb();
		$this->receivedMsg->storePic( $this->user->userId );
		$sql = "update `wx_user` set `pid`='{$this->receivedMsg->pid}' where `uid`='{$this->user->userId}';";
		$r = $wxdb->query($sql);
		return $r;
	}
	
	public static function setBindStatus($user)
	{
		$bind = Oauth::haveBind($user->userId);
		$content="";
		if( $bind ){
			//已经绑定就设置
			$user->setStatus( STAT_BIND.BIND_BASE );
			$content.="您已经绑定了个人信息：\n学号：".Encodes::decode($bind['snum']);
			$content.="\n昵称：".$bind['nname']."\n";
			$content.=Notices::BIND_COMMON;
		}else{
			$oauth = Oauth::getOauth($user->userId);
			$url = OAUTH_URL."?oa=".$oauth;
			$content.="您还没有绑定个人信息。\n点击：".$url." 进行绑定。进行绑定。\n绑定成功后回复`课表`可以查询课表哦。";
		}
		
		return $content;
	}
}