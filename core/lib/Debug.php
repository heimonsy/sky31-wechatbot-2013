<?php

class Debug
{
	public static $start;
	
	public static function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	public static function start()
	{
		self::$start = self::microtime_float();
	}
	
	public static function echoTimes()
	{
		echo " \n".self::microtime_float()-self::$start."\n ";
	}
	
	public static function usedTimes() {
		return self::microtime_float()-self::$start;
	}
}