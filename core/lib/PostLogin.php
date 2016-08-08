<?php

class PostLogin
{
	private static $errorInfo;
	
	public static function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	public static function loginToStuM($userName,$password)
	{
		//$cookieFile = $userName.".txt";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR,  "");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "");
		$fields="username=$userName&password=$password&identity=student";
		curl_setopt($ch, CURLOPT_REFERER,    "http://202.197.224.134:8083/jwgl/login.jsp");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_URL, "http://202.197.224.134:8083/jwgl/logincheck.jsp");
		curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE)."";
		//echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($code[0]!='2')
			throw new OtherException("无法访问教务管理系统,httpcode:".$code,"教务管理系统奔溃了。。T_T...");
		
		curl_setopt($ch, CURLOPT_URL, "http://202.197.224.134:8083/jwgl/index1.jsp");
		curl_exec($ch);
		//$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return $ch;
	}
	
	public static function getStuName( $userName, $password )
	{
		$ch = self::loginToStuM($userName, $password);
		curl_setopt($ch, CURLOPT_URL, "http://202.197.224.134:8083/jwgl/index1.jsp");
		$str = curl_exec($ch);
		$str = iconv("GB2312","UTF-8",$str);
		$nameReg="/<font color=red>(.*?)(老师|同学)/";
		if(preg_match($nameReg, $str, $matchs))
			return $matchs[1];
		else
			return false;
	}
	
	public static function getKb($userName,$password)
	{
		$ch = self::loginToStuM($userName, $password);

		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_URL, "http://202.197.224.134:8083/jwgl/xk/xk1_kb_gr.jsp?xq1=01");
		$str = curl_exec($ch);
		$str=iconv("GB2312","UTF-8",$str);
		
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE );
		if( $code!=200 )
			return false;
		
		curl_close($ch);
		return self::analyseKb($str);
	}
	
	private static function analyseKb($str)
	{
		if( preg_match("/登录超时|重新登录/", $str) || $str=="" )
			return false;
		
		$info=array();
		$str = str_replace("\n", "", $str);
		$str = str_replace("\r", "", $str);
		$str = str_replace(" ", "", $str);
		//获取姓名
		preg_match("/姓名:(.*?)<\/font>/i", $str,$match);
		$info['name'] = trim($match[1]);
		
		//获取每节课程时段的'name'
		$str = str_replace("<tdvalign=top></td>", "<tdvalign=top><tablewidth=100%border=0cellpadding=0cellspacing=0></table></td>", $str);
		preg_match_all("/<tdvalign=top>(.*?)<\/table><\/td>/", $str ,$match);
		$match=$match[0];
		$mnums = count($match);
		if( $mnums!=35 )
			return false;
	
		for($index=0;$index<35;$index++){
			$wd = $index%7;
			$cn = (int)($index/7);
			preg_match_all("/<tablewidth=100%border=0cellpadding=0cellspacing=0>(.*?)<\/table>/i", $match[$index]."</table>", $smatch);
			$smatch=$smatch[1];
			foreach( $smatch as $key=>$v ){
				if( $v!="" ){
					$v=str_replace("colspan=2", "", $v);
					preg_match_all("/<td>(.*?)<\/td>/i", $v, $vm);
					$vm=$vm[1];
					$info['class'][$wd][$cn][$key]['name']=$vm[0];
					$info['class'][$wd][$cn][$key]['place']=$vm[1];
					$info['class'][$wd][$cn][$key]['tname']=$vm[2];
					$info['class'][$wd][$cn][$key]['week']=$vm[3];
				}else{
					$info['class'][$wd][$cn]=array();
				}
			}
		}
		return $info;
	}
	
	
	public static function lastErrorInfo() {
		return self::errorInfo;
	} 
	
}

