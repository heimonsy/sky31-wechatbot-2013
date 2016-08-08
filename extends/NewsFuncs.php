<?php
class NewsFuncs
{
	/**
	 * 从数据库中获取今日新闻的消息，返回数组
	 * @access public
	 * @return array $res
	 */
	public static function getTodayNews()
	{
		//global $web_config;
		$wxdb = MyDB::getWxdb();
		
// 		$sql = "select `id`,`title`,`pic_url`,`description`,`src` from `wx_newslist` where `is_today`=true order by `sort`";
// 		$r   = $wxdb->query($sql);
// 		$res = array();
// 		while( ($m=mysql_fetch_assoc($r))!=NULL ){
// 			$m['pic_url'] = $web_config['host'].$m['pic_url'];
// 			$res[] = $m;
// 		}
// 		return $res;
		$sql = "select `json` from `news_today`";
		$r = $wxdb->query($sql);
		$r = mysql_fetch_assoc($r);
		$json = json_decode( $r['json'] );
		
		$l = count( $json ); 
		for($i=0;$i<$l;$i++){
			$res[$i]['title'] = html_entity_decode($json[$i]->title, ENT_COMPAT | ENT_HTML401, 'UTF-8');
			$res[$i]['pic_url']=$json[$i]->imgURL;
			if( $l<1 ) 
				$res[$i]['description'] = $json[$i]->desc;
			else
				$res[$i]['description'] = "";
			$res[$i]['src'] = $json[$i]->url;
		}
		return $res;
	}
	
}