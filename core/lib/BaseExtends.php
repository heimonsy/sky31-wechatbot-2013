<?php
abstract class BaseExtends
{
	protected $user;
	protected $receivedMsg;
	protected $responseMsg = NULL;
	
	public function __construct($user, $receivedMsg) {
		$this->user = $user;
		$this->receivedMsg = $receivedMsg;
	}
	
	/**
	 * 分析用户的文字
	 * @param string $matchs 传递给分析函数的匹配后的数组
	 */
	public abstract function analyse($matchs="");
	
	/**
	 * 必须继承用来设置pattern
	 */
	public static function getKeyWordPatterns(){
		return "";
	}

	public function getResponseMsg() {
		return $this->responseMsg;
	}
	
	public static function setStatus($uid, $className) {
		
	}
}