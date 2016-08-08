<?php

class ErrorLogs
{
	const FATAL_ERROR = 1;
	const WARRING = 0;
	
	public static $errorNums = 0;
	private static $lastErrorInfo = "";
	
	/**
	 * 将错误写入数据库中
	 * @param WxUser $user
	 * @param String $file
	 * @param String $line
	 * @param String $msg
	 * @param int $errorCode
	 * @return boolean
	 */
	public static function writeToLog($uid, $file, $line, $msg, $errorCode) {
		if($errorCode==self::FATAL_ERROR) self::$errorNums++;
		
		self::$lastErrorInfo = "$uid , $file, $line, $msg, $errorCode";
		$file  = addslashes($file);
		
		$time = time();
		$sql =  "insert into `wx_logs` values(NULL, '{$uid}', '{$file}', '{$line}', '{$msg}', '{$errorCode}', '{$time}')";
		$db = MyDB::getWxdb();
		$r  = $db->query($sql);
		if(!$r) self::writeToFile($uid, $file, $line, $msg, $errorCode, mysql_error()." sql: ".$sql);
		
		return $r == false ? false : true;
	}
	/**
	 * 当连接不上数据库的时候（也就是writeToLog不起作用的时候），将错误写入文件中
	 * @param int $uid
	 * @param string $file
	 * @param string $line
	 * @param string $msg
	 * @param int $errorCode
	 */
	public static function writeToFile($uid, $file, $line, $msg, $errorCode, $sqlerror) {
		$f = fopen(BASE_PATH."/logs/".self::getDate().".php", "a+");
		fwrite($f, "$uid , $file, $line, $msg, $errorCode, $sqlerror\r\n");
		fclose($f);
	}
	
	public static function getDate(){
		return date("Y-m-d");
	}
	
	/**
	 * 获取上一次调用writeToLog的信息
	 * @return string
	 */
	public static function getLastErrorInfo() {
		return self::$lastErrorInfo;
	}
	
	/**
	 * 判断是否在执行的过程中产生了错误
	 * @return boolean
	 */
	public static function noError() {
		return self::$errorNums == 0 ? true : false;
	}
}