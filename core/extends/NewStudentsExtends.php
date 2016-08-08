<?php

class NewStudentsExtends extends BaseExtends
{
	public static function getKeyWordPatterns(){
		return "/^生活贴士|生活帖士|学院介绍|六大组织$/i";
	}
	
	/* (non-PHPdoc)
	 * @see BaseExtends::analyse()
	 */
	public function analyse($matchs = "") {
		// TODO Auto-generated method stub
		//$start = Debug::microtime_float();
		if(isset($matchs[0])){
			$list = array();
			switch ($matchs[0]){
				case "生活贴士":
				case "生活帖士":
					$list = array(
						"新生注意事项"=>"http://url.cn/HfrNW9",
						"饮食篇"=>"http://url.cn/FHduvq",
						"交通篇"=>"http://url.cn/HOtM6M",
						"运动篇"=>"http://url.cn/E7bHEU",
						"购物篇"=>"http://url.cn/HW8muY",
						"宿舍篇"=>"http://url.cn/FDeAZZ",
						"医疗篇"=>"http://url.cn/G9Timg"
					);
					break;
				case "学院介绍":
					$list = array(
						"本校介绍"=>"http://url.cn/GHwzny",
						"哲学与历史文化学院"=>"http://url.cn/EO39T3",
						"旅游管理学院"=>"http://url.cn/EY72YU",
						"外国语学院"=>"http://url.cn/GGyue5",
						"化学学院"=>"http://url.cn/GJHICc",
						"信息工程学院"=>"http://url.cn/FC6av0",
						"兴湘学院"=>"http://url.cn/IYo4o3",
						"商学院"=>"http://url.cn/I5JJIH",
						"法学院知识产权学院"=>"http://url.cn/Ibjvul",
						"数学与计算机学学院"=>"http://url.cn/HKE799",
						"化工学院"=>"http://url.cn/EdTmm7",
						"土木工程与力学学院"=>"http://url.cn/GtkNKq",
						"艺术学院"=>"http://url.cn/F5ShwA",
						"职业技术学院"=>"http://url.cn/HoEzDo",
						"能源工程学院"=>"http://url.cn/IbhAyQ",
						"公共管理学院"=>"http://url.cn/IPkJ0X",
						"文学与新闻学院"=>"http://url.cn/Gkbhan",
						"材料与光电物理学院"=>"http://url.cn/FswTrd",
						"机械工程学院"=>"http://url.cn/FzKXou",
						"国际交流学院"=>"http://url.cn/F3D9q2"
					);
					$str = "";
					foreach($list as $key => $url) {
						$str.=$key.':'.$url."\n";
					}
					$this->responseMsg = WxMsgFactory::setTextMsg(
						$this->receivedMsg->fromUserName, 
						$this->receivedMsg->toUserName, 
						time(), $str);
					return $this->responseMsg;
					break;
				case "六大组织":
					$list = array(
						"校学生会"=>"http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NTA3MDU2MQ==&appmsgid=10000693&itemidx=1&sign=f444a5ca7d997fc6766931f7cedc2b0a",
						"三翼工作室"=>"http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NTA3MDU2MQ==&appmsgid=10000672&itemidx=1&sign=baab718d1766148fb5e10c1fda0959da",
						"艺术团"=>"http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NTA3MDU2MQ==&appmsgid=10000701&itemidx=1&sign=cb596f7a4de7d1aa23664cf62c19e60c",
						"雷锋公司"=>"http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NTA3MDU2MQ==&appmsgid=10000666&itemidx=1&sign=1e27a43ea4e837353d5cfd7a9b1e90ba",
						"学生科学技术协会"=>"http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NTA3MDU2MQ==&appmsgid=10000699&itemidx=1&sign=071106c004dbca29d87283e6812561cb",
						"学生社联"=>"http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NTA3MDU2MQ==&appmsgid=10000676&itemidx=1&sign=a109f73a1928685cf226e48c20c86e31",
						"学生社团一览表"=>"http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NTA3MDU2MQ==&appmsgid=10000691&itemidx=1&sign=7e1b2e4b6069ef8465eacfb158831818"
					);
					break;
				default:
					throw new OtherException("matchs错误，uid:".$this->user->uid, "服务器内部错误，请稍候再试T_T");
			}
			$newsItems = array();
			foreach($list as $title => $url ) {
				$newsItems[] = new WxNewsItem($title, "", "", $url);
			}
			
			$this->responseMsg = WxMsgFactory::setNewsMsg(
				$this->receivedMsg->fromUserName, 
				$this->receivedMsg->toUserName, 
				time(), $newsItems);
			//var_dump($this->responseMsg);
			//echo Debug::microtime_float()-$start;
			return $this->responseMsg;
			
		} else
			throw new OtherException("用户状态错误，uid:".$this->user->uid, "你的状态有误，请重试"); 
	}
	
	
}