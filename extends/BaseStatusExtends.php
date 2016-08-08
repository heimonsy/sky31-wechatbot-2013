<?php
class BaseStatusExtends extends BaseExtends
{
	
	function analyse()
	{
		if( $this->status==STAT_BASE ){
			//当前栏目
			$this->responseMsg = $this->analyseMsg();
			
		}else if( $this->status==STAT_COURSE ){
			//绑定用户信息的扩展
			$next = new CourseExtends($this->user, $this->receivedMsg, $this->nextStatus);
			$next->analyse();
			$this->responseMsg = $next->getMsg();
			
		}else if( $this->status==STAT_LEHUO){
			//乐活扩展
			$next = new LehuoExtends($this->user, $this->receivedMsg, $this->nextStatus);
			$next->analyse();
			$this->responseMsg = $next->getMsg();
			
		}else if( $this->status==STAT_RADIO ){
			//电台扩展
			$next = new RadioExtends($this->user, $this->receivedMsg, $this->nextStatus);
			$next->analyse();
			$this->responseMsg = $next->getMsg();
			
		}else if( $this->status==STAT_BIND ){
			//绑定用户信息的扩展
			$next = new BindExtends($this->user, $this->receivedMsg, $this->nextStatus);
			$next->analyse();
			$this->responseMsg = $next->getMsg();
			
		}else{
			$msgFactory = new WxMsgFactory();
			$this->responseMsg = $msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName, 
					Notices::STAT_ERROR, 
					time()
					);
			$this->responseMsg = $msgFactory->getMsg();
		}
	}
	
	function analyseMsg()
	{
		if( $this->receivedMsg->msgType ==MSG_TEXT )
			//文本消息
			return $this->analyseTextMsg();
		else if( $this->receivedMsg->msgType == MSG_EVENT  ){
			
			$msgFactory = new WxMsgFactory();
			$msgFactory -> setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					Notices::COMMON_HELP
					);
			return $msgFactory->getMsg();
		}
		else{
			$msgFactory = new WxMsgFactory();
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					"您送的信息我们目前还不能处理哦T_T"
			);
			return $msgFactory->getMsg();
		}
	
	}
	
	function analyseTextMsg()
	{
		$msgFactory = new WxMsgFactory();

		if( preg_match( NEWS_REG, $this->receivedMsg->content )>0 ){
			//今日湘大
			$newsList = NewsFuncs::getTodayNews();
			$msgFactory->setNewsMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$newsList
					);
			return $msgFactory->getMsg();
			
		
		}else if( preg_match( RADIO_REG, $this->receivedMsg->content) ){
			//进入电台栏目
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
			return $msgFactory->getMsg();

		}else if( preg_match( "/^成绩|成绩查询|期末成绩|查分|查分数|查成绩$/i", $this->receivedMsg->content) ){
			//成绩查询
			$content = getScore($this->user->userId);

			$msgFactory -> setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
					);
			return $msgFactory->getMsg();

		}else if( preg_match( WEATHER_REG, $this->receivedMsg->content) ){
			//发送天气预报
			$arr = OtherFuncs::getWeather();
			$msgFactory->setMusicMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$arr['name'],
					'四季电台录制',
					$arr['fileUrl'],
					$arr['fileUrl']
				);
		
			return $msgFactory->getMsg();
		
		}else if( preg_match( COURSE_REG, $this->receivedMsg->content, $matchStr )) {
			$content = CourseExtends::setCourseStatus($this->user);
			$msgFactory ->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
					
					);
			return $msgFactory->getMsg();
			
		}else if( preg_match( HELP_REG, $this->receivedMsg->content) ){
			//帮助信息
			$content = Notices::COMMON_HELP;
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
			);
			return $msgFactory->getMsg();
		
		}else if( preg_match( TRHO_REG, $this->receivedMsg->content, $matchStr) ){
		
			//发送到树洞
			if( OtherFuncs::throwToTreeHole( trim($matchStr[1]), $this->user ))
				$content="您的信息发表成功! 您可以这个页面：".THPL_SINA."看到您的信息";
			else
				$content="您发送的信息可能为空白，或有误!";
		
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
					
			);
			return $msgFactory->getMsg();
		
		}else if( preg_match( WALK_REG, $this->receivedMsg->content, $matchStr) ){
			//获取外卖信息
			$area = $matchStr[1];
			if( $area=='外卖' || $area =='3' ){
				$content="校园外卖信息\n";
				$content.=TakeAwayFuncs::getAllSell();
				$content.="回复地区名字可获得该地区的外卖详情。";
			}else{
				$content='【'.$area."】外卖\n";
				$content.=TakeAwayFuncs::getTakeListByAreaName($area);
				$content.="回复其他地区名获取其他地区的外卖。";
			}
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
					);
			return $msgFactory->getMsg();
		
		}else if( preg_match( BIND_REG, $this->receivedMsg->content) ){
			//绑定用户信息扩展
			$content = BindExtends::setBindStatus($this->user);
			
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
			);
			return $msgFactory->getMsg();
			
		}else if( preg_match( TALK_SUBJECT, $this->receivedMsg->content ,$matchStr) ){
			//nihao
			$content = TalkSubjectExtends::insertTalkSubject($this->user, $this->receivedMsg, $matchStr[1]);
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
						
			);
			return $msgFactory->getMsg();
			
		}else if( preg_match( TRANSLATION_REG, $this->receivedMsg->content, $matchStr) ){
			//翻译
			$content = OtherFuncs::translation( $matchStr[1] );
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$content
					
					);
			return $msgFactory->getMsg();
		
		}else{
			exit();
			//调用小黄鸡
			$str = Simsimi::getSimsimi( $this->receivedMsg->content);
			$str = json_decode($str);
			if( isset($str->result) && $str->result==100 )
				$str = $str->response;
			else $str= "你说呢^_^";
			$msgFactory->setTextMsg(
					$this->receivedMsg->fromUserName,
					$this->receivedMsg->toUserName,
					time(),
					$str
			);
		
			return $msgFactory->getMsg();
		}
	}
	
	
	
	
	
}