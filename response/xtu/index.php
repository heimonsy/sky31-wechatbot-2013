<?php
function replaceWarp($str) {
	return str_replace("\r", "", str_replace("\n", "", $str));
}

include "HeFetchUrl.class.php";
include "XtuStu.class.php";
include "MyMcrypt.class.php";

if(isset($_GET['token']) && $_GET['token']==='HAC00q'){
	//var_dump($_GET);
	//exit();
	$method = trim($_GET['m']);
	$obj = trim(MyMcrypt::decrypt(urldecode($_GET['pack'])));
	$obj = json_decode($obj, true);
	if($obj!=NULL){
		try{
			if($method == 'course') {
				$xs = new XtuStu(trim($obj['stuNum']), trim($obj['pw']));
				$info = $xs->get_course();
			}else if($method == 'score') {
				$xs = new XtuStu(trim($obj['stuNum']), trim($obj['pw']));
				$info = $xs->get_score();
			}else if($method == 'rank'){
				$xs = new XtuStu(trim($obj['stuNum']), trim($obj['pw']));
				$info = $xs->get_rank();
			}else if($method == 'schoolFee'){
				$info = XtuStu::getPaymentCheckExtends($obj['stuNum'], $obj['pw']);
			}else {
				$info = '操作不存在';
			}
			
			echo json_encode(array(
					"ret" => 0,
					"info" => $info
				));
			exit();
	    	
    	}catch(Exception $e){
    		echo json_encode(array(
    			'ret'=>1,
    			'info'=>$e->getMessage()
    		));
			exit();
    	}
	}else {
		echo json_encode(array(
			'ret' => 1,
			'info' => 'pack解码失败'
		));
		exit();
	}

}else{
	echo "error";
}