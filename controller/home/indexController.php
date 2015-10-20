
<?php
/**
 * index控制器
 * 
 * 
 */
class indexController extends Controller{
	public $user_info=null;
	public $wx=NULL;
	public $uid=NULL;
	public $type=NULL;
	//public $uid=203;
	public function __construct(){

		parent::__construct();
		error_reporting(E_ALL);

		$this->get_help();
		 //$this->QQ_callback();
		$this->get_set();

		 $this->assign("p",0);
		sadsa
		11.25

	}





/*业务逻辑部分*/
	public function index(){
		$this->get_index();

		$this->assign("p",0);
		$this->display("index.html");
	}

	public function gs(){
		$this->get_text(1);
		$this->assign("p",1);
		$this->display("gs.html");
	}

	public function ds(){
		$this->get_text(2);
		$this->assign("p",2);
		$this->display("ds.html");
	}

	public function kt(){
		$this->get_text(3);
		$this->assign("p",3);
		$this->display("kt.html");
	}

	public function tj(){
		$this->get_tj();
		$this->assign("p",4);
		$this->display("tj.html");
	}

	public function cs(){
		$this->get_cs();
		$this->assign("p",5);
		$this->display("cs.html");
	}

	public function bz(){
		$this->get_bz();
		$this->get_question();
		$this->assign("p",6);
		$this->display("bz.html");
	}
	public function help($id=""){
		$this->get_help_detail($id);		
		$this->display("help.html");
	}

	public function about($id=""){
		$this->get_help(2);	

		$this->get_about($id);	
		$this->display("about.html");
	}

	public function join($id=""){
		$this->get_job();	

		
		$this->display("join.html");
	}






public function get_news($from=0){


	$sp=5;
	$n1=$from*$sp;
	$n2=$from;

	$arr=$this->M->get_all("SELECT * from `lx_article` where `status`=1 order by `t4` desc,`id` desc limit $n1,$sp");
	$n2++;

	foreach ($arr as $key => &$e) {
		$e['ny']=date("Y",$e['t4']);
		$e['ny2']=$e['t4'];
		$e['t4']=date("Y年m月",$e['t4']);
		$e['t2']=htmlspecialchars_decode($e['t2']);
		$e['t2']=strip_tags($e['t2']);
		$e['n2']=$n2;
		
	}
	unset($e);

	if ($from>0) {
		$arr=$arr;	
	}else{
		$arr2=array_slice ($arr,0,4);//取$arr前四项
		$arr=array_slice ($arr,4);//取$arr第四项以后的全部		
	}

	$arr=multi_array_sort($arr,'ny2');//多维数组排序，默认倒序
	$new_arr=array();
	foreach ($arr as $key => $e) {
		$new_arr[$e['ny']][]=$e;//将同一年的放入一个数组
	}
	$new_arr=array_values($new_arr);//返回数组中所有的键值，但不保留键名，结果为数字数组
	//rsort($new_arr);
	//var_dump($new_arr);die;
	
	if ($from>0) {
		$new_arr=json_encode($new_arr);	
		echo $new_arr;
	}else{
		$this->assign("list",$new_arr);
		$this->assign("list2",$arr2);	
		$this->display("news.html");
	}
}



//自己写的新闻方法，还不能完全实现
	public function news_wen(){
		$article=$this->M->get_all("SELECT * FROM `lx_article` order by `t4` desc");
		$article_new=array();
		$article_newest=array();
		$i=0;
		$j=0;
		foreach($article as $key =>$v){
			$v['year']=date('Y',$v['t4']);
			$v['month']=date('m',$v['t4']);
			if($i<4){
				$article_newest[$i]=$v;
				$i++;
			}else{
				if($j>0){
					if($v['year']!=$article_new[$j-1]['year']){
						$v['year_flag']=1;
					}else{
						$v['year_flag']=0;
					}
					$article_new[$j]=$v;
				}else{
					$v['year_flag']=0;
					$article_new[$j]=$v;
				}
				$j++;
			}
		}		
		var_dump($article_new);die;
		$this->assign('article_new',$article_new);
		$this->assign('article_newest',$article_newest);
		$this->display('news.html');
	}
	public function news_detail($id){
		$a_detail=$this->M->get_one("SELECT * FROM `lx_article` where `status`=1 and `id`='".$id."'");
		$a_detail['time']=date('Y年m月',$a_detail['t4']);
		$a_detail['t2']=htmlspecialchars_decode($a_detail['t2']);
		$a_images=$this->M->get_all("SELECT * FROM `lx_cc` where `status`=1 and `tid`='".$id."'");
		$prev=$this->M->get_one("SELECT `t1`,`id` from `lx_article` where `status`=1 and `id`<'".$id."' order by `id` desc limit 1");

		$next=$this->M->get_one("SELECT `t1`,`id` from `lx_article` where `status`=1 and `id`>'".$id."' order by `id` asc limit 1");
		$this->assign('a_detail',$a_detail);
		$this->assign('a_images',$a_images);
		$this->assign('prev',$prev);
		$this->assign('next',$next);
		//var_dump($a_detail);die;
		$this->display('news_detail.html');
	}
//wu
	public function new_detail_wu(){
		$prev=$this->M->get_one("SELECT `t1`,`id` from `lx_article` where `status`=1 and `id`<'".$id."' order by `id` desc limit 1");

		$next=$this->M->get_one("SELECT `t1`,`id` from `lx_article` where `status`=1 and `id`>'".$id."' order by `id` asc limit 1");
		$this->assign('prev',$prev);
		$this->assign('next',$next);

	}


/*数据部分*/


public function get_set(){
	$arr=$this->M->get_one("SELECT * from `lx_set` where `id`=1");
	//$arr['t12']=explode('+', $arr['t12']);
	$this->assign("set_list",$arr);
	return $arr;
}


public function get_index($id=""){
	$arr=$this->M->get_all("SELECT * from `lx_index`");
	//var_dump($arr);
	$this->assign("index_list",$arr);
}

public function get_text($id=""){
	$arr=$this->M->get_all("SELECT * from `lx_text` where `tid`='".$id."'");
	
	foreach ($arr as $key => &$e) {
		$e['b1']=str_replace("\n","<br>",$e['b1']);
		$e['b2']=str_replace("\n","<br>",$e['b2']);
	
	}
	unset($e);

	$this->assign("text_list",$arr);
}

public function get_tj($id=""){
	$arr=$this->M->get_all("SELECT * from `lx_tuji` order by `id` desc");
	$this->assign("tj_list",$arr);
}
public function get_cs($id=""){
	$arr=$this->M->get_one("SELECT * from `lx_parameter`");
	$arr['d4']=str_replace("\n","<br>",$arr['d4']);
	$arr['d5']=str_replace("\n","<br>",$arr['d5']);
	$arr['d6']=str_replace("\n","<br>",$arr['d6']);
	$arr['d7']=str_replace("\n","<br>",$arr['d7']);

	
	$this->assign("cs_list",$arr);
}
public function get_bz($id=""){
	$arr=$this->M->get_one("SELECT * from `lx_change`");
	$arr['e2']=str_replace("\n","<br>",$arr['e2']); 
	$arr['e4']=str_replace("\n","<br>",$arr['e4']); 
	$arr['e15']=str_replace("\n","<br>",$arr['e15']); 
	$arr['e17']=str_replace("\n","<br>",$arr['e17']); 
	$arr['e20']=str_replace("\n","<br>",$arr['e20']); 
	$arr['e22']=str_replace("\n","<br>",$arr['e22']); 
	$this->assign("bz_list",$arr);
}
public function get_question($id=""){

	$arr=$this->M->get_all("SELECT * from `lx_question` order by `id` desc");
	$this->assign("qs_list",$arr);
}
public function get_help($id=1){
	$arr=$this->M->get_all("SELECT * from `lx_g` where `tid`='".$id."'");
	foreach ($arr as $key => &$e) {
		$arr2=$this->M->get_all("SELECT * from `lx_h` where `tid`='".$e['id']."'");
		$e['h']=$arr2;
	}
	unset($e);
	$this->assign("g_list",$arr);
}


public function get_help_detail($id=""){
	$arr=$this->M->get_one("SELECT * from `lx_h` where `id`='".$id."'");
	$arr['h2']=htmlspecialchars_decode($arr['h2']);
	$arr['g']=$this->M->get_one("SELECT * from `lx_g` where `id`='".$arr['tid']."'");
	$this->assign("list",$arr);
}



public function get_about($id=""){
	if ($id=="") {
		$arr=$this->M->get_one("SELECT * from `lx_h` where `tid`=2");
	}else{

		$arr=$this->M->get_one("SELECT * from `lx_h` where `id`='".$id."'");
	}
	$arr['h2']=htmlspecialchars_decode($arr['h2']);
	$arr['g']=$this->M->get_one("SELECT * from `lx_g` where `id`='".$arr['tid']."'");
	$this->assign("list",$arr);
}


public function get_job($id=""){
	$arr=$this->M->get_all("SELECT * from `lx_job` order by `id` desc");
	$this->assign("job_list",$arr);
}


public function do_sub(){
	$data=$_POST;
	$data['c_time']=$data['u_time']=time();
	$data['status']=1;
	$this->M->insert("lx_join_list",$data);
	echo $this->M->insert_id();
}







public function lay_out(){
	$_SESSION['uid']='';
	R("home/index/index");
}




public function get_sj_code($tel=""){
	$data[0]=make_coupon_card(4);
	$data[1]=1;
	$this->send_sms($tel,$data);
}



public function get_ercode($id=""){
	$url=URL."/index.php/home/index/item_detail/id/".$id;
	$this->get_qr($url);
}










}