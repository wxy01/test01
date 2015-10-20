<?php
/**
 * index控制器
 * 
 * 
 */
class regController extends Controller{
	public $user_info=null;
	public $wx=NULL;
	public $uid=NULL;
	//public $uid=203;
	public function __construct(){
		parent::__construct();
		error_reporting(E_ALL);
		$this->get_set();
		
		
	}

public function check_tx_code($code=""){
	if ($code!=$_SESSION['code']) {
		echo -1;
	}else{
		echo 1;
	}
	

}


public function get_set(){
	$arr=$this->M->get_one("SELECT * from `lx_set` where `id`=1");
	//$arr['t12']=explode('+', $arr['t12']);
	$this->assign("set_list",$arr);
	return $arr;
}
	public function login(){
		$this->display("login.html");
	}
	public function reg(){
		$this->display("reg.html");
	}
	public function company_reg(){
		$this->display("company_reg.html");
	}
	public function do_login(){
		$phone=$_POST['phone'];
		$password=$_POST['password'];
		$arr=$this->M->get_one("SELECT `id`,`password` from `lx_user` where `phone`='".$phone."' and `status`=1");	
		if ($arr) {
				if ($password==$arr['password']) {
					$_SESSION['uid']=$arr['id'];
					echo 1;
				}else{
					echo -1;
				}
		}else{
			echo -1;
		}

	}

	public function do_login2(){
		$phone=$_POST['phone'];
		$code=$_POST['code'];
		//var_dump($_SESSION['sms_phone']);
		if ($code!=$_SESSION['sms_code']||$phone!=$_SESSION['sms_phone']) {
			//var_dump($_SESSION['sms_code']);
			echo -3;//手机验证码错误
			exit;
		}else{
			
		}		

		$arr=$this->M->get_one("SELECT `id` from `lx_user` where `phone`='".$phone."' and `status`=1");

		if ($arr) {
				
					$_SESSION['uid']=$arr['id'];
					echo 1;

		}else{

			$data['c_time']=$data['u_time']=time();
			$data['status']=1;
			$data['phone']=$phone;
			$data['password']="123456";
			$this->M->insert("lx_user",$data);
			$id=$this->M->insert_id();
			$_SESSION['uid']=$id;
			echo 1;
		}

	}



	public function do_reg($t=0){
		$data=$_POST;


if ($t==1) {
	
}else{

		if ($data['z_code2']!=$_SESSION['code']) {
			//var_dump($_SESSION['code']);
			echo -4;//验证码错误
			exit;
		}else{
			unset($data['z_code2']);
		}	
}


		if ($data['code']!=$_SESSION['sms_code']||$data['phone']!=$_SESSION['sms_phone']) {
			//var_dump($_SESSION['sms_code']);
			echo -3;//手机验证码错误
			exit;
		}else{
			unset($data['code']);
		}



		if (strlen($data['password'])<6) {
			echo -2;exit;//密码长度小于6
		}
		$arr=$this->M->get_one("SELECT `id` from `lx_user` where `phone`='".$data['phone']."' and `status`=1");
		if ($arr) {
			echo -1;//账号已被注册
		}else{
			$data['c_time']=$data['u_time']=time();
			$data['status']=1;
			//$data['rank']=1;
			$this->M->insert("lx_user",$data);
			$id=$this->M->insert_id();

			$_SESSION['uid']=$id;
			echo 1;
		}
	}

	/*检查授权码*/
	public function check_code($code=""){
		$arr=$this->M->get_one("SELECT * from `lx_code` where `code`='".$code."' and `status`=1");
		if ($arr) {
			# code...
		return 1;
		}else{
			return -1;
		}
	}
	/*注册成功*/
	public function reg_suc(){
		$this->display("reg_suc.html");
	}



	
}