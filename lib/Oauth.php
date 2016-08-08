<?php


class Oauth
{
	
	public static function getOauth( $uid )
	{
		
		if( ($oa=self::haveOauth($uid))==false ){
			$oa = self::encode( $uid );
			$sql = "insert into `wx_oauth` (`oid`,`uid`, `oauth`) values (null, '{$uid}', '{$oa}')";
			$wxdb = MyDB::getWxdb();
			$r = $wxdb->query($sql);
			if( !$r ) echo mysql_error();
			return $oa;
		}
		return $oa;
	}
	
	public static function haveOauth( $uid )
	{
		$wxdb = MyDB::getWxdb();
		$sql  = "select `oauth` from `wx_oauth` where `uid`={$uid}";
		$r = $wxdb->query($sql);
		
		if( !$r ) ErrorListen::throwsError( __LINE__ , mysql_error());
		$r = mysql_fetch_assoc($r);
		if( $r==null )
			return false;
		else
			return $r['oauth'];
		
	}
	
	private static function encode($uid)
	{
		return md5( $uid.OAUTH_KEY.time() );
	}
	
	public static function valid($oauth) 
	{
		$wxdb = MyDB::getWxdb();
		$sql = "select `uid` from `wx_oauth` where `oauth`='{$oauth}'";
		$r = $wxdb->query($sql);
		if( $r ){
			if( mysql_num_rows($r)==1 ){
				$r=mysql_fetch_assoc($r);
				return $r['uid'];
			}else return false;
		}else return false;
	}
	
	/**
	 * 判断一个oauth信息字符是否合法
	 * @param string $oauth
	 * @return boolean
	 */
	public static function isOauth($oauth)
	{
		$l = strlen($oauth);
		
		if($l<32  ){
			return false;
		}else{
			$flag = true;
			for($i=0;$i<$l && $flag;$i++  )
				if( $oauth[$i]==' ' ) $flag=false;
			return $flag;
		}
	} 
	
	public static function haveBind($uid)
	{
		$wxdb = MyDB::getWxdb();
		
		$sql = "select `nname`,`snum`,`name`,`pw` from `wx_user` inner join `stu_info` ON `wx_user`.`uid`=`stu_info`.`uid` where `wx_user`.`uid`={$uid}";
		$r = $wxdb->query($sql);
		if( $r ){
			$r=mysql_fetch_assoc($r);
			if( $r!==null )
				return $r; 
			else
				return false;
		}else return false;
	}
}