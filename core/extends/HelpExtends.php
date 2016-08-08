<?php
class HelpExtends extends BaseExtends
{

private $helpInfo = "翼宝很高兴能为小伙伴服务。翼宝伴你左右，期待你的关注。（更多功能敬请期待 ^.^ ）。
---------
生活类
【天气】
【学费】查询缴费情况	
---------
娱乐类
【微信墙】
---------
学习类
【绑定】绑定你的学号信息
【课表】课表查询
【成绩】成绩查询
---------
新生季
【生活贴士】
【学院介绍】
【六大组织】
---------
回复 方括号 内的文字即可获得服务。
回复 帮助 获取帮助消息。"; 
	
	public static function getKeyWordPatterns() {
		return "/^(帮助|h|help)$/i";
	}
	
	public function analyse($matchs=NULL) {
		WxMsgFactory::setTextMsg(
			$this->receivedMsg->fromUserName, 
			$this->receivedMsg->toUserName, 
			time(),
			$this->helpInfo);
		$this->responseMsg = WxMsgFactory::getMsg();
	}
}