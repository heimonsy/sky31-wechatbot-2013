<?php
/**
 * 用于数据库操作的类
 * @author Heister
 * @version 1.0
 * qq 250661062
 */
//* 服务类
class WxService
{
	public $receivedMsg;
	public $responseMsg;
	public $user;

	public function __construct($postStr)
	{
		$msgFactory = new WxMsgFactory( $postStr );
		$this->receivedMsg = $msgFactory->getMsg();
		
		//获取用户对象
		$this->setUserObj();
		
		//将接收到的消息添加到数据库
		//if( ! $this->addPostMsg( $postStr ) ) echo mysql_error();
		$mid = $this->addPostMsg( $postStr );
		
		if( $this->user->fakeid ==0 && $this->receivedMsg->msgType==MSG_TEXT ){
			///echo "fff";
			$ch = curl_init( "http://wx.sky31.com/getuserinfo.php?mid=".$mid );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			$str = curl_exec($ch);
			//echo $str;
			//$fp = fsockopen('http://wx.sky31.com', 80, $errno, $errmsg);
			//fputs($fp, "GET /getuserinfo.php?mid=".$mid."\r\n\r\n");
			//echo $errno." ".$errmsg;
			//fclose($fp);
		}
	}

	public function addPostMsg($postStr)
	{
		$wxdb = MyDB::getWxdb();
		
		$postStr=addslashes($postStr);
		$sql="INSERT INTO `wx_msg_rec` (`id`, `fuid`, `mtype`,`fc`, `time`)VALUES(
			null,
			{$this->user->userId},
			'{$this->receivedMsg->msgType}',
			'{$postStr}',
			'{$this->receivedMsg->time}'
		)";
		$wxdb->query($sql);
		return mysql_insert_id($wxdb->link);
	}

	public function setUserObj()
	{
		$this->user = new WxUser($this->receivedMsg->fromUserName);
		//如果没有获取到用户信息
		if( !$this->user->getUserInfo() )
		{
			$this->user->addUser($this->receivedMsg->fromUserName);
			$this->user->getUserInfo();
		}

	}

	/**
	 * 分析并获取返回的信息
	 */
	public function setResponseMsg()
	{
		//分析
		$analyse = new BaseStatusExtends($this->user, $this->receivedMsg, $this->user->status);
		$analyse->analyse();
		$this->responseMsg = $analyse->getMsg();
	}

	/**
	 * 发送信息
	 */
	public function sendMsg()
	{
		echo $this->responseMsg;
	}

	/**
	 * 立即发送消息
	 * 
	 */
	function sendMsgImdtly($fakeid, $content)
	{
		$ch=loginToWxMp();
		//curl_setopt($ch, CURLOPT_POST,    true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('type'=>1,'content'=>$content,'error'=>'false','tofakeid'=>"$fakeid",'ajax'=>1));
		curl_setopt($ch, CURLOPT_URL, "http://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&lang=zh_CN");
		$str = curl_exec($ch);
		$str = json_decode($str);
		//print_r($str);
		return $str->msg=='ok' ? true : false;
	}

	/**
	 *
	 *
	 */
	function mySendMsg($user, $receivedMsg ,$content)
	{
		return $this->sendMsgImdtly($user->fakeid, $content );
	}

}
