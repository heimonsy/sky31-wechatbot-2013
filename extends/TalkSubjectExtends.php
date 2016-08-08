<?php
class TalkSubjectExtends extends BaseExtends   
{
	public function analyse()
	{
		
	}
	
	public static function insertTalkSubject( $user, $recevedMsg , $word)
	{
		$word = str_replace(" ", "", $word);
		$wxdb = MyDB::getWxdb();
		
		if( $word=="" ) return "您输入的话题没有内容";
		
		$sid = self::haveSubject($word);
		if( !$sid ){
			return "您发表的话题不存在。当前热门话题：#红歌会#。";
			//
			$sql = "insert into `wx_subject` 
					(`sid`, `word`) 
					values
					(null, '{$word}')";
			$r = $wxdb->query($sql);
			if( !$r ) return "内部错误".mysql_error();
			$sid = mysql_insert_id( $wxdb->link );
		}
		
		$sql = "insert into `wx_rsubmsg`
				(`rsid`, `sid`, `uid`, `cnt`,`time`)
				values
				(null, '{$sid}','{$user->userId}', '{$recevedMsg->content}', '{$recevedMsg->time}')";
		$r = $wxdb->query($sql);
		
		if( !$r )
			return "内部错误".mysql_error();
		else
			return "话题发表成功！";
		
	}
	
	public static function haveSubject( $word )
	{
		$wxdb = MyDB::getWxdb();
		$sql = "select `sid` from `wx_subject` where `word`='{$word}'";
		$r   = $wxdb->query($sql);
		if( !$r ) echo mysql_error();
		$r   = mysql_fetch_assoc( $r );
		if( $r==NULL )
			return false;
		else 
			return $r['sid'];
		
	}
}