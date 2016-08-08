<?php
class TakeAwayFuncs
{
	public static function getAllSell()
	{
		$wxdb = MyDB::getWxdb();
		
		$sql = "select * from `take_area` order by `sort`";
		$res = $wxdb->query($sql);
		while( ($m=mysql_fetch_assoc($res))!=NULL ){
			$sql  = " select * from `takeaway` where `aid`='{$m['id']}' order by `sort` limit 2";
			$r = $wxdb->query( $sql );
			while( ($mm=mysql_fetch_assoc($r))!=NULL ){
				$reaArr[$m['name']][]=array( 'name'=>$mm['name'], 'tel'=>$mm['tel'],'adr'=>$mm['adr']  );
			}
		}
		$resStr="";
		foreach ($reaArr as $name => $v) {
			$resStr.='【'.$name.'】'.":\n";
			foreach ($v as $vv) {
				$resStr.=$vv['name']."\n";
				$resStr.="tel:".$vv['tel']."\n";
			}
			$resStr.="-------------\n";
		}
	
		return $resStr;
	}
	
	public static function getTakeListByAreaName($name)
	{
		$wxdb = MyDB::getWxdb();
		$id = self::getAidByAname($name);
		
		$sql = "select * from `takeaway` where `aid`='{$id}' order by `sort` ";
		$r = $wxdb->query($sql);
		$str = "";
		while( ($m=(mysql_fetch_assoc($r)))!=NULL ){
			$str .= $m['name']."\n";
			$str .= 'tel:'.$m['tel']."\n";
		}
		return $str;
	}
	
	public static function getAidByAname($name)
	{
		$wxdb = MyDB::getWxdb();
		
		$sql = "select `id` from `take_area` where `name`='{$name}'";
		$r = $wxdb->query($sql);
		$r = mysql_fetch_assoc($r);
		return $r['id'];
	}
}