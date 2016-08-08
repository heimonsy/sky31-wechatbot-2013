<?php
class CourseExtends extends BaseExtends
{
	public static $cnm_to_word=array(0=>"第一，二节：",1=>"第三，四节：", 2=>"第五，六节：", 3=>"第七，八节：", 4=>"第九，十节：");
	
	
	public static function getKeyWordPatterns(){
		return "/^(@)?课表$/i";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BaseExtends::analyse()
	 */
	public function analyse($matchs=NULL)
	{
		$bind = BindExtends::haveBind($this->user->uid);
		if(!$bind) 
			$content = "您还没有绑定个人信息，点击链接进行绑定：".Oauth::getBindUrl($this->user->uid);
		//var_dump($matchs);
		else {
			if( isset($matchs[1]) && $matchs[1]=='@' ) {
				//更新课表
				$errorInfo = "";
				$bind   = BindExtends::haveBind($this->user->uid);
				
				$course = Course::getCourseFromXtu(
						Encodes::decode($bind['snum']), Encodes::decode($bind['pw']), $this->user->uid);
				if($course) {
					Course::updateCourse($this->user->uid, $course['class']);
					$content = "更新成功\n";
					$content.= $this->getTodayCourses($this->user->uid, $bind);
					$content.= "回复  @课表  更新课表信息\n";
					$content.= "<a href=\"".WX_USER_URL."course/course.php?oa=".Oauth::getOauth($this->user->uid)."\">查看所有课程</a>";
					
				} else
					$content = "更新失败\n".Course::$error_info;
				
			}else {
				//直接获取课表
				
				if( $bind ){
					if(!Course::haveInsertCourse($this->user->uid))
						$content = "您还没有更新过课表哦，回复`@课表`即可更新课表。\n如果长时间没有响应(教务管理系统，你懂的),回复`课表`再试一次。";
					else {
						$content  = $this->getTodayCourses($this->user->uid, $bind);
						$content .= "回复  @课表  更新课表信息\n";
						$content .= "<a href=\"".WX_USER_URL."course/course.php?oa=".Oauth::getOauth($this->user->uid)."\">查看所有课程</a>";
					}
				}
			}
		}//matchs

		$this->responseMsg = WxMsgFactory::setTextMsg(
				$this->receivedMsg->fromUserName,
				$this->receivedMsg->toUserName,
				time(),
				$content
			);
			
		return $this->responseMsg;
	}
	
	/**
	 * 输入用户的uid 和 绑定信息(通过 Oauth::haveBind) 获得, 返回用户 今日的课程信息, 也可指定日期$twek , $twek默认为今天。
	 * @param int $uid
	 * @param array $bind
	 * @param string $twek
	 * @return string
	 */
	public function getTodayCourses( $uid ,$bind ,$twek=null)
	{
		//获取今天是星期几
		if( $twek==NULL ) {
			$twek = date('w',time());
			$twek--;
			if( $twek<0 ) $twek=6;
		} 
		
		$wxdb = MyDB::getWxdb();
		$sql = "select `cnm`,`kname`,`tname`,`place`,`week` from `stu_kb`  inner join `stu_course` ON `stu_kb`.`cid`=`stu_course`.`cid`  inner join `stu_room` on `stu_course`.`rid`=`stu_room`.`rid` where `uid`='{$uid}' and `wek`='{$twek}' ";
		$r = $wxdb->query($sql);
		
		if(!$r) 
			throw new CURDException(
					"获取课程错误，表名：stu_kb, stu_course", 
					"服务器出错，读取课表失败T_T", 
					mysql_error($wxdb->link));
		
		if( mysql_num_rows($r)==0 )
			return "你今天没有课哦~";
		
		while(($m=mysql_fetch_assoc($r)) != null ){
			$arr[$m['cnm']][] = array($m['kname'], $m['place'], $m['tname'], $m['week']);
		}
		
		$num = count( $arr );
		$content="翼宝提醒您， 今天有{$num}节课哦：\n";
		foreach ($arr as $key=>$v ) {
			$content .= "--------------\n";
			$w = self::$cnm_to_word[intval( $key )];
			$content .= $w."\n";
			foreach ( $v as $vv)
			foreach ($vv as $vvv)
				$content.=$vvv."\n";
		}
		return $content."--------------\n";
	}
}
