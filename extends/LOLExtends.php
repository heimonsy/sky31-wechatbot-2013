<?php
class LOLExtends
{
	private static $serverMap = 
	array( '艾欧尼亚'=>'电信一', '祖安' =>'电信二', '诺克萨斯' => '电信三' ,
		   '班德尔城'=>'电信四', '皮尔特沃夫'=>'电信五', '战争学院'=>'电信六',
		   '巨神峰'=>'电信七', '雷瑟守备'=>'电信八', '裁决之地'=>'电信九',
		   '黑色玫瑰'=>'电信十', '暗影岛'=>'电信十一', '钢铁烈阳'=>'电信十二',
		   '均衡教派'=>'电信十三', '水晶之痕'=>'电信十四',
		   '影流'=>'电信十五' ); 
	//
	//http://lolbox.duowan.com/playerDetail.php?serverName=电信十五&playerName=明明明砍人
	public static function getUserInfo($serverName, $playerName)
	{
		$url = 'http://lolbox.duowan.com/playerDetail.php?serverName='
				.urlencode($serverName).'&playerName='.urlencode($playerName);
	
		$ch = curl_init( $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$str = curl_exec($ch);
		if( $str=='' )
			return false;
		else
			return $str;
	}
}


echo LOLExtends::getUserInfo("影流", "kjflsjlsdfsdjkf");

