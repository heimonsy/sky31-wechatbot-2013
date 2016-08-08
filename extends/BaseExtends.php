<?php
abstract class BaseExtends
{
	protected $status;
	protected $nextStatus;
	protected $user;
	protected $receivedMsg;
	protected $responseMsg;
	
	public function __construct($user, $receivedMsg, $status)
	{
		$this->status = $this->getStatus($status);
		$this->nextStatus = $this->getNextStatus($status);
		$this->user = $user;
		$this->receivedMsg = $receivedMsg;
	}
	
	public abstract function analyse();
	
	public function getMsg(){
		return $this->responseMsg;
	}
	
	public function getStatus( $status )
	{
		return substr($status, 0, 2);
	}
	
	public function getNextStatus( $status )
	{
		return substr($status, 2);
	}
}