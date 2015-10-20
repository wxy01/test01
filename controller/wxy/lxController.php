<?php
/**
 * 管理中心
 */

/**
* 
*/
class lxController extends Controller
{
	
	function __construct()
	{	

		parent::__construct();
		

	}

	public function login(){
		$this->display('login.html');
	}
	public function do_login(){
		
		$uname=isset($_POST['uname'])?$_POST['uname']:'';
		$pwd=isset($_POST['pwd'])?$_POST['pwd']:'';

		if ($uname==''||$pwd=='') {
			$data=-1;
		}else{
			$arr=$this->M->get_one("SELECT `pwd`,`id` from `lx_admin` where `uname`='".$uname."' and `grade`=100");

			if(!$arr){
				$data=-2;
			}else{
				
				if ($pwd==$arr['pwd']) {
					$data=1;
					$_SESSION['admin_id']=$arr['id'];
				}else{
					$data=-3;
				}
			}	
		}

		echo $data;
		
	}










}