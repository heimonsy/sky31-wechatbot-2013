<?php


class FirstExtends extends BaseExtends
{
	public static function getKeyWordPatterns() {
		return "/^subscribe$/i";
	}
	
	function analyse($matchs=NULL) {
		if($matchs == NULL) {
			
		} else {
			$content = "欢迎关注湘潭大学三翼校园官方微博！你已经成功加入三翼大家庭，从此以后我们就是一起玩耍的小伙伴咯！有开心事儿可以找小伙伴，不开心也可以找小伙伴，小伙伴会一直在你身边哦~下面为了庆贺小伙伴的加入，小编特意准备了各种风格庆贺节目，请小伙伴随意点播哦~\n";
			$content .= "【1】温情版\n";
			$content .= "【2】重口味版\n";
			$content .= "【3】二货版\n";
			$content .= "【4】温情版\n";
			$content .= "【5】温情版\n";
			$content .= "【6】温情版\n";
		}
	}
}