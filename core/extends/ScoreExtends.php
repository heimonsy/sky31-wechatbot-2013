<?php

class ScoreExtends extends BaseExtends
{
	
	public static function getKeyWordPatterns() {
		return "/^成绩|成绩查询|期末成绩|查分|查分数|查成绩$/i";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BaseExtends::analyse()
	 */
	public function analyse($matchs="") {
		
		$content = $this->getScore($this->user->uid);
		
		$this->responseMsg = WxMsgFactory::setTextMsg(
				$this->receivedMsg->fromUserName,
				$this->receivedMsg->toUserName, 
				time(), $content
			);
	}

	function getScore($uid){
		$wxdb = MyDB::getWxdb();
		if(Oauth::haveBind($uid)) {
			$bind = Oauth::haveBind($uid);
			$user = Encodes::decode($bind['snum']);
			$pw   = Encodes::decode($bind['pw']);
			
			$ch = new MyUrlFetch();
			
			$fields = array(
					'username'=>$user, 'password'=>$pw,
					'identity'=>'student', 'role'=>'1');
			$ch->setPostArray($fields);
			$str = iconv("GB2312", "UTF-8",
					$ch->post("http://202.197.224.134:8083/jwgl/logincheck.jsp"));
			$code = $ch->getHttpCode()."";
			if($code[0]=='2') {
				if(strpos($str, "密码错误") == FALSE) {
					$ch->post("http://202.197.224.134:8083/jwgl/index1.jsp");
					$str = iconv("GB2312", "UTF-8",
							$ch->get("http://202.197.224.134:8083/jwgl/cj/cj1_cjLiebiao.jsp?xq=0&xkjc=&type=null&xkdl2=&xh={$user}&bh=null"));
					$str = str_replace("\n", "", $str);
					$str = str_replace("\r", "", $str);
					$str = str_replace(" ", "", $str);
					$str = str_replace("&nbsp;", "", $str);
					$str = str_replace("style=\"color:red\"", "", $str);
					
					$r = preg_match_all("/<tr>(.*?)<\/tr>/u", $str, $matchs);
					$matchs = $matchs[1];
					$str = "翼宝为你查询到的成绩：\n\n";
					$count = count($matchs);
					$pre = -1;
					for($i = $count-1; $i >=0; $i--){
						preg_match_all("/<td>(.*?)<\/td>/u", $matchs[$i], $m);
						$m = $m[1];
						if( $pre!=-1 && ($pre!=intval($m[6]) || $m[6]=="" ) ) break;
						else $pre = intval($m[6]);
							
						$str.=($m[0]."【".substr($m[1],0,6)."】\n平时：".$m[3]." 考试：".$m[4]." 期评：".$m[5]."\n------------\n");
					}
					$oauth = Oauth::getOauth($uid);
					return $str."<a href=\"".WX_USER_URL."course/score.php?oa=$oauth\">点此查看排名</a>";
					
				} else 
					return '密码错误哦（如果确定没有改过密码的话，可能是教务管理系统崩溃了T_T..）。\n如果已经改了密码，点击链接重新绑定：'.Oauth::getBindUrl($uid);
				
			} else 
				throw new OtherException(
					"无法连接教务管理系统,http code:".$code,
					"教务管理系统奔溃了~~~, 请稍后再试");
			
		}else
			return "您还没有绑定个人信息。\n点此绑定个人信息：".Oauth::getBindUrl($this->user->uid)." 进行绑定。\n绑定成功后回复 成绩 可以查询成绩哦。;";
	}
	
}//class

