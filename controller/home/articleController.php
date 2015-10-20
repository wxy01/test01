<?php
/**
 * index控制器
 * 
 * 
 */
class articleController extends Controller{
	public $user_info=null;
	public $wx=NULL;
	public $uid=NULL;
	//public $uid=203;
	public function __construct(){
		parent::__construct();
		error_reporting(0);
		$this->get_article_type();
		
	}


	public function index(){

		$this->get_index_img();
		$this->display("index.html");
	}

	public function article_child_type($id=""){

		$this->get_article_child_type($id);
		$this->display("article_child_type.html");
	}
	public function article_list($id=""){

		$this->get_article_list2($id);
		$this->display("article_list.html");
	}

	public function article($id=""){

		$this->get_article($id);
		$this->display("article.html");
	}

	public function ordered($id=""){
		$this->assign("id",$id);
		$this->display("ordered.html");
	}
	public function sub_order(){
		$arr=$_POST;
		foreach ($arr as $key => $e) {
			$data[$key]=$e;
		}
		$data['c_time']=time();
		$data['status']=1;
		$this->M->insert("lx_ordered",$data);
		$id=$this->M->affected_rows();
		echo $id;
	}





	public function get_ad(){
		$arr=$this->M->get_one("SELECT * from `lx_ad` where `status`=1");
		$this->assign("ad_list",$arr);
	}
	/**/
	public function get_article_type(){
		$arr=$this->M->get_all("SELECT * from `lx_article` where `status`=1 order by `id` desc");
		$this->assign("article_type_list",$arr);
	}
	public function get_article_child_type($id=""){
		$arr=$this->M->get_all("SELECT * from `lx_article_child_type` where `parent_id`='".$id."' and `status`=1 order by `id` desc");
		foreach ($arr as $key => &$e) {
			$e['article_list']=$this->get_article_list($e['id']);
		}
		unset($e);
		$this->assign("article_child_type_list",$arr);
	}
	public function get_article_list($id=""){
		$arr=$this->M->get_all("SELECT `name`,`id` from `lx_article` where `tid`='".$id."' and `status`=1 order by `id` desc");
		// $this->assign("article_list",$arr);
		return $arr;
	}
	public function get_article_list2($id=""){
		$arr=$this->M->get_all("SELECT `id`,`img_url`,`abstract`,`name` from `lx_article` where `tid` in (SELECT `id` from `lx_article_child_type` where `parent_id`='".$id."') order by `id` desc");
		
		$this->assign("article_list",$arr);
		//return $arr;
	}


	public function get_article($id=""){
		$arr=$this->M->get_one("SELECT * from `lx_article` where `id`='".$id."' and `status`=1 ");
		$arr['content']=htmlspecialchars_decode($arr['content']);
	 	$this->assign("list",$arr);
		//return $arr;
	}
/**/

	public function get_product_lb(){
		$arr=$this->M->get_all("SELECT * from `lx_product` where `status`=1 and `is_lb`=1 order by `id` desc");
		foreach ($arr as $key => &$e) {
			$e['snum']=$this->get_num($e['id']);
		}
		unset($e);
		$this->assign("lb_list",$arr);
	}

	public function get_num($pid=""){
		$arr=$this->M->get_one("SELECT count(`id`) as a from `lx_buy_record` where `pid`='".$pid."' and `status`=1");
		return $arr['a'];
	}
	public function get_detail($id=""){
		$arr=$this->M->get_one("SELECT * from `lx_product` where `status`=1 and `id`='".$id."' ");
		$arr['content']=htmlspecialchars_decode($arr['content']);
		$this->assign("list",$arr);
		return $arr;
	}
	public function get_buy($id=""){
		$arr=$this->M->get_one("SELECT * from `lx_buy_record` where `status`=0 and `id`='".$id."' ");
		$this->assign("buy_list",$arr);
		return $arr['pid'];
	}
	public function get_set(){
		$arr=$this->M->get_one("SELECT * from `lx_set` where  `id`=1 ");
		$this->assign("set_list",$arr);
	}
	public function get_ask(){
		$arr=$this->M->get_all("SELECT * from `lx_ask` where  `status`=1 ");
		$this->assign("ask_list",$arr);
	}

	public function get_index_img(){
		$arr=$this->M->get_all("SELECT * from `lx_index` where  `status`=1 ");
		$this->assign("img_list",$arr);		
	}



}