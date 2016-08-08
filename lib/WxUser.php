<?php
/**
 * 用于用户信息操作的类
 * @author Heister
 * @version 1.0
 * qq 250661062
 */
class WxUser
{
	public $userName;
	public $userId;
	public $status;
	public $fakeid;
	public $nickName;
	public $pid;
	
	public $stuNum=null;
	public $pw    =null;
	public $name  =null;

	public function __construct($userName)
	{
		$this->userName =$userName;
	}

	/*
	 * 获取用户的id和status
	 */
	public function getUserInfo()
	{
		$res = false;

		$wxdb = MyDB::getWxdb();
		
		$sql = "SELECT * FROM `wx_user` WHERE `uname`='{$this->userName}';";
		$r   = $wxdb->query($sql);
		if( $r )
		{
			if( mysql_num_rows($r)!=0 )
			{
				$r= mysql_fetch_assoc($r);
				$this->userId = $r['uid'];
				$this->status = $r['stat'];
				$this->nickName = $r['nname'];
				$this->fakeid   = $r['fid'];
				$this->pid   = $r['pid'];
				$res=true;
			}

		}

		return $res;
	}
	
	function setStatus( $status )
	{
		$wxdb = MyDB::getWxdb();
		$sql = "UPDATE  `wx_user` SET `stat`='{$status}' WHERE `uid`={$this->userId} ";
		$wxdb->query($sql);
	}

	public static function addUser($userName)
	{
		$wxdb = MyDB::getWxdb();
		$time = time();
		$sql = "INSERT INTO `wx_user` (`uid`,`uname`,`stat`, `time`) VALUES (null, '{$userName}', '".STAT_BASE."',$time) ";
		$r   = $wxdb->query( $sql );
		//echo mysql_error();
	}

	function updateFakeid($fakeid, $nickName)
	{
		$wxdb = MyDB::getWxdb();
		$this->fakeid = $fakeid;
		$this->nickName = $nickName;
		$sql = "update `wx_user` set `fakeid`='{$fakeid}',`nnam`='{$nickName}' where `uid`={$this->userId}";
		return $wxdb->query($sql);
	}
	
	function getStuInfo()
	{
		$wxdb = MyDB::getWxdb();
		
		$sql = "select * from `stu_info` where `uid`={$this->userId}";
		$r = $wxdb->query($sql);
		
		if( $r ){
			if(mysql_num_rows($r)==0)
				return false;
			$r = mysql_fetch_assoc($r);
			$this->stuNum = $r['snum'];
			$this->name   = $r['name'];
			$this->pw     = $r['pw'];
			return true;
		}else 
			echo mysql_error();
	}
	
	function getPicUrl()
	{
		//echo $this->pid;
		if( $this->pid==0 ) return false;
		
		$sql = "select `url` from `wx_pic` where `pid`='{$this->pid}'";
		$wxdb=MyDB::getWxdb();
		$r = $wxdb->query($sql);
		$r = mysql_fetch_assoc($r);	
		if( $r==NULL )
			return false;
		else 
			return $r['url'];
	}
	
	function update( $keyName, $value )
	{
		$wxdb = MyDB::getWxdb();
	}
}
