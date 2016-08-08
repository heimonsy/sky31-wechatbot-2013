<?php
/**
 * 
 * @author Heister
 *
 */
class CURDException extends Exception
{
	public $msgToUser;
	public function __construct($msg, $msgToUser, $mysql_error) {
		parent::__construct($msg." , mysql_error:".$mysql_error);
		$this->msgToUser = $msgToUser;
	}
	
	/**
	 * 反馈给用户的提示信息
	 * @return $str
	 */
	public function getMsgToUser() {
		return $this->msgToUser;
	}
}