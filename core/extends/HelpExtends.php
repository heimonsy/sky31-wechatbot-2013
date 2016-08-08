<?php
class HelpExtends extends BaseExtends
{

private $helpInfo = "翼宝很高兴能为小伙伴服务哦回复序号后的关键字即可了解相关信息。翼宝伴你左右，期待你的关注。（更多功能敬请期待 ^.^ ）。
---------
生活类
【天气】
【学费】查询缴费情况	
---------
娱乐类
正在制作中
---------
学习类
【绑定】绑定你的学号信息
【课表】课表查询
【成绩】成绩查询
---------
新生季
正在制作中
---------
回复方括号内的文字即可获得服务。"; 
	
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