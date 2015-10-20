<?php
/**
 * 管理中心
 */

/**
* 
*/
class adminController extends Controller
{
	
	public $sid;
	function __construct()
	{	

		parent::__construct();
		error_reporting(E_ALL);
		$_POST=str_safe($_POST);
		$this->check_login();

	}



// public function index(){
// 	$arr=$this->M->get_all("SHOW FULL FIELDS FROM `lx_prize`");
// 	var_dump($arr);
// }












	public function set_do_edit($id=''){
		
		$arr=$_POST;
		foreach ($arr as $key => $e) {
			$data[$key]=$e;
		}
	
		$this->M->update('lx_set',$data,"`id`='".$id."'");
		
		$id=$this->M->affected_rows();

		echo 1;	
	}



	








	/*public function w_list($table="",$sp="",$img_type="",$tid="",$table2="",$item_id=""){
		
		if ($img_type) {
			$arr=$this->M->get_all("SELECT * from `lx_$table` where `img_type`='".$img_type."' order by `id` desc");
		}else if($tid){
			$arr=$this->M->get_all("SELECT * from `lx_$table` where `tid`='".$tid."' order by `id` desc");
		}else{
			$arr=$this->M->get_all("SELECT * from `lx_$table` order by `id` desc");	
		}
		if ($item_id) {
			$arr=$this->M->get_all("SELECT * from `lx_$table` where `item_id`='".$item_id."' order by `id` desc");
		}
		


		$arr2=$this->add_table($arr,$this->get_table_fields($table));
		
		// $this->assign('list',$arr);
		$this->assign('table',$table);
		$this->assign('sp',$sp);
		$this->assign('img_type',$img_type);
		$this->assign('tid',$tid);
		$this->assign('table2',$table2);

		if ($table=='item') {
			$this->display("item_list.html");
		}else{

			$this->display("list.html");
		}
		
	}*/

	/*2015.10.12修改w_list开始*/
	public function w_list($table="",$sp="",$img_type="",$tid="",$table2="",$item_id="",$page=1){
		
		$str="1=1";//
		$ord="`id` desc";//排序
		$pn=20;//每一页多少条记录
		$page=trim($page);


		if ($img_type) {
			$str.=" and `img_type`='".$img_type."'";
			
		}else if($tid){
			$str.=" and `tid`='".$tid."'";
			
		}else{
			//$arr=$this->M->get_all("SELECT * from `lx_$table` order by `id` desc");	
		}
		if ($item_id) {
			$str.=" and `item_id`='".$item_id."'";
			
		}

		$row=$this->M->get_one("SELECT count(*) as a from `lx_$table` where $str order by $ord");
		$page2=$this->Page($page,$row['a'],$pn);	 
		$select_from=$page2['select_from'];
		$select_limit=$page2['select_limit'];	
		$arr=$this->M->get_all("SELECT * from `lx_$table` where $str order by $ord limit $select_from,$select_limit");


		$arr2=$this->add_table($arr,$this->get_table_fields($table));
		
		// $this->assign('list',$arr);
		$this->assign('pagenav',$page2['pagenav']);
		$this->assign('table',$table);
		$this->assign('sp',$sp);
		$this->assign('img_type',$img_type);
		$this->assign('tid',$tid);
		$this->assign('table2',$table2);

		if ($table=='item') {
			$this->display("item_list.html");
		}else{

			$this->display("list.html");
		}
		
	}
	/*2015.10.12修改结束*/



	public function add($table="",$img_type="",$tid=""){


		$this->add_html($table);
		
		$this->assign('table',$table);
		$this->assign('img_type',$img_type);
		$this->assign('tid',$tid);
		$this->display("add.html");
	}
	public function do_add($table=""){
		
		$arr=$_POST;
		foreach ($arr as $key => $e) {
			$data[$key]=$e;
			$fnm=$this->get_field_type($key);
			if ($fnm['type']==8) {
				$data[$key]=strtotime($e);
			}
		}
		$data['c_time']=time();
		$data['status']=1;



		$this->M->insert('lx_'.$table,$data);
		$id=$this->M->insert_id();	
		if ($id>0) {
			echo 1;
		}else{
			echo -1;
		}
		
	}
	public function edit($id='',$table="",$sw=0){
			
		$arr=$this->M->get_one("SELECT * from `lx_$table` where `id`='".$id."'");
		//$arr['t8']=htmlspecialchars_decode($arr['t8']);
		$new_arr=array();
		$i=0;
		foreach ($arr as $key => $e) {
			$k=substr($key,0,1);
			if ($this->check_key($key)) {				
				$new_arr[$i][0]=$k;
				$new_arr[$i][1]=$key;
				$new_arr[$i][2]=$e;
				$i++;				
			}
		}
		//var_dump($new_arr);exit;
		$arr2=$this->edit_html($new_arr);
		$this->assign("list",$arr2);
		$this->assign("table",$table);
		$this->assign("id",$id);
		$this->assign("sw",$sw);
		$this->display("edit.html");
	}

	public function do_edit($id='',$table=""){
		
		$arr=$_POST;
		foreach ($arr as $key => $e) {
			$data[$key]=$e;
			$fnm=$this->get_field_type($key);
			if ($fnm['type']==8) {
				$data[$key]=strtotime($e);
			}
			
		}
	
		$this->M->update('lx_'.$table,$data,"`id`='".$id."'");
		
		$id=$this->M->affected_rows();

		echo 1;	
	}

	public function check_key($key=""){
		$arr=array("id","c_time","u_time","status");
		if (in_array($key, $arr)) {
			return false;
		}else{
			return true;
		}

	}

	public function get_field_type($name=""){
		$arr=$this->M->get_one("SELECT * from `lx_fields_set` where `field_name`='".$name."'");
		return $arr;
	}
	public function add_html($table=""){
		//SHOW FULL FIELDS FROM `lx_img`
		$arr=$this->M->get_all("SHOW FIELDS FROM `lx_$table`");
		//$arr=$this->M->query("SHOW FULL FIELDS FROM `lx_img`");
		$new_arr=array();
		$i=0;
		$k=0;
		$y=0;
		$t=0;
		foreach ($arr as $key => $e) {
			$fnm=$this->get_field_type($e['Field']);
			if ($fnm['is_edit']==1) {

				switch ($fnm['type']) {
					case '1':

						$new_arr[$i][0]='<input type="text" class="form-control1" name="'.$e['Field'].'" placeholder="">';
						break;
					case '2':
					
						$new_arr[$i][0]='<textarea name="'.$e['Field'].'" cols="50" rows="4" class="form-control1"></textarea>';
						break;	
					case '3':
						$new_arr[$i][0]='<script id="remark2" type="text/plain" name="'.$e['Field'].'" style="width:100%;height:400px;"></script><input type="hidden" id="w_re" value="">';
						$y=1;
						break;	
					case '4':
						$new_arr[$i][0]='<button class="" id="file_upload'.$k.'"><span>上传</span></button><label>预览：</label><img id="img'.$k.'" style="hight:64px;width:64px" src=""><input type="hidden" id="img_url'.$k.'" name="'.$e['Field'].'" value="">';
						$k++;
						break;	
					case '8':

						$new_arr[$i][0]='<input type="text" name="'.$e['Field'].'" data-field="datetime" data-format="yyyy-MM-dd hh:mm:ss"  class="form-control1" readonly>';
						$t=1;
						break;
					case '9':
						
							$spa1='<input type="radio" name="'.$e['Field'].'"  value="1">是';
							$spa2='<br><input type="radio" name="'.$e['Field'].'" checked value="0">否';

						$new_arr[$i][0]=$spa1.$spa2;
						
						break;


					default:
						# code...
						break;
				}
				$new_arr[$i][1]=$fnm['remark'];
				$i++;
				
			}
		}
		//var_dump($arr);
		$this->assign("k",$k);
		$this->assign("y",$y);
		$this->assign("t",$t);		
		$this->assign("list",$new_arr);
		return $new_arr;
	}
	public function edit_html($arr=""){
		$new_arr=array();
		$i=0;
		$k=0;
		$y=0;
		$t=0;
		foreach ($arr as $key => $e) {
			$fnm=$this->get_field_type($e[1]);
			if ($fnm['is_edit']==1) {
				
			
				switch ($fnm['type']) {
					case '1':

						$new_arr[$i][0]='<input type="text" class="form-control1" name="'.$e[1].'" value="'.$e[2].'" placeholder="">';
						break;
					case '2':

						$new_arr[$i][0]='<textarea cols="50" rows="4" class="form-control1" name="'.$e[1].'">'.$e[2].'</textarea>';
						break;	
					case '3':
						$new_arr[$i][0]='<script id="remark2" type="text/plain" name="'.$e[1].'" style="width:100%;height:400px;"></script><input type="hidden" id="w_re" value="'.$e[2].'">';
						$y=1;//编辑器
						break;	
					case '4':
						$new_arr[$i][0]='<button class="" id="file_upload'.$k.'"><span>上传</span></button><label>预览：</label><img id="img'.$k.'" style="hight:64px;width:64px" src="'.$e[2].'"><input type="hidden" id="img_url'.$k.'" name="'.$e[1].'" value="'.$e[2].'">';
						$k++;
						break;		
					case '8':

						$new_arr[$i][0]='<input type="text" name="'.$e[1].'" data-field="datetime" data-format="yyyy-MM-dd hh:mm:ss"  class="form-control1" readonly value="'.date("Y-m-d H:i:s",$e[2]).'">';
						$t=1;//日历
						break;
					case '9':
						if ($e[2]==1) {
							$spa1='<input type="radio" name="'.$e[1].'" checked value="1">是';
							$spa2='<br><input type="radio" name="'.$e[1].'" value="0">否';
						}else{
							$spa1='<input type="radio" name="'.$e[1].'"  value="1">是';
							$spa2='<br><input type="radio" name="'.$e[1].'" checked value="0">否';
						}
						$new_arr[$i][0]=$spa1.$spa2;
						
						break;
					default:
						# code...
						break;
				}
				$new_arr[$i][1]=$fnm['remark'];
				$i++;
			}
		}
		$this->assign("k",$k);
		$this->assign("y",$y);
		$this->assign("t",$t);

		return $new_arr;
	}

	// public function add_html($arr=""){

	// 	$new_arr=array();
	// 	$i=0;
	// 	foreach ($arr as $key => $e) {
	// 		$fnm=$this->get_field_type($e[1]);
	// 		switch ($fnm['type']) {
	// 			case '1':
	// 				$new_arr[$i][0]='<input type="text" name="'.$e[1].'" value="'.$e[2].'">';
	// 				break;
	// 			case '2':
	// 				$new_arr[$i][0]='<textarea cols="3" name="'.$e[1].'">'.$e[2].'</textarea>';
	// 				break;	
	// 			case '3':
	// 				$new_arr[$i][0]='<script id="remark2" type="text/plain" name="'.$e[1].'" style="width:100%;height:400px;"></script><input type="hidden" id="w_re" value="'.$e[2].'">';
	// 				break;	
	// 			case '4':
	// 				$new_arr[$i][0]='<button class="" id="file_upload"><span>上传</span></button><label>预览：</label><img id="img" style="hight:64px;width:64px" src="'.$e[2].'"><input type="hidden" id="img_url" name="'.$e[1].'" value="'.$e[2].'">';
	// 				break;													
	// 			default:
	// 				# code...
	// 				break;
	// 		}
	// 		$new_arr[$i][1]=$fnm['remark'];
	// 		$i++;
	// 	}
	// 	return $new_arr;
	// }
	public function get_table_fields($table=""){
		$arr=$this->M->get_all("SHOW FIELDS FROM `lx_$table`");//获取表中各列的信息
		//$arr=$this->M->query("SHOW FULL FIELDS FROM `lx_img`");
		$new_arr=array();
		$i=0;	
		foreach ($arr as $key => $e) {
			$new_arr[$i]=$e['Field'];//获取字段名
			$i++;
		}		
		return $new_arr;
	}

	public function add_table($arr="",$f_arr=""){
		
		$str="";
		foreach ($f_arr as $key => $e) {
			$fnm=$this->get_field_type($e);
			if ($fnm['is_show']==1) {	

				$str.='<th>'.$fnm['remark'].'</th>';
			}else{

			}
		}
		$arr2=array();
		$j=0;
		foreach ($arr as $key => $e) {
			$new_arr=array();
			$i=0;
			foreach ($e as $m => $n) {
				$fnm2=$this->get_field_type($m);

				if ($fnm2['is_show']==1) {
					if ($fnm2['type']==8) {
						$n=date("Y-m-d H:i:s",$n);
					}
					if ($fnm2['type']==3) {
						$n=htmlspecialchars_decode($n);
					}										
					$new_arr[$i][0]=$n;
					$new_arr[$i][1]=$fnm2['type'];

					$i++;
				}
			}
			unset($n);
			$arr2[$j][0]=$new_arr;
			$arr2[$j][1]=$e['id'];
			$j++;
		}
		unset($e);
		//var_dump($arr2);
		$this->assign("fields",$str);
		$this->assign("list",$arr2);
	}


public function join_list(){
	$arr=$this->M->get_all("SELECT * from `lx_join_list` order by `id` desc");
	foreach ($arr as $key => &$e) {
		$e['c_time']=date("Y-m-d H:i:s",$e['c_time']);
	}
	unset($e);
	$this->assign("list",$arr);
	$this->display("join_list.html");
}













	public function index_edit($id=''){
		
	
		$arr=$this->M->get_one("SELECT * from `lx_index` where `id`='".$id."'");
		//$arr['t8']=htmlspecialchars_decode($arr['t8']);
		//var_dump($arr);
		$this->assign("list",$arr);
		$this->display("index_edit.html");
	}




/**/
	public function activity(){
		
		$arr=$this->M->get_all("SELECT * from `lx_activity`");	
		foreach ($arr as $key => &$e) {

			switch ($e['status']) {
				case 0:
					$e['w_status']='正常';
					break;
				case 1:
					$e['w_status']='正常';
					break;	
				case -1:
					$e['w_status']='已禁用';
					break;								
				default:
					$e['w_status']='正常';
					break;
			}
			//echo make_coupon_card(); 
		}
		unset($e);
		
		$this->assign('list',$arr);
		$this->display("activity.html");
	}



	public function activity_add(){

		$this->display("activity_add.html");
	}
	public function activity_edit($id=''){
		
	
		$arr=$this->M->get_one("SELECT * from `lx_activity` where `id`='".$id."'");
		//$arr['t8']=htmlspecialchars_decode($arr['t8']);
		$this->assign("list",$arr);
		$this->display("activity_edit.html");
	}
	public function activity_do_edit($id=''){
		
		$arr=$_POST;
		foreach ($arr as $key => $e) {
			$data[$key]=$e;
		}
	
		$this->M->update('lx_activity',$data,"`id`='".$id."'");
		
		$id=$this->M->affected_rows();

		echo 1;	
	}

	public function activity_do_add(){
		
		$arr=$_POST;
		foreach ($arr as $key => $e) {
			$data[$key]=$e;
		}
		$data['c_time']=time();
		$data['status']=1;	
		$this->M->insert('lx_activity',$data);
		$id=$this->M->insert_id();	
		if ($id>0) {
			echo 1;
		}else{
			echo -1;
		}
		
	}

	public function do_delete($table="",$ids=""){
		if ($ids!="") {
			$ids=rtrim($ids,',');
			$this->M->query("DELETE from `lx_$table` where `id` in ($ids)");
		}else{
			$id=$_POST['id'];
			$this->M->query("DELETE from `lx_$table` where `id`='".$id."'");			
		}

		$p=$this->M->affected_rows();
		if ($p>0) {
			echo 1;
		}else{
			echo -1;
		}
		
	}

	public function do_enable($table=""){
		$id=$_POST['id'];
		$sta=$_POST['sta'];
		$data['status']=($sta==1)?-1:1;
		$this->M->update('lx_$table',$data,"`id`='".$id."'");
		$p=$this->M->affected_rows();
		if ($p>0) {
			echo 1;
		}else{
			echo -1;
		}
		
	}

/**/








	public function message(){
		
		$arr=$this->M->get_all("SELECT * from `lx_message` order by `id` desc");	
		foreach ($arr as $key => &$e) {


			$e['c_time']=date("Y-m-d H:i:s",$e['c_time']);
		}
		unset($e);
		
		$this->assign('list',$arr);
		$this->display("message.html");
	}
















	public function check_login(){

		$uid='';
		$p=is_admin_login();
		
			if($p>0){
				
				$uid=is_admin_login();	
			}else{

				R('wxy/lx/login');return false;
			
			}	
		
		$arr=$this->M->get_one("SELECT `grade` from `lx_admin` where `id`='".$uid."'");

		if ($arr['grade']>=100) {
			return $uid;
		}else{
			R('wxy/lx/login');return false;
		}

	}


	public function out(){
		session_destroy();
		R('lx/lx/login');exit;

	}






/*数据库备份*/

public function back_list(){
	$arr=getDir(ROOT_PATH.'/backup/');
	//var_dump($arr);
	$new_arr=array();
	$i=0;
	foreach ($arr as $key => $e) {
		$new_arr[$i][0]=$e;
		//$str=substr($e, 0,14);
		$str=substr($e,0,4)."-".substr($e,4,2)."-".substr($e, 6,2)." ".substr($e, 8,2).":".substr($e, 10,2).":".substr($e, 12,2);
		$new_arr[$i][1]=$str;

		$i++;
	}
	$this->assign("list",$new_arr);
	$this->display("back_list.html");
}

public function backup($type=1,$file=""){
	require_once(APP_LIB_PATH."/lib_DBManage.php");
	if ($type==1) {

		$db = new DBManage ( '127.0.0.1', 'root', 'root', 'lx_pd', 'utf8mb4' );
		// 参数：备份哪个表(可选),备份目录(可选，默认为backup),分卷大小(可选,默认2000，即2M)
		$db->backup ();		
	}


//分别是主机，用户名，密码，数据库名，数据库编码

	if ($type==2) {

		//导入
		$db = new DBManage ( '127.0.0.1', 'root', 'root', 'lx_pd', 'utf8mb4' );
		//参数：sql文件
		$db->restore ( ROOT_PATH.'/backup/'.$file.'/'.$file.'_v1.sql');	
	}
	if ($type==3) {
		deldir(ROOT_PATH.'/backup/'.$file.'/');
		echo 1;
	}


}




public function item_list($tid=0,$key=""){
	$this->get_type_list();
	$time=time();
	$set=$this->M->get_one("SELECT * from `lx_set`");

	$str="1=1";
	$order="`id` desc";
	$limit="1000";

	if ($tid>0) {
		$str.=" and `tid`='".$tid."'";
	}
	if ($key!="") {
		$str.=" and `p1` like '%".$key."%'";
	}

	$arr=$this->M->get_all("SELECT * from `lx_item` where $str order by $order limit $limit");
	foreach ($arr as $key => &$e) {
		$e['item']=$this->get_type($e['tid']);

		if ($time>$e['p6']&&$time<$e['p7']) {
			$e['sp']=1;//进行中
			$e['sp_str']='进行中';//开始
			if (($time-$e['p7'])/3600<$set['s2']) {
							
				$e['sp_str']='即将结束';//开始
			}
		}elseif($time<$e['p6']){
			$e['sp']=-1;//预热中
			$e['sp_str']='预热中';
			if (($e['p6']-$time)/3600<$set['s1']) {
							
				$e['sp_str']='即将开始';//开始
			}			

		}elseif ($time>$e['p7']) {
			$e['sp']=-2;//已结束
			$e['sp_str']='已结束';
		}else{
			$e['sp']=0;
		}

	}
	unset($e);
	$this->assign("list",$arr);
	$this->assign("tid",$tid);
	$this->display("item_list.html");
}
public function get_type($id=""){
	$arr=$this->M->get_one("SELECT * from `lx_type` where `id`='".$id."'");
	return $arr;
}
public function get_type_list($id=""){
	$arr=$this->M->get_all("SELECT * from `lx_type`");
	$this->assign("type_list",$arr);
	return $arr;
}

public function get_user($id=""){
	$arr=$this->M->get_one("SELECT * from `lx_user` where `id`='".$id."'");
	return $arr;
}

public function order_list($status=0,$key="",$item_name="",$prize_name="",$user_name="",$w_t1="",$w_t2=""){
	$str="1=1";
	$order="`id` desc";
	$limit="1000";


	if ($key!="") {
		$str.=" and `order_num` like '%".$key."%'";
	}

	if ($item_name!="") {
		
		$str="`tid` in (SELECT `id` from `lx_item` where `p1` like '%".$item_name."%')";
	}
	if ($prize_name!="") {
		$str.=" and `pid` in (SELECT `id` from `lx_prize` where `pz_t1` like '%".$prize_name."%')";
	}
	if ($user_name!="") {
		$str.=" and `uid` in (SELECT `id` from `lx_user` where `nickname` like '%".$user_name."%')";
	}
	if ($w_t1!="") {
		$w_t1=strtotime($w_t1);
		$str.=" and `c_time`>$w_t1";
	}
	if ($w_t2!="") {
		$w_t2=strtotime($w_t2);
		$str.=" and `c_time`<$w_t2";
	}

	if ($status>0) {
		$str.=" and `status`='".$status."'";
	}
	

	$arr=$this->M->get_all("SELECT * from `lx_order_list` where $str order by $order limit $limit");
	$pay_money=0;
	foreach ($arr as $key => &$e) {
		$e['c_time']=date("Y-m-d H:i:s",$e['c_time']);
		$e['item']=$this->M->get_one("SELECT `id`,`p1` from `lx_item` where `id`='".$e['tid']."'");
		$e['prize']=$this->M->get_one("SELECT * from `lx_prize` where `id`='".$e['pid']."'");
		$e['user_info']=$this->get_user($e['uid']);

		switch ($e['status']) {
			case 1:
				$e['status']='未支付';
				break;
			case 2:
				$e['status']='已支付';
				$pay_money=$pay_money+$e['money'];
				break;	
			case 3:
				$e['status']='已发货';
				break;								
			default:
				# code...
				break;
		}
	}
	unset($e);
	$this->assign("list",$arr);
	$this->assign("status",$status);
	$this->assign("pay_money",$pay_money);
	$this->display("order_list.html");
}

public function send_prize($id=""){
	$data['status']=3;
	$this->M->update("lx_order_list",$data,"`id`='".$id."'");
	echo $this->M->affected_rows();
}

public function user_list($status=0,$key=""){
	$str="1=1";
	$order="`id` desc";
	$limit="1000";

	if ($status>0) {
		$str.=" and `status`='".$status."'";
	}
	if ($key!="") {
		$str.=" and `nickname` like '%".$key."%' or `phone` like '%".$key."%'";
	}


	$arr=$this->M->get_all("SELECT * from `lx_user` where $str order by $order limit $limit");
	foreach ($arr as $key => &$e) {
		$e['c_time']=date("Y-m-d H:i:s",$e['c_time']);
	}
	unset($e);
	$this->assign("list",$arr);
	$this->assign("status",$status);
	$this->display("user_list.html");
}

public function out_money_list($status=0,$key="",$type=0,$uid=""){
	$str="1=1";
	$order="`id` desc";
	$limit="1000";

	if ($status!=0) {
		$str.=" and `status`='".$status."'";
	}
	if ($key!="") {
		$str.=" and `nickname` like '%".$key."%' or `phone` like '%".$key."%'";
	}
	if ($type!=0) {
		$str.=" and `type`='".$type."'";
	}
	if ($uid!="") {
		$str.=" and `uid`='".$uid."'";
	}


	$arr=$this->M->get_all("SELECT * from `lx_money_record` where $str order by $order limit $limit");
	foreach ($arr as $key => &$e) {
		$e['c_time']=date("Y-m-d H:i:s",$e['c_time']);
		$e['user_info']=$this->get_user($e['uid']);


		switch ($e['status']) {

			case 1:
			if ($e['type']==1) {
				$e['status']="已增加";
			}else{

				$e['status']="正在申请";
			}
				break;
			case 2:
				$e['status']="已提现";
				break;
			case -1:
				$e['status']="已拒绝";
				break;							
			default:
				# code...
				break;
		}
		switch ($e['type']) {
			case 1:
				$e['type']="获得";
				break;
			case 2:
				$e['type']="提现";
				break;			
			default:
				# code...
				break;
		}


	}
	unset($e);
	$this->assign("list",$arr);
	$this->assign("status",$status);
	$this->assign("type",$type);
	$this->display("out_money_list.html");
}

public function do_out_money(){
	$data['status']=$_POST['status'];
	$id=$_POST['id'];
	$this->M->update("lx_money_record",$data,"`id`='".$id."'");

	echo $this->M->affected_rows();
	if ($data['status']==2) {

		$arr=$this->M->get_one("SELECT * from `lx_money_record` where `id`='".$id."'");
		$money=$arr['money'];
		$this->M->query("UPDATE `lx_user` set `money`=(`money`-$money) where `id`='".$arr['uid']."'");
		$this->M->query("UPDATE `lx_user` set `out_money`=(`out_money`+$money) where `id`='".$arr['uid']."'");
	}
}






public function express_list($order_id=""){

	$arr=$this->M->get_all("SELECT * from `lx_express`");
	$this->assign("list",$arr);
	$this->assign("order_id",$order_id);
	$this->display("express_list.html");
}

public function express_edit($id=""){
	$arr=$this->M->get_one("SELECT * from `lx_express` where `id`='".$id."'");
	$arr['e3']=htmlspecialchars_decode($arr['e3']);
	$this->assign("list",$arr);
	$this->assign("id",$id);
	$this->display("express_edit.html");
}
public function express_add(){

	$this->display("express_add.html");
}

public function send_list($order_id="",$id=""){
	$arr=$this->M->get_all("SELECT * from `lx_send_list`");
	$this->assign("list",$arr);
	$this->assign("order_id",$order_id);
	$this->assign("id",$id);
	$this->display("send_list.html");
}
public function express($order_id="",$id="",$send_id=""){
	$arr=$this->M->get_one("SELECT * from `lx_express` where `id`='".$id."'");
	$arr['e3']=htmlspecialchars_decode($arr['e3']);
	$arr2=$this->M->get_one("SELECT * from `lx_order_list` where `id`='".$order_id."'");
	$arr3=$this->M->get_one("SELECT * from `lx_send_list` where `id`='".$send_id."'");
	$arr2=json_encode($arr2);
	$arr3=json_encode($arr3);
	$this->assign("send_list",$arr3);
	$this->assign("express_list",$arr);
	$this->assign("order_list",$arr2);
	$this->display("express.html");
}


public function detailed_list($id=""){
	$arr=$this->M->get_one("SELECT * from `lx_order_list` where `id`='".$id."'");

	$arr2=$this->M->get_one("SELECT * from `lx_img` where `img_type`=6 and `status`=1 order by `id` desc");
	$this->assign("logo_list",$arr2);
	$arr3=$this->M->get_one("SELECT * from `lx_set` where `id`=1");
	$this->assign("set_list",$arr3);
	$arr['prize']=$this->M->get_one("SELECT * from `lx_prize` where `id`='".$arr['pid']."'");
	$arr['c_time']=date("Y-m-d H:i:s",$arr['c_time']);
	$this->assign("list",$arr);
	$this->display("detailed_list.html");
}







public function check_sms_code($sms_code=""){
	var_dump($_SESSION['sms_code']);
}
/*短信发送*/

	public function send_sms($to="13253631415",$datas=array('9292585','122','45'),$tempId=1){

		//主帐号
		$accountSid= 'aaf98f894fba2cb2014fbbf47fcc0333';

		//主帐号Token
		$accountToken= '7c29ea93fc3d4f439c21842fa9294277';

		//应用Id
		$appId='8a48b5514fba2f87014fbbf4c20408d1';

		//请求地址，格式如下，不需要写https://
		$serverIP='sandboxapp.cloopen.com';

		//请求端口 
		$serverPort='8883';

		//REST版本号
		$softVersion='2013-12-26';

		require_once(APP_LIB_PATH."/CCPRestSDK.php");
	     $rest = new REST($serverIP,$serverPort,$softVersion);
	     $rest->setAccount($accountSid,$accountToken);
	     $rest->setAppId($appId);	

 //echo "Sending TemplateSMS to $to <br/>";
     $result = $rest->sendTemplateSMS($to,$datas,$tempId);
     if($result == NULL ) {
         echo "result error!";
         break;
     }
     if($result->statusCode!=0) {
         echo "error code :" . $result->statusCode . "<br>";
         echo "error msg :" . $result->statusMsg . "<br>";
         //TODO 添加错误处理逻辑
     }else{
         // echo "Sendind TemplateSMS success!<br/>";
         // // 获取返回信息
         // $smsmessage = $result->TemplateSMS;
         // echo "dateCreated:".$smsmessage->dateCreated."<br/>";
         // echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";


         //TODO 添加成功处理逻辑
     	$_SESSION['sms_code']=$datas[0];
     	echo 1;
     }



	}


/**/

public function lay_out(){
	$_SESSION['admin_id']="";
	R("wxy/admin/index");//http://localhost/index.php/+$url,直接跳转
}














}







 
