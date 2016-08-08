<?php
class PaymentCheckExtends extends BaseExtends
{
	public static function getKeyWordPatterns() {
		return "/学费/i";
	}
	
	public function analyse($matchs=NULL) {
		
		$this->responseMsg = WxMsgFactory::setTextMsg(
				$this->receivedMsg->fromUserName, 
				$this->receivedMsg->toUserName, 
				time(), self::getPaymentCheckExtends($this->user->uid));
		return $this->responseMsg;
	}
	
	
	public static function getPaymentCheckExtends($uid) {
		$info = BindExtends::haveBind($uid);
		if($info){
			$stuNum = Encodes::decode($info['snum']);
			$pw     = $info['cwpw']==NULL ? $stuNum : Encodes::decode($info['cwpw']);
			if(preg_match("/[0-9]{4}96/", $stuNum)) 
				$content = self::getFromXX($stuNum, $pw);
			else 
				$content = self::getFromBB($stuNum, $pw);
			
			if(!$content)
				$content = "你的帐号或密码有误，财务管理系统的初始密码是学号，如果你已经更改，点此链接修改：".Oauth::getCwBindUrl($uid);
			else 
				$content = "姓名：".Encodes::decode($info['name'])."\n"."学号：".$stuNum."\n".$content; 
			return $content;

		} else 
			return $content = "您还没有绑定个人信息，点击链接进行绑定：".Oauth::getBindUrl($uid);
	}
	
	public static function getFromBB($stuNum, $pw){
		$ch = new MyUrlFetch();
		$datas = array("TxtName"=>$stuNum, "TxtPass"=>$pw);
		$ch->setPostArray($datas);
		$str = iconv("GB2312", "UTF-8",
				$ch->post("http://cwcx.xtu.edu.cn:8004/cwcx4/sf40/"));
		
		$hc = $ch->getHttpCode()."";

		if($hc[0] != 2 && $hc[0] != 3)
			throw new OtherException("财务查询系统无法访问","目前财务查询系统无法访问T_T..");
		
		if(strpos($str, "对象已移动")) {
			$str=iconv("GB2312", "UTF-8",
					$ch->get("http://cwcx.xtu.edu.cn:8004/cwcx4/sf40/FindOk.asp"));
			$str = str_replace(" ", "", replaceWarp($str));
				
			//cho $str;
			preg_match_all("/<TRclass=\"right\">(.*?)<\/TR>/", $str, $matchs);
				
			//获取卡号：
			preg_match("/帐号：([0-9]{19})/", $str, $m2);
				
			$mnums = count($matchs[1]);
			//echo $mnums;
			$pre = NULL;
			$content = "建行卡号：".$m2[1]."\n";
			for($i = $mnums-2; $i >= 0; $i--) {
				//echo $i." ";
				preg_match_all("/<TD.*?>(.*?)<\/TD>/", $matchs[1][$i], $m);
				if($pre != NULL && $pre!=$m[1][0]) break;
				if($pre==NULL) $content.='学期：'.$m[1][0]."\n";
				$content.="--------\n【".$m[1][1]."】\n";
				$content.="应交：".$m[1][2]."\n";
				$content.="实交：".$m[1][3]."\n";
				$content.="减免：".$m[1][4]."\n";
				$content.="退费：".$m[1][5]."\n";
				$content.="欠交：".$m[1][6]."\n";
				$pre = $m[1][0];
			}
			return $content."--------\n总学费为以上几项相加，在提示欠费时应尽快在绑定的建行卡上存入欠交的总学费。\n缴费状态随时可查。";
			//echo $str;
				
		} else
			return false;
	}
	
	public static function getFromXX($stuNum, $pw) {
		$ch = new MyUrlFetch();
		$datas = array("TxtName"=>$stuNum, "TxtPass"=>$pw);
		$ch->setPostArray($datas);
		$str = iconv("GB2312", "UTF-8",
				$ch->post("http://cwcx.xtu.edu.cn:8004/cwcxxx/sf40/"));
		
		$hc = $ch->getHttpCode()."";
		if($hc[0] != 2 && $hc[0] != 3)
			throw new OtherException("财务查询系统无法访问","目前财务查询系统无法访问T_T..");
		
		if(strpos($str, "对象已移动")) {
			$str=iconv("GB2312", "UTF-8",
					$ch->get("http://cwcx.xtu.edu.cn:8004/cwcxxx/sf40/FindOk.asp"));
			$str = str_replace(" ", "", replaceWarp($str));
			
			//cho $str;
			preg_match_all("/<TRclass=\"right\">(.*?)<\/TR>/", $str, $matchs);
			
			//获取卡号：
			preg_match("/帐号：([0-9]{19})/", $str, $m2);
			
			$mnums = count($matchs[1]);
			//echo $mnums;
			$pre = NULL;
			$content = "建行卡号：".$m2[1]."\n";
			for($i = $mnums-2; $i >= 0; $i--) {
				//echo $i." ";
				preg_match_all("/<TD.*?>(.*?)<\/TD>/", $matchs[1][$i], $m);
				if($pre != NULL && $pre!=$m[1][0]) break;
				if($pre==NULL) $content.='学期：'.$m[1][0]."\n";
				$content.="--------\n【".$m[1][1]."】\n";
				$content.="应交：".$m[1][2]."\n";
				$content.="实交：".$m[1][3]."\n";
				$content.="减免：".$m[1][4]."\n";
				$content.="退费：".$m[1][5]."\n";
				$content.="欠交：".$m[1][6]."\n";
				$pre = $m[1][0];
			}
			return $content."--------\n总学费为以上几项相加，在提示欠费时应尽快在绑定的建行卡上存入欠交的总学费。\n缴费状态随时可查。";
			//echo $str;
			
		} else 
			return false;
	
	}
}