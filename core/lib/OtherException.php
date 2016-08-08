<?php

/**
 * 
 * @author Heister
 *
 */
class OtherException extends Exception
{
	public $msgToUser;
	public function __construct($msg, $msgToUser) {
		parent::__construct($msg);
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