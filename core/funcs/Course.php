<?php


class Course
{
	public static $error_info="";
	
	/**
	 * 判断用户是否已经更新了 课表
	 * @param int $uid
	 * @return boolean
	 */
	public static function haveInsertCourse($uid)
	{
		$wxdb = MyDB::getWxdb();
		$sql  = "select `cid` from `stu_kb` where `uid`='{$uid}' limit 1";
		$r    = $wxdb->query($sql);
		if( !$r )
			throw new CURDException(
					"课表错误，表名： stu_kb",
					"服务器错误T_T，请稍后再试",
					mysql_error($wxdb->link));
	
		$r = mysql_num_rows($r);
		return $r == 0? false : true;
	}
	
	/**
	 * 用户获得课表之后插入课表信息
	 * @param unknown $uid
	 * @param unknown $course
	 */
	private static function insertCourse($uid, $course ){
		$wxdb =  MyDB::getWxdb();
		for($i=0;$i<6;$i++){
			for( $j=0;$j<5;$j++ ){
				$k = $course[$i][$j];
				foreach( $k as $v ){
					$cid = self::getCourseid($i, $j, $v);
					$sql  = "insert into `stu_kb` (`kid`,`uid`,`cid`) values(null, {$uid}, {$cid})";
					$r = $wxdb->query($sql);
					if( !$r ) throw new CURDException(
							"插入课表错误，表名： stu_kb",
							"插入课表错误T_T，请稍后再试",
							mysql_error($wxdb->link));
	
				}
			}
		}
	}
	
	/**
	 * 获取课程的Id信息
	 * @param unknown $wek
	 * @param unknown $cnm
	 * @param unknown $v
	 * @throws CURDException
	 * @return number|Ambigous <>
	 */
	public static function getCourseid($wek, $cnm, $v) {
		$wxdb = MyDB::getWxdb();
		$sql  = "select `cid` from `stu_course`
		where `kname`='{$v['name']}'
		and `tname`  ='{$v['tname']}'
				and `week`  ='{$v['week']}'
						and `wek`    ={$wek}
						and `cnm`     ={$cnm}";
		$r = $wxdb->query($sql);
	
		if(!$r)
			throw new CURDException(
					"获取课程错误，表名： stu_course",
					"服务器出错，读取课获取课程ID失败T_T",
					mysql_error($wxdb->link));
		
		$r = mysql_fetch_assoc($r);
		if($r==NULL){
			$rid = self::getRomeid($v['place']);
			$sql = "insert into `stu_course` (`cid`,`wek`,`cnm`, `rid`, `kname`, `tname`, `week`) values(
					null,
					{$wek},
					{$cnm},
					{$rid},
					'{$v['name']}',
					'{$v['tname']}',
					'{$v['week']}'
					)";
			$r = $wxdb->query($sql);
			if(!$r)
				throw new CURDException(
					"获取课程错误，表名： stu_course",
					"服务器出错，插入课程信息失败T_T,请稍后再试",
					mysql_error($wxdb->link));
			
			return 	mysql_insert_id();
		}else
			return $r['cid'];
	}
	
	/**
	 * 将课表信息插入数据库，需要先getCourseFromXtu获取课表
	 * @param unknown $uid
	 * @param unknown $course
	 * @throws CURDException
	 */
	public static function updateCourse($uid, $course)
	{
		$wxdb = MyDB::getWxdb();
		$sql = "delete `stu_kb` from `stu_kb`  inner join `stu_course` ON `stu_kb`.`cid`=`stu_course`.`cid`  inner join `stu_room` on `stu_course`.`rid`=`stu_room`.`rid` where `uid`={$uid}";
		$r = $wxdb->query($sql);
		if( !$r )
			throw new CURDException(
					"删除旧课表信息失败,表名：stu_course",
					"服务器出错了T_T, 请稍后再试",
					mysql_error($wxdb->link)
			);
		self::insertCourse($uid, $course);
	}
	/**
	 * 获取课表信息，返回课表数组
	 * @param unknown $userName
	 * @param unknown $password
	 * @throws OtherException
	 * @return boolean
	 */
	public static function getCourseFromXtu($userName,$password, $uid)
	{
		$ch = new MyUrlFetch();
	
		$fields = array(
				'username'=>$userName, 'password'=>$password,
				'identity'=>'student', 'role'=>'1');
		$ch->setPostArray($fields);
		$str = iconv("GB2312", "UTF-8",
				$ch->post("http://202.197.224.134:8083/jwgl/logincheck.jsp"));
		$code = $ch->getHttpCode()."";
		if($code[0]=='2') {
			if(strpos($str, "密码错误") == FALSE) {
				$ch->post("http://202.197.224.134:8083/jwgl/index1.jsp");
				$str = iconv("GB2312", "UTF-8",
						$ch->get("http://202.197.224.134:8083/jwgl/xk/xk1_kb_gr.jsp?xq1=01"));
	
			} else {
				self::$error_info = '密码错误哦。点击链接重新绑定：'.Oauth::getBindUrl($uid);
				return false;
			}
				
		} else
			throw new OtherException(
					"无法连接教务管理系统,http code:".$code,
					"教务管理系统奔溃了~~~, 请稍后再试");
	
		//echo $str ;
	
		$ch->close();
		return self::analyseKb($str);
	}
	
	/**
	 * 分析课表，是需要进过anayseKb处理的
	 * @param unknown $str
	 * @return boolean|Ambigous <multitype:, multitype:string , unknown>
	 */
	private static function analyseKb($str) {
		if(preg_match("/登录超时|重新登录/", $str) || $str == "" )
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
	 * 获取教室id,方便以后做蹭课
	 * @param unknown $place
	 * @throws CURDException
	 * @return number|Ambigous <>
	 */
	public static function getRomeid($place)
	{
		//普通教室
		//$nReg="/^(.*)[0-9]+$/";
		//$kReg="/^(.*)(|)/";
		$wxdb = MyDB::getWxdb();
		$sql  = "select `rid` from `stu_room` where `place`='{$place}'";
		$r = $wxdb->query($sql);
	
		if( !$r ) echo "\n".__LINE__.mysql_error()."\n";;
	
		$r = mysql_fetch_assoc($r);
		if( $r==NULL){
			$sql = "insert into `stu_room` (`rid`,`place`) values(null,'{$place}')";
			$r = $wxdb->query($sql);
			if( !$r )
				throw new CURDException(
						"获取教室错误，表名： stu_room",
						"获取教室ID出错T_T,请稍后再试",
						mysql_error($wxdb->link));
				
			return mysql_insert_id($wxdb->link);
		}else
			return $r['rid'];
	
	}
}