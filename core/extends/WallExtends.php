<?php


class WallExtends extends BaseExtends
{
	
	public static function getKeyWordPatterns() {
		//﹟
		return "/^[\043](.+)[\043](.*)$/i";
	}
	
	public function analyse($matchs=NULL) {
		if($matchs != null) {
			//发表话题内容
			$subject = addslashes($matchs[1]);
			$cont = htmlspecialchars(addslashes($this->receivedMsg->content));
			$sid = self::getSubjectId($subject);
			if($sid != false) {
				//话题存在则插入话题
				$time = time();
				$db = MyDB::getWxdb();
				$sql = "insert into `wx_rsubmsg` VALUES (NULL, '{$sid}', '{$this->user->uid}', '{$cont}', 0, '{$time}')";
				$r = $db->query($sql);
				if(!$r)
					throw new CURDException("插入错误,wx_rsubmsg,uid:".$this->user-uid.",msg:".$cont, "发表失败，服务器内部错误", mysql_error());
				
				$content = "话题发表成功 :-)";
				
			} else //话题不存在
				$content = "您发表的话题\"#".$subject."#\"不存在。\n近期话题:\n".self::getRecentSubject(); 
			
		} else 
			throw new OtherException("WallExtends错误, uid:".$this->user->uid, "服务器内部错误T^T");
		
		$this->responseMsg = WxMsgFactory::setTextMsg(
				$this->receivedMsg->fromUserName, 
				$this->receivedMsg->toUserName, 
				time(), $content);
		return $this->responseMsg;
	}
	
	
	public static function getSubjectId($sname) {
		$db = MyDB::getWxdb();
		$sql = "select `sid` from `wx_subject` where `word`='{$sname}'";
		$r = $db->query($sql);
		if(!$r)
			throw new CURDException("wx_subject,获取微信墙sid失败", "话题发表失败，服务器内部错误T^T", mysql_error());
		
		$r = mysql_fetch_assoc($r);
		$sid = NULL;
		if($r != NULL)
			$sid = $r['sid'];
		return $sid;
	}
	 
	public static function insertSubject($sname) {
		$db =  MyDB::getWxdb();
		$sql = "insert into `wx_subject` VALUES (NULL, '{$sname}')";
		$r = $db->query($sql);
		if(!$r)
			throw new CURDException("wx_subject，插入话题失败, sname:".$sname, "话题发表失败，服务器内部错误T^T", mysql_error());
			
		$sid = mysql_insert_id($db->link);
	}
	
	public static function getRecentSubject() {
		$db = MyDB::getWxdb();
		$sql = "select `word` from `wx_subject` order by `time`,`sid` desc limit 3";
		$r = $db->query($sql);
		$str = "";
		while(($m=mysql_fetch_assoc($r)) != NULL)
			$str .= "#".$m['word']."#\n";  
		return $str;
	}
	
}