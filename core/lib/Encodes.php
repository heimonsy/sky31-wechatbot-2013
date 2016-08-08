<?php

class Encodes
{
	//key
	private static $key="GZQBB";
	private static $charOffset = 1;
	
	const DECODE_TAG = 0;
	const ENCODE_TAG = 1;
	
	/**
	 * 用于加密用户的学号和密码
	 */
	public static function encode($str)
	{
		$str = self::enByKey($str, self::ENCODE_TAG);
		return $str;
	}
	
	/**
	 * 用于解密用户的学号和密码
	 */
	public static function decode($str)
	{
		$str = self::enByKey($str, self::DECODE_TAG);
		return $str;
	}
	
	private static function enByKey($str , $tag)
	{
		$lstr = strlen( $str );
		$lkey = strlen( self::$key );
		$pk   = 0;
		//简单的异或加密
		for($i=0;$i<$lstr;$i++){
			if( $tag==self::ENCODE_TAG )
				$str[$i]= self::charEncode($str[$i]^self::$key[$pk++]);
			else 
				$str[$i]= self::charDecode($str[$i])^self::$key[$pk++];
			if( $pk>=$lkey ) $pk=0;
		}
		return $str;
	} 

	private static function charEncode($char)
	{
		//偏移
		return chr( ord($char)+self::$charOffset );
	}
	
	private static function charDecode($char)
	{
		//偏移
		return chr( ord($char)-self::$charOffset );
	}

}

