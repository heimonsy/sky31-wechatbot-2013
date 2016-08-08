<?php

class MyDB
{
	private static $wxdb;
	
	public static function setWxdb($config){
		
		self::$wxdb = new Mysql($config);
	}
	
	public static function getWxdb(){
		return self::$wxdb;
	}
	
	function __destruct(){
		self::$wxdb=NULL;
	}
}