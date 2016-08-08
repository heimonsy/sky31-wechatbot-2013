<?php

define("TOKEN","heimonsy972551578");

/*
 *常用常量的定义
 */
define("MSG_TEXT"     ,  "text");
define("MSG_IMAGE"    ,  "image");
define("MSG_LOCATION" ,  "location");
define("MSG_LINK"     ,  "link");
define("MSG_NEWS"     ,  "news");
define("MSG_MUSIC"    ,  "music");
define("MSG_VOICE"    ,  "voice");
define("MSG_OTHER"    ,  "other");
define("MSG_EVENT"    ,  "event");
define("KEY_TYPE_LOCKED", 0);
define("KEY_TYPE_USER"  , 1);

//用户关注平台时发送过来的信息
define("FLLOWING",  "subscribe");

// 用户状态，根据栏目来确定
define("STAT_FORBID",  "--");
define("STAT_BASE" ,   "00"); //基本菜单
define("STAT_LEHUO",   "02");
define("STAT_RADIO",   "04");
//define("STAT_YZ"   ,   "09"); //用于验证
define("STAT_BIND" ,   "09");
define("STAT_COURSE" ,   "10");

//电台子栏目
define("RADIO_BASE"  ,  "00" );
define("SPRING_INDEX", 1);
define("RADIO_SPRING",  "01" );
define("SUMMER_INDEX", 2);
define("RADIO_SUMMER",  "02" );
define("AUTUMN_INDEX", 3);
define("RADIO_AUTUMN",  "03" );
define("WINTER_INDEX", 4);
define("RADIO_WINTER",  "04" );
define("YOUTH_INDEX", 0);
define("RADIO_YOUTH" ,  "05" );
$radio_status_array=array(RADIO_YOUTH, RADIO_SPRING, RADIO_SUMMER, RADIO_AUTUMN, RADIO_WINTER);

//绑定个人信息
define("BIND_BASE", "00");

//新闻分类
define("NEWS_SINGLE", 1);
define("NEWS_MULTI" , 2);

//关键字的分类
define("KEY_SYS", 1);
define("KEY_USER", 2);

//回复的分类
define("RES_TYPE_F", 1);//第一次
define("RES_TYPE_H", 2);//帮助
define("RES_TYPE_A", 3);//用户调教自定义的类型

//网站配置
define("THPL_SINA", "http://weibo.com");//微博平台树洞地址

//oauth 用于绑定认证
define("OAUTH_KEY", "SKY31");

//正则表达式配置，用于栏目分析
//今日新闻
define( "NEWS_REG"   , '/^(1|今日湘大|新闻|news)$/i' );
//天气 
define( "WEATHER_REG", '/^(2|天气|天气预报)$/i' );
//外卖
define( "WALK_REG"   , '/^(3|外卖|北苑|金翰林|(新)?琴湖|兴湘|联建|南苑)(外卖)?$/i' );
//电台
define( "RADIO_REG"  , '/^4|四季电台|电台|fm|radio$/i' );
//乐活
define( "LEHUO_REG"  , '/^(5|乐活)$/i' );
//树洞,不需要尾部匹配
define( "TRHO_REG"   , '/^@\040?(.+)$/i' );
//帮助
define( "HELP_REG"   , '/^(帮助|h|help|Hello2BizUser)$/i' );
//翻译
define( "TRANSLATION_REG" , '/#\040?(.*)/i' );
//绑定扩展
define( "BIND_REG" , '/^绑定$/i' );
//
define( "COURSE_REG" , '/^(查)?课表$/i' );
//
define( "TALK_SUBJECT", '/\#(.+?)\#/');

define( "SKY31_USERNAME" , "ceo@sky31.com" );
define( "SKY31_PW"       , "e10adc3949ba59abbe56e057f20f883e");

//define( "SKY31_PW"       , "595568ec41c753e222ab34211e531b35");



$wxdb_config = array( "host"=>"localhost", "root"=>"root","password"=>"NIUBSky3!.com", "dbname" => "wx_db");

$web_config = array( "host"=>"http://wx.sky31.com/" ,"admin_path"=>"http://wx.sky31.com/mpadmin/");


define("ROOT_URL", "http://wx.sky31.com/");
define("OAUTH_URL", ROOT_URL."oauth/oauth.php");

