<?php
/**
 * @desc 用于在SAE，BAE等环境中抓去数据，可以模拟登录，支持cookies
 * @author   heimonsy(heimonsy@gmail.com)
 * @version  1.0
 */

class XtuStu
{
	private $fetch;
	
	private $stu_num;
	
	private $stu_pw;
	
	public function __construct($stu_num, $stu_pw) {
		import("@.ORG.HeFetchUrl");
		$this->fetch   = new HeFetchUrl();
		//import("@.ORG.MFetchUrl");
		//$this->fetch   = new MFetchUrl();
		
		$this->stu_num = $stu_num;
		$this->stu_pw  = $stu_pw;
		
		$res = $this->_login_to_stu();
		if($res['ret'] != 0)
			throw new Exception($res['info'], $res['ret']);
	}
	
	
	/**
	 * 登录学生管理平台
	 * @return string
	 */
	private function _login_to_stu() {
		
		$this->fetch->get("http://202.197.224.134:8083/jwgl/login.jsp");
		
		$fields = array(
			'username'=>$this->stu_num, 'password'=>$this->stu_pw,
			'identity'=>'student', 'role'=>'1');
		$this->fetch->set_post_data($fields);
		
		$response = iconv("GBK", "UTF-8//IGNORE",
				$this->fetch->post("http://202.197.224.134:8083/jwgl/logincheck.jsp"));
		$http_code = $this->fetch->get_http_code()."";
		if($http_code[0]==2) {
			if(strpos($response, "密码错误") == FALSE) {
				$this->fetch->post("http://202.197.224.134:8083/jwgl/index1.jsp");
				$res['ret']  = 0;
				$res['info'] = "OK";
			} else {
				$res['ret']  = 1;
				$res['info'] = "帐号或密码错误。也可能是教务管理系统挂掉了，可以再试一下T_T..";
			}
		} else {
			$res['ret']  = 1;
			$res['info'] = "无法访问学校网站。可能是学校网站挂掉了T_T..";
		}
		return $res;
	}
	
	/**
	 * @desc 获取课程表
	 * @return array("ret", "info", ["course"])
	 */
	public function get_course() {
		$response = iconv("GBK", "UTF-8//IGNORE",
			$this->fetch->get("http://202.197.224.134:8083/jwgl/xk/xk1_kb_gr.jsp?xq1=01"));
		
		if(empty($response) || preg_match("/登录超时|重新登录/", $response))
			throw new Exception("登录失败！"."可能是学校服务器挂掉了T_T...", 1);
		
		$info=array();
		$str = str_replace("\n", "", $response);
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
		
		for($index=0;$index<35;$index++) {
			$wd = $index%7;
			$cn = (int)($index/7);
			preg_match_all("/<tablewidth=100%border=0cellpadding=0cellspacing=0>(.*?)<\/table>/i", $match[$index]."</table>", $smatch);
			$smatch=$smatch[1];
			foreach( $smatch as $key=>$v ) {
				if( $v!="" ) {
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
	
	/**
	 * @desc 获取学生的分数
	 */
	function get_score() {
		//echo "cookies:";
		//var_dump($this->fetch->get_cookies());
		
		$str = iconv("GB2312", "UTF-8",
			$this->fetch->get("http://202.197.224.134:8083/jwgl/cj/cj1_cjLiebiao.jsp?xq=0&xkjc=&type=null&xkdl2=&xh={$this->stu_num}&bh=null"));
		$str = str_replace("\n", "", $str);
		$str = str_replace("\r", "", $str);
		$str = str_replace(" ", "", $str);
		$str = str_replace("&nbsp;", "", $str);
		$str = str_replace("style=\"color:red\"", "", $str);
			
		$r = preg_match_all("/<tr>(.*?)<\/tr>/u", $str, $matchs);
		if($r===0 || $r===false)
			throw new Exception("无法获取课表:". $str, 1);
		$matchs = $matchs[1];
		$max = 0;
		$scores = array();
		foreach($matchs as $rs) {
			preg_match_all("/<td>(.*?)<\/td>/u", $rs, $m);
			$m = $m[1];
			if(!is_numeric($m[6])) continue;
			$xq = intval($m[6]);
			$max>=$xq || $max=$xq;
			$sname = $m[0];	
			$scores[$xq][]= array(
				'sname'=> $sname,
				'type' => $m[1],
				'xf'   => $m[2],
				'ps'   => $m[3],
				'ks'   => $m[4],
				'qp'   => $m[5],
				'xq'   => $xq
			);
		}
		
		return $scores[$max];
	}
	
	/**
	 * @desc 获取排名信息
	 */
	public function get_rank(){
		$year = intval(date("Y"));
		$month =  intval(date("n"));
		if( $month <7 ) $xq = ($year-1)."02";
		else $xq = $year."01";
		
		$response = iconv("GB2312", "UTF-8",
			$this->fetch->get("http://202.197.224.134:8083/jwgl/cj/cj1_paiming.jsp?xq1={$xq}&xh={$this->stu_num}"));
		
		$str = str_replace("\n", "", $response);
		$str = str_replace("\r", "", $str);
		$str = str_replace(" ", "", $str);
		$len = preg_match_all('/<td>(.*?)<\/td>/', $str, $matchs);
		$rank = array();
		for($i=0; $i<$len; $i+=2) {
			$rank[]=array(
				'name'=>$matchs[1][$i],
				'value'=>$matchs[1][$i+1]
			);
		}
		
		return $rank;
	}
	
	public static function getPaymentCheckExtends($stuNum, $pw) {
		if(preg_match("/[0-9]{4}96/", $stuNum)) 
			$data = self::getFromXX($stuNum, $pw);
		else 
			$data = self::getFromBB($stuNum, $pw);
			
		return $data;
	}
	
	/**
	 */
	private static function getFromBB($stuNum, $pw){
		$ch = new HeFetchUrl(); 
		$ch->setJump(false);
		$datas = array("TxtName"=>$stuNum, "TxtPass"=>$pw);
		$ch->set_post_data($datas);
		$str = iconv("GB2312", "UTF-8",
				$ch->post("http://cwcx.xtu.edu.cn:8004/cwcx4/sf40/"));
		
		$hc = $ch->get_http_code()."";

		if($hc[0] != 2 && $hc[0] != 3)
			throw new Exception("财务查询系统无法访问");
		
		if(strpos($str, "对象已移动")) {
			$data = array(
				'ret'=>0,
				'info'=>array(
					'card'=>'',
					'list'=>array()
					)
			);
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
			//$content = "建行卡号：".$m2[1]."\n";
			$data['info']['card']=$m2[1];
			for($i = $mnums-2; $i >= 0; $i--) {
				//echo $i." ";
				preg_match_all("/<TD.*?>(.*?)<\/TD>/", $matchs[1][$i], $m);
				if($pre != NULL && $pre!=$m[1][0]) break;
				/*
				if($pre==NULL) $content.='学期：'.$m[1][0]."\n";
				$content.="--------\n【".$m[1][1]."】\n";
				$content.="应交：".$m[1][2]."\n";
				$content.="实交：".$m[1][3]."\n";
				$content.="减免：".$m[1][4]."\n";
				$content.="退费：".$m[1][5]."\n";
				$content.="欠交：".$m[1][6]."\n";
				*/
				$data['info']['list'][]=array(
					'xq' => $m[1][0],
					'xm' => $m[1][1],
					'yj' => $m[1][2],
					'sj' => $m[1][3],
					'jm' => $m[1][4],
					'tf' => $m[1][5],
					'qj' => $m[1][6]
				);
				$pre = $m[1][0];
			}
			return $data;
			//echo $str;
				
		} else
			throw new Exception("登录失败！原因未知。。", 1);
	}
	
	/**
	 */
	private static function getFromXX($stuNum, $pw){
		$ch = new HeFetchUrl(); 
		$ch->setJump(false);
		$datas = array("TxtName"=>$stuNum, "TxtPass"=>$pw);
		$ch->set_post_data($datas);
		$str = iconv("GB2312", "UTF-8",
				$ch->post("http://cwcx.xtu.edu.cn:8004/cwcxxx/sf40/"));
		
		$hc = $ch->get_http_code()."";

		if($hc[0] != 2 && $hc[0] != 3)
			throw new Exception("财务查询系统无法访问");
		
		if(strpos($str, "对象已移动")) {
			$data = array(
				'ret'=>0,
				'info'=>array(
					'card'=>'',
					'list'=>array()
					)
			);
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
			//$content = "建行卡号：".$m2[1]."\n";
			$data['info']['card']=$m2[1];
			for($i = $mnums-2; $i >= 0; $i--) {
				//echo $i." ";
				preg_match_all("/<TD.*?>(.*?)<\/TD>/", $matchs[1][$i], $m);
				if($pre != NULL && $pre!=$m[1][0]) break;
				/*
				if($pre==NULL) $content.='学期：'.$m[1][0]."\n";
				$content.="--------\n【".$m[1][1]."】\n";
				$content.="应交：".$m[1][2]."\n";
				$content.="实交：".$m[1][3]."\n";
				$content.="减免：".$m[1][4]."\n";
				$content.="退费：".$m[1][5]."\n";
				$content.="欠交：".$m[1][6]."\n";
				*/
				$data['info']['list'][]=array(
					'xq' => $m[1][0],
					'xm' => $m[1][1],
					'yj' => $m[1][2],
					'sj' => $m[1][3],
					'jm' => $m[1][4],
					'tf' => $m[1][5],
					'qj' => $m[1][6]
				);
				$pre = $m[1][0];
			}
			return $data;
			//echo $str;
				
		} else
			throw new Exception("登录失败！原因未知。。", 1);
	}
}