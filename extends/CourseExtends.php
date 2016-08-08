<?php
class CourseExtends extends BaseExtends
{
	public static $cnm_to_word=array(0=>"第一，二节：",1=>"第三，四节：", 2=>"第五，六节：", 3=>"第七，八节：", 4=>"第九，十节：");
	
	public function analyse()
	{
		$msgFactory = new WxMsgFactory();
		if( $this->receivedMsg->content == "更新" ){
			$bind = Oauth::haveBind($this->user->userId);
			$course = PostLogin::getKb( Encodes::decode($bind['snum']) , Encodes::decode($bind['pw']));
			self::updateCourse($this->user->userId, $course['class']);
			$content = "更新成功。\n";
			$content.= self::getTodayCourses($this->user->userId, $bind);
			$content.= Notices::COURSE_COMMON;
			$content.= "<a href=\"".ROOT_URL."data/course/course.php?oa=".Oauth::getOauth($this->user->userId)."\">查看所有课程</a>";
			
		}else if( $this->receivedMsg->content == "课表" ){
			//课表
			$bind = Oauth::haveBind($this->user->userId);
			if( !self::haveInsertCourse($this->user->userId)){
				//$this->user->setStatus( STAT_COURSE );
				$content="您还没有更新过课表哦，回复`更新`即可更新课表。\n如果长时间没有响应(教务管理系统，你懂的),回复`课表`再试一次。回复`退出`退出本栏目。";
				//$course = PostLogin::getKb( Encodes::decode($bind['snum']) , Encodes::decode($bind['pw']));
				//self::insertCourse($user->userId, $course['class']);
			}else{
				$content = self::getTodayCourses($this->user->userId, $bind);
				$content .= Notices::COURSE_COMMON;
				$content .= "<a href=\"".ROOT_URL."data/course/course.php?oa=".Oauth::getOauth($this->user->userId)."\">查看所有课程</a>";
			}
			
		}else if( $this->receivedMsg->content == "退出" || $this->receivedMsg->content=="0" ){
			//退出
			$this->user->setStatus( STAT_BASE );
			$content = "成功退出!\n".Notices::COMMON_EXIT_HELP;
			
		}else if( $this->receivedMsg->content == "帮助" || $this->receivedMsg->content == "h" ){
			//帮助信息
			//echo "fuck";
			$content = Notices::COURSE_HELP.Notices::COURSE_COMMON;
			
		}else{
			$content  = Notices::COURSE_INPUT_ERROR;
			$content .= Notices::COURSE_HELP.Notices::COURSE_COMMON;
		}
		
		$msgFactory->setTextMsg(
				$this->receivedMsg->fromUserName,
				$this->receivedMsg->toUserName,
				time(),
				$content
		);
		$this->responseMsg=$msgFactory->getMsg();
	}
	
	/**
	 * 通过传入的user对象设置用户的状态并返回向用户反馈的内容
	 * @param WxUser $user
	 * @return string
	 */
	public static function setCourseStatus($user)
	{
		$bind = Oauth::haveBind($user->userId);
		if( !$bind ){
			$oauth = Oauth::getOauth($user->userId);
			$url = OAUTH_URL."?oa=".$oauth;
			$content = "您还没有绑定个人信息。\n点击：".$url." 进行绑定。\n绑定成功后回复`更新`即可,如果长时间没有响应(教务管理系统，你懂的),回复`课表`再试一次。回复0或`退出`回到主菜单";
			
		}else{

			if( !self::haveInsertCourse($user->userId)){
				$user->setStatus( STAT_COURSE );
				return $content="您还没有更新过课表哦，回复`更新`即可更新课表。\n如果长时间没有响应(教务管理系统，你懂的),回复`课表`再试一次。回复`退出`退出本栏目。";
				//$course = PostLogin::getKb( Encodes::decode($bind['snum']) , Encodes::decode($bind['pw']));
				//self::insertCourse($user->userId, $course['class']);
			}
			$content = self::getTodayCourses($user->userId, $bind);
			$content .= Notices::COURSE_COMMON;
			$content .= "<a href=\"".ROOT_URL."data/course/course.php?oa=".Oauth::getOauth($user->userId)."\">查看所有课程</a>";
		}
		$user->setStatus( STAT_COURSE );
		return $content;
	}
	
	
	/**
	 * 输入用户的uid 和 绑定信息(通过 Oauth::haveBind) 获得, 返回用户 今日的课程信息, 也可指定日期$twek , $twek默认为今天。
	 * @param int $uid
	 * @param array $bind
	 * @param string $twek
	 * @return string
	 */
	public static function getTodayCourses( $uid ,$bind ,$twek=null)
	{
		//获取今天是星期几
		if( $twek==NULL ){
			$twek = date('w',time());
			$twek--;
			if( $twek<0 ) $twek=6;
		} 
		//$twek=1;
		
		$wxdb = MyDB::getWxdb();
		$sql = "select `cnm`,`kname`,`tname`,`place`,`week` from `stu_kb`  inner join `stu_course` ON `stu_kb`.`cid`=`stu_course`.`cid`  inner join `stu_room` on `stu_course`.`rid`=`stu_room`.`rid` where `uid`='{$uid}' and `wek`='{$twek}' ";
		$r = $wxdb->query($sql);
		if( !$r ) echo mysql_error();
		
		if( mysql_num_rows($r)==0 )
			return Notices::COURSE_NO_COURSE;
		
		while( ($m=mysql_fetch_assoc($r))!=null ){
			$arr[ $m['cnm'] ][] = array( $m['kname'], $m['place'], $m['tname'], $m['week']);
		}
		
		$num = count( $arr );
		$content="翼宝提醒您， 今天有{$num}节课哦：\n";
		foreach ( $arr as $key=>$v ){
			$content.="--------------\n";
			$w = self::$cnm_to_word[intval( $key )];
			$content.=$w."\n";
			foreach ( $v as $vv)
			foreach ($vv as $vvv)
				$content.=$vvv."\n";
		}
		return $content."--------------\n";
	}
	
	public static function haveInsertCourse($uid)
	{
		$wxdb=MyDB::getWxdb();
		$sql = "select `cid` from `stu_kb` where `uid`='{$uid}' limit 1";
		$r = $wxdb->query($sql);
		if( !$r ) echo mysql_error();
		$r = mysql_num_rows($r);
		return $r==0? false:true;
	}
	
	public static function insertCourse( $uid, $course )
	{
		$wxdb =  MyDB::getWxdb();
		for($i=0;$i<6;$i++){
			for( $j=0;$j<5;$j++ ){
				$k = $course[$i][$j];
				foreach( $k as $v ){
					$cid = self::getCourseid($i, $j, $v);
					$sql  = "insert into `stu_kb` (`kid`,`uid`,`cid`) values(null, {$uid}, {$cid})";
					$r = $wxdb->query($sql);
					if( !$r ) echo "\n".__LINE__.mysql_error();
				}
			}
		}
	}
	
	public static function getCourseid($wek, $cnm, $v)
	{
		$wxdb =MyDB::getWxdb();
		$sql  ="select `cid` from `stu_course` 
			where `kname`='{$v['name']}' 
			and `tname`  ='{$v['tname']}'
			and `week`  ='{$v['week']}'
			and `wek`    ={$wek}
			and `cnm`     ={$cnm}";
		$r = $wxdb->query($sql);
		
		if( !$r ) echo "\n".__LINE__.mysql_error()."\n";
		
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
			if( !$r ) echo "\n".__LINE__.mysql_error()."\n";
			return 	mysql_insert_id();
			
		}else
			return $r['cid'];	
	}
	
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
			if( !$r ) echo "\n".__LINE__.mysql_error()."\n";
			return mysql_insert_id($wxdb->link);
		}else
			return $r['rid']; 
		
	}
	
	public static function updateCourse($uid, $course)
	{
		$wxdb=MyDB::getWxdb();
		$sql="delete `stu_kb` from `stu_kb`  inner join `stu_course` ON `stu_kb`.`cid`=`stu_course`.`cid`  inner join `stu_room` on `stu_course`.`rid`=`stu_room`.`rid` where `uid`={$uid}";
		$r = $wxdb->query($sql);
		if( !$r ) exit(mysql_error());
		self::insertCourse($uid, $course);
	} 
}
// include_once '../include/common.php';
// //include_once("../lib/PostLogin.php");
// //$userName = "2010551303";
// //$password = '666666';
// $userName = "2011960509";
// $password = "123456";
// $r = PostLogin::getKb($userName, $password);
// if(!$r) echo"fuck";
// //CourseExtends::insertCourse(2, $r['class']);
// CourseExtends::updateCourse(2, $r['class']);

