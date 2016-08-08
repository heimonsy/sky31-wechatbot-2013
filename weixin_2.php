<?php
//
//exit();
require_once './include/common.php';


$postStr="<xml><ToUserName><![CDATA[gh_f3fadd8dfeb9]]></ToUserName>
<FromUserName><![CDATA[oYeDBjrIxjgl0haUk56FO-npcW9s]]></FromUserName>
<CreateTime>1364752972</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[成绩]]></Content>
<MsgId>5854629251579379899</MsgId>
</xml>";

$s = new WxService($postStr);

$s->setResponseMsg();

$s->sendMsg();