<?php

class Notices
{
	const COMMON_EXIT="回复`退出`返回主界面。回复`帮助`获取帮助。";
	
	const COMMON_HELP="Hello，我是翼宝。∩_∩
回复序号或栏目名即可了解相关信息啦~
1:今日湘大   2:天气预报
3:外卖           4:四季电台
回复“绑定”进行学号绑定\n回复“课表”获取课表信息\n回复“#+内容”实现英汉互译
翼宝伴你左右，期待你的关注。（更多功能敬请期待 ^.^ ）";
	
	//const COMMON_EXIT_HELP="翼宝支持的功能：\n1,今日湘大\n2,天气预报\n3,外卖\n4,四季电台\n回复序号或栏目名获取栏目信息。\n其他功能:\n绑定  课表\n回复`帮助`获取帮助信息。";
	const COMMON_EXIT_HELP = Notices::COMMON_HELP;
	const RADIO_CHOISE="回复序号获取节目，音频文件较大，请使用WIFI或3G模式收听。";
	const YZ_INPUT = "您好，您是第一次使用翼宝的语音功能，我们需要进行验证，请回复数字：\n";
	const YZ_ERROR = "您好，您的输入可能有误！请再输入一次。";
	
	const STAT_ERROR = "您的用户状态有误，请联系管理员。";

	const RADIO_LM   = "【1】.【青春祭】\n【2】.【春·绘声绘影】\n【3】.【夏·爱的发声】\n【4】.【秋·小情小调】\n【5】.【冬·你说我说】\n回复序号或春/夏/秋/冬/青春祭进入栏目。\n";
	
	
	/**********************************
	 *
	 *  BindExtends 的 notices
	 *
	 **********************************/
	const BIND_COMMON = "回复“头像”查看已经绑定头像。修改绑定信息回复“修改”，在本栏目下直接回复图片即可修改头像。回复“退出”即可返回主菜单";
	const BIND_NULL  = "您目前还没有绑定任何信息。";
	
	/**********************************
	 * 
	 *  CourseExtends 的 notices 
	 * 
	 **********************************/
	const COURSE_HELP   = "您当前在`课表`栏目下。\n";
	const COURSE_COMMON = "回复`课表`获取今日课程信息。回复`更新`更新课表。回复`0`或`退出`返回主菜单。\n";
	const COURSE_INPUT_ERROR = "您输入的信息有误 T_T..\n";
	const COURSE_NO_COURSE   = "翼宝提醒您，您今天没有课哦。\n";

	
	

	public static function getRadioLmTitle($index)
	{
		$radioLmTitle = array( YOUTH_INDEX=>"【青春祭】", SPRING_INDEX=>"【春·绘声绘影】", SUMMER_INDEX=>"【夏·爱的发声】", AUTUMN_INDEX=>"【秋·小情小调】", WINTER_INDEX=>"【冬·你说我说】");
		return $radioLmTitle[$index];
	}
}