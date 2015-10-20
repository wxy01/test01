<?php
/**
 * index控制器
 * 
 * 
 */
class shopController extends Controller{
	public $user_info=null;
	public $wx=NULL;
	public $uid=NULL;
	//public $uid=203;
	public function __construct(){
		parent::__construct();
		error_reporting(E_ALL);
		$this->check_login();
		$this->uid=$this->get_uid();
		$this->get_userinfo($this->uid);
		$this->get_article_type();

	
		
	}




	public function index(){
		$this->get_index_image();		
		$this->display("index.html");
	}

	public function product_list(){
		$this->get_index_image();	
		$this->get_product_list();
		$this->display("product_list.html");
	}

	public function product($id=""){
		$this->get_product($id);
		$this->get_product_img($id);
		$this->display("product.html");
	}

	public function add_car(){
		$data['pid']=$_POST['pid'];
		$data['num']=$_POST['num'];
		$data['ml']=$_POST['ml'];
		$data['uid']=$this->uid;
		$data['c_time']=$data['u_time']=time();
		$data['status']=1;
		$this->M->insert("lx_shop_car",$data);
		$id=$this->M->insert_id();
		echo $id;
	}

	public function remove_car(){
		$id=$_POST['id'];
		$data['status']=-1;

		$this->M->update("lx_shop_car",$data,"`id`='".$id."'");
	}

	public function my_car(){
		$uid=$this->uid;
		$this->get_my_car();
		$this->display("my_car.html");
	}

	public function add_buy(){
		$ids=$_POST['ids'];
		$data['ids']=$ids=rtrim($ids,',');
		$arr3=$this->get_total_money($ids);
		$data['yunfei']=$arr3['yunfei'];
		$data['money']=$arr3['money'];
		$data['order_num']=order_number();
		$data['c_time']=$data['u_time']=time();
		$data['status']=1;
		$data['uid']=$uid=$this->uid;

		$this->M->insert("lx_shop_buy",$data);
		$id=$this->M->insert_id();
		echo $id;
		$this->M->query("UPDATE `lx_shop_car` set `status`=2 where `uid`='".$uid."' and `id` in ($ids)");
	
	}
	public function get_car($id=""){
		$arr=$this->M->get_one("SELECT `id` from `lx_shop_car` where `uid`='".$this->uid."' and `pid`='".$id."' and `status`=1 ");
		if ($arr) {
			echo 1;
		}else{
			echo -1;
		}
	}
	public function pay($id=""){
		$this->get_pay_money($id);

		$this->display("pay.html");
	}

	public function add_address(){
		
		$this->display("add_address.html");
	}
	public function do_add_address(){
		$arr=$_POST;
		foreach ($arr as $key => $e) {
			$data[$key]=$e;
		}
		$is_default=$data['is_default'];
		unset($data['is_default']);
		$data['uid']=$uid=$this->uid;
		$data['status']=1;
		$data['c_time']=$data['u_time']=time();
		$this->M->insert("lx_shop_address",$data);
		$id=$this->M->insert_id();
		echo $id;
		if ($is_default==1) {		
			$this->set_default_address($id);
		}

	}
	public function do_edit_address($id=""){
		$arr=$_POST;
		foreach ($arr as $key => $e) {
			$data[$key]=$e;
		}
		$is_default=$data['is_default'];
		unset($data['is_default']);	
		$data['u_time']=time();
		$this->M->update("lx_shop_address",$data,"`id`='".$id."'");
		echo 1;
		if ($is_default==1) {		
			$this->set_default_address($id);
		}

	}
	public function del_address($id=""){
		$data['status']=-1;
		$this->M->update("lx_shop_address",$data,"`id`='".$id."'");
	}

	public function my_address(){
		$uid=$this->uid;
		$arr=$this->M->get_all("SELECT * from `lx_shop_address` where `uid`='".$uid."' and `status`=1 order by `id` desc");
		$this->assign("list",$arr);
		$this->display("my_address.html");
	}

	public function edit_address($id=""){
		
		$this->get_address($id);
		$this->display("edit_address.html");
	}

	public function my_address2($id=""){
		$uid=$this->uid;
		$arr=$this->M->get_all("SELECT * from `lx_shop_address` where `uid`='".$uid."' and `status`=1 order by `id` desc");
		$this->assign("list",$arr);
		$this->assign("id",$id);
		$this->display("my_address2.html");
	}
	public function choose_address(){
		$id=$_POST['id'];
		$data['address_id']=$_POST['aid'];
		$this->M->update("lx_shop_buy",$data,"`id`='".$id."'");
		echo 1;
	}
	public function pay_2($id=""){
		$this->get_pay_money2($id);
		$this->display("pay_2.html");
	}
	public function do_pay($id=""){
		$data['remark']=$_POST['remark'];
		$data['status']=2;
		$id=$_POST['id'];
		$this->M->update("lx_shop_buy",$data,"`id`='".$id."'");
	}
















	public function user(){
		$this->display("user.html");
	}
	public function my_order(){
		$uid=$this->uid;
		$this->get_order($uid);
		$this->display("my_order.html");
	}	

	public function cancel_order($id=""){
		$data['status']=-1;
		$this->M->update("lx_shop_buy",$data,"`id`='".$id."'");
		echo 1;
	}	
	public function my_info(){
		$uid=$this->uid;
		$this->display("my_info.html");
	}
	public function save_info(){
		$arr=$_POST;
		$uid=$this->uid;
		foreach ($arr as $key => $e) {
			$data[$key]=$e;
		}
		$this->M->update("lx_user",$data,"`id`='".$uid."'");
	}
	public function password(){
		$uid=$this->uid;
		$this->display("password.html");
	}
	public function change_password(){
		$uid=$this->uid;
		$old_password=$_POST['old_password'];
		$data['password']=$password=$_POST['new_password'];
		$renew_password=$_POST['renew_password'];
		$arr=$this->M->get_one("SELECT `password` from `lx_user` where `id`='".$uid."'");
		if ($old_password!=$arr['password']) {
			echo -1;
		}else{
			$this->M->update("lx_user",$data,"`id`='".$uid."'");
			echo 1;

		}

	}












	public function get_index_image(){
		$arr=$this->M->get_all("SELECT * from `lx_shop_index` where `status`=1");
		$this->assign("index_list",$arr);
	}
	/**/
	public function get_article_type(){
		$arr=$this->M->get_all("SELECT * from `lx_article_type` where `status`=1 order by `id` desc");
		$this->assign("article_type_list",$arr);
	}

	public function get_product_list(){
		$arr=$this->M->get_all("SELECT `name`,`id`,`abstract`,`img_url`,`now_price` from `lx_shop_product`  where`status`=1 order by `id` desc");
		

		$this->assign("product_list",$arr);
		return $arr;
	}
	public function get_product($id=""){
		$arr=$this->M->get_one("SELECT * from `lx_shop_product` where `id`='".$id."' and `status`=1");
		$arr['ml']=explode(",", $arr['ml']);
		$arr['content']=htmlspecialchars_decode($arr['content']);
		$this->assign("list",$arr);

	}

	public function get_product_img($id=""){
		$arr=$this->M->get_all("SELECT * from `lx_shop_product_img` where `pid`='".$id."'");
		$this->assign("p_list",$arr);
	}
	public function get_my_car(){
		$arr=$this->M->get_all("SELECT * from `lx_shop_car` where `uid`='".$this->uid."' and `status`=1 order by `id` desc");
		foreach ($arr as $key => &$e) {
			$e['product']=$this->M->get_one("SELECT `id`,`name`,`now_price`,`img_url`,`yunfei` from `lx_shop_product` where `id`='".$e['pid']."'");
		}
		unset($e);
		$this->assign("list",$arr);
	}
	public function get_total_money($ids=""){
		$arr=$this->M->get_all("SELECT `id`,`num`,`pid` from `lx_shop_car` where `id` in ($ids)");
		$money=0;
		$yunfei=0;
		foreach ($arr as $key => $e) {
			$arr2=$this->get_product_money($e['pid']);
			$money+=$arr2['now_price']*$e['num'];
			if ($yunfei < $arr2['yunfei']) {
				$yunfei=$arr2['yunfei'];
			}
		}
		$arr3['money']=$money; 
		$arr3['yunfei']=$yunfei; 
		return $arr3;
	}	
	public function get_product_money($id=""){
		$arr=$this->M->get_one("SELECT `now_price`,`yunfei` from `lx_shop_product` where `id`='".$id."'");
		return $arr;
	}
	public function get_pay_money($id=""){
		$arr=$this->M->get_one("SELECT * from `lx_shop_buy` where `id`='".$id."' and `status`=1");
		$arr2=$this->M->get_one("SELECT * from `lx_shop_address` where `id`='".$arr['address_id']."'");
		$this->assign("list",$arr);
		$this->assign("list2",$arr2);
	}
	public function get_pay_money2($id=""){
		$arr=$this->M->get_one("SELECT * from `lx_shop_buy` where `id`='".$id."' and `status`=2");
		$arr2=$this->M->get_one("SELECT * from `lx_shop_address` where `id`='".$arr['address_id']."'");
		$this->assign("list",$arr);
		$this->assign("list2",$arr2);
	}

	public function set_default_address($id=""){
		$data['address_id']=$id;
		$this->M->update("lx_user",$data,"`id`='".$this->uid."'");
	}
	public function get_address($id=""){
		$arr=$this->M->get_one("SELECT * from `lx_shop_address` where `id`='".$id."'");
		$this->assign("a_list",$arr);	
		return $arr;	
	}

	public function get_order($uid=""){
		$arr1=$this->M->get_all("SELECT * from `lx_shop_buy` where `uid`='".$uid."' and `status`=2");
		$arr2=$this->M->get_all("SELECT * from `lx_shop_buy` where `uid`='".$uid."' and `status`=1");
		$n1=count($arr1);
		$n2=count($arr2);
		foreach ($arr1 as $key => &$e) {
			$e['c_time']=date("Y-m-d H:i:s",$e['c_time']);
			$e['product_list']=$this->get_order_product_list($e['ids']);
		}
		unset($e);
		foreach ($arr2 as $key => &$e) {
			$e['c_time']=date("Y-m-d H:i:s",$e['c_time']);
		}
		unset($e);		
		$this->assign("list1",$arr1);	
		$this->assign("list2",$arr2);	
		$this->assign("n1",$n1);	
		$this->assign("n2",$n2);	
		//return $arr;	
	}

	public function get_order_product_list($ids=""){
		$arr=$this->M->get_all("SELECT * from `lx_shop_car` where `id` in ($ids)");

		foreach ($arr as $key => &$m) {

			$arr2=$this->M->get_one("SELECT `name`,`img_url`,`now_price`,`id` from `lx_shop_product` where `id`='".$m['pid']."'");
			$m['product']=$arr2;
		}
		unset($m);
		return $arr;

	}



/**/

	public function get_uid(){
		if ($_SESSION['uid']>0) {
			$this->uid=$_SESSION['uid'];
		}else{
			R('home/reg/index');return false;
		}
	}




}