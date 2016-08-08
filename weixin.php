<?php
require_once './include/common.php';

$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

// $postStr="<xml><ToUserName><![CDATA[gh_f3fadd8dfeb9]]></ToUserName>
// <FromUserName><![CDATA[oIZfmjg0b3kvyGdX69c42ZyyVr7Y]]></FromUserName>
// <CreateTime>1364752972</CreateTime>
// <MsgType><![CDATA[text]]></MsgType>
// <Content><![CDATA[课表]]></Content>
// <MsgId>5854629251579379899</MsgId>
// </xml>";

$s = new WxService($postStr);

$s->setResponseMsg();
$s->sendMsg();