<?php

class MyDB
{
	private static $wxdb = NULL;
	
	
	public static function getWxdb()
	{
		if(self::$wxdb == NULL){
			global $wxdb_config;
			self::$wxdb = new Mysql($wxdb_config);
		}
		return self::$wxdb;
	}
	
	public static function close()
	{
		if(self::$wxdb != NULL)
			self::$wxdb->close();
	} 
}