<?php


class BindExtends extends BaseExtends
{
	
	public static function getKeyWordPatterns() {
		return "/^绑定$/i";
	}
	
	function analyse($matchs=NULL) {
		$info = self::haveBind($this->user->uid);
		if($info){
			$content = "你绑定信息如下：\n-----------\n";
			$content.= "姓名：".Encodes::decode($info['name'])."\n";
			$content.= "学号：".Encodes::decode($info['snum'])."\n";
			$content.= "-----------\n如果需要重新绑定，请点此链接：".Oauth::getBindUrl($this->user->uid);
			
		}else
			$content = "您还没有绑定个人信息，点击链接进行绑定：".Oauth::getBindUrl($this->user->uid);
		
		$this->responseMsg = WxMsgFactory::setTextMsg(
			$this->receivedMsg->fromUserName, 
			$this->receivedMsg->toUserName, 
			time(), $content);
		
		return $this->responseMsg;
	}
	
	/**
	 * 判断用户是否已经绑定
	 * @param int $uid 用户的id
	 */
	public static function haveBind($uid) {
		$wxdb = MyDB::getWxdb();
		
		$sql = "select `nname`,`snum`,`name`,`pw`,`cwpw` from `wx_user` inner join `stu_info` ON `wx_user`.`uid`=`stu_info`.`uid` where `wx_user`.`uid`={$uid}";
		$r = $wxdb->query($sql);
		if($r){
			$r=mysql_fetch_assoc($r);
			if( $r!==null )
				return $r;
			else
				return false;
		}else
			throw new OtherException(
					"获取用户绑定信息失败", 
					"服务器内部错误。T_T...");
			
	}
	
}