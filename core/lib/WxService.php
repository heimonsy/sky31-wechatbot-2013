<?php
/**
 * 用于服务操作的主类
 * @author Heister
 * @version 1.0
 * qq 250661062
 */
class WxService
{
	public $receivedMsg;
	public $responseMsg = NULL;
	public $user;
	public $receivedMsgMid;
	public $uid;

	public function __construct($postStr)
	{
		$this->receivedMsg = WxMsgFactory::loadFromXml($postStr);
		WxMsgFactory::$receivedMsg = $this->receivedMsg;
		
		//获取用户对象
		$this->setUserObj();
		
		//将接收到的消息添加到数据库
		$mid = $this->addPostMsg($postStr);
		$this->receivedMsgMid = $mid;

		if($this->user->fakeid == 0 && $this->receivedMsg->msgType == MSG_TEXT) {
			//$ch = curl_init( "http://wx.sky31.com/getuserinfo.php?mid=".$mid );
			//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
			//curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			//$str = curl_exec($ch);
			//echo $str;
			$fp = fsockopen('wxuser.sky31.com', 80, $errno, $errmsg);
			$cnt = "GET /cgi_bin/getuserinfo.php?mid=".$mid." HTTP/1.1\r\n";
			$cnt.= "Host: wxuser.sky31.com\r\n\r\n";
			fputs($fp, $cnt);
			
			//fputs($fp, "");
			//echo $errno." ".$errmsg;
			fclose($fp);
		}
	}

	/**
	 * 将用户的信息收集到数据库中（最好人工定时清理）
	 * @param str 接受到xml信息信息
	 * @return number 插入的id
	 */
	public function addPostMsg($postStr)
	{
		$wxdb = MyDB::getWxdb();
		
		$postStr = addslashes($postStr);
		$sql="INSERT INTO `wx_msg_rec` (`id`, `fuid`, `mtype`,`fc`, `time`)VALUES(
			null,
			{$this->user->uid},
			'{$this->receivedMsg->msgType}',
			'{$postStr}',
			'{$this->receivedMsg->time}'
		)";
		$wxdb->query($sql);
		return mysql_insert_id($wxdb->link);
	}
	
	
	/**
	 * 根据接受到的信息获取用户的对象
	 * @return boolean 总是返回true;
	 */
	public function setUserObj()
	{
		$this->user = new WxUser($this->receivedMsg->fromUserName);
		//如果没有获取到用户信息
		if( !$this->user->getUserInfo() )
		{
			$this->user->addUser($this->receivedMsg->fromUserName);
			$this->user->getUserInfo();
		}
		return true;
	}

	/**
	 * 分析并获取返回的信息
	 * @return boolean 获取消息是否成功
	 */
	public function setResponseMsg()
	{
		global $ROUTER_TABLE;
		$router = new Router($this->user, $this->receivedMsg, $this->user->status);
		$this->responseMsg = $router->findRouter($ROUTER_TABLE);
		
		return $this->receivedMsg == NULL ? false : true;
	}

	/**
	 * 发送信息
	 * @return boolean 总是返回true;
	 */
	public function returnMsg()
	{
		echo $this->responseMsg;
		return true;
	}

	/**
	 * 通腾讯的管理平台立即发送消息
	 * @param User 用户类
	 * @param string 发送的内容
	 */
	public static function sendMsg($user, $content)
	{
		if($user->fakeid == "") {
			ErrorLogs::writeToFile(
				$user->uid, __FILE__, __LINE__, "立即发送信息，但是用户的fakeid不存在",
				ErrorLogs::$fatalError);
			return false;
		}
			
		$ch = loginToWxMp();
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('type'=>1,'content'=>$content,'error'=>'false','tofakeid'=>"$user->fakeid",'ajax'=>1));
		curl_setopt($ch, CURLOPT_URL, "http://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&lang=zh_CN");
		$str = curl_exec($ch);
		$str = json_decode($str);

		return $str->msg == 'ok' ? true : false;
	}

}
