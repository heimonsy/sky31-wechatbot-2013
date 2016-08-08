<?php

define("TOKEN","heimonsy972551578");

/*
 * 信息类型的常量的定义
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

//新闻分类
define("NEWS_SINGLE", 1);
define("NEWS_MULTI" , 2);

//关键字的分类
define("KEY_SYS", 1);
define("KEY_USER", 2);

//oauth 用于绑定认证
define("OAUTH_KEY", "SKY31");


define("SKY31_USERNAME" , "ceo@sky31.com" );
define("SKY31_PW"       , "595568ec41c753e222ab34211e531b35");

$wxdb_config = array( "host"=>"localhost", "root"=>"root","password"=>"NIUBSky3!.com", "dbname" => "wx_db");

$web_config  = array( "host"=>"http://wx.sky31.com/" ,"admin_path"=>"http://wx.sky31.com/mpadmin/");


define("ROOT_URL", "http://wxxrs.sky31.com/");
define("OAUTH_URL", "http://wxuser.sky31.com/oauth/oauth.php");
define("CW_OAUTH_URL", "http://wxuser.sky31.com/oauth/payment_pw.php");
define("WX_USER_URL", "http://wxuser.sky31.com/");
define("CGI_BIN_GETUSERINFO", "http://wxuser.sky31.com/cgi_bin/getuserinfo.php");
