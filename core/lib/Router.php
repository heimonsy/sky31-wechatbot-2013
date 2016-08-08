<?php
class Router {
	public $receivedMsg;
	public $user;
	public $responseMsg = NULL;
	
	public function __construct($user, $receivedMsg) {
		$this->receivedMsg  = $receivedMsg;
		$this->user = $user;	
	}
	
	/**
	 * 进行路由选择的函数
	 * @param array 路由表，数组，元素为函数
	 */
	public function router($routers) {
		$matchs = array();
		//先判断用户所在的状态
		if($this->user->status !=""){
			$extends = $this->user->status;
			
			if(class_exists($extends)){
				$todo = new $extends($this->user, $this->receivedMsg);
				$todo->analyse();
				$this->responseMsg = $todo->getResponseMsg();
				
			} else {
				$this->user->setStatus("");
				throw new OtherException(
						"用户状态的类不存在！ status:".$this->user->status, 
						"你的状态有误，请重试。");
			}
			
		} else {
			foreach ($routers as $extends)
			if(class_exists($extends)) {
				if(preg_match(
					$extends::getKeyWordPatterns(), $this->receivedMsg->content, $matchs)) {
					//匹配到模式串
					$todo = new $extends($this->user, $this->receivedMsg);
					//开始分析
					$todo->analyse($matchs);
					$this->responseMsg = $todo->getResponseMsg();
					break;
				}
			} else 
				throw new OtherException(
						"路由表有错误！ 找不到$extends ！",
						NULL);
		}
		return $this->responseMsg;
	}
	
}