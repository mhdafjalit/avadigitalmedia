<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * The global CI helpers
 */
if ( ! function_exists('CI'))
{
	function CI()
	{
		if (!function_exists('get_instance')) return FALSE;
		$CI = &get_instance();
		return $CI;
	}
}

if (!function_exists('common_dropdown')) {

	function common_dropdown($name, $selval, $tbl_arr, $extra = '', $stval = '', $multiselect = FALSE) {

		$CI = CI();
		$select_fld = $tbl_arr['select_fld'];
		$tbl_name = $tbl_arr['tbl_name'];
		$where = $tbl_arr['where'];
		$fld_arr = explode(",", $select_fld);
		$id = $fld_arr[0];
		$title = $fld_arr[1];
		$query = $CI->db->query("select $select_fld from $tbl_name where 1 $where order by $title");
		$arr = array();
		if ($stval != 'no') {
			if ($stval != '') {
				$arr = array('' => $stval);
			} else {
				$arr = array('' => "Select One");
			}
		}
		if ($query->num_rows() > 0) {

			$res = $query->result();
			foreach ($res as $val) {
				$cid = $val->$id;
				$arr[$cid] = $val->$title;
			}
		}
		return ($multiselect) ? form_multiselect($name, $arr, $selval, $extra) : form_dropdown($name, $arr, $selval, $extra);
		//return form_dropdown($name,$arr,$selval,$extra);
	}
}

if (!function_exists('get_title_by_id')) {

	function get_title_by_id($tbl_name, $getfld, $condarr) {

		if(count($condarr) > 0 && is_array($condarr)) {

			$ci = CI();

			$cond = "where 1";

			foreach ($condarr as $key => $value) {

				$cond .=" and $key='" . $value . "'";

			}

			$qry = $ci->db->query("select `$getfld` from $tbl_name $cond");

			// echo $ci->db->last_query();exit;



			if ($qry->num_rows() > 0) {



				$res = $qry->row();

				return $res->$getfld;

			}

		}

	}



}


if (!function_exists('substring')) {

	function substring($string, $len) {

		$string = strip_tags($string);
		$string = character_limiter($string, $len, '...');
		return $string;
	}
}







if (!function_exists('get_cms_page')) {



	function get_cms_page($Id)

	{
		$CI = CI();
		$CI->db->select("page_name,page_description,page_short_description");
		$CI->db->where("status",'1');
		$CI->db->where("page_type",'1');
		$CI->db->where("page_id",$Id);
		$qry=$CI->db->get('tbl_cms_pages');
		$res=array();

		if($qry->num_rows() > 0)

		{

			$res=$qry->row_array();

		}

		return $res;

	}

}







if(!function_exists('get_expiry_date'))

{

	function get_expiry_date($no_of_days, $rttype = 'DAY')

	{

		$CI = CI();



		$currdate = $CI->config->item('config.date.time');

		$rs = $CI->db->query("SELECT DATE_ADD('" . $currdate . "', INTERVAL $no_of_days $rttype) as expdate ");



		$res = $rs->row_array();



		$expdate = $res['expdate'];

		return $expdate;

	}

}







if (!function_exists('get_next_auto_id')) {


	function get_next_auto_id($tblname, $prefix = 'DOM-') {

		$ci             = CI();
		$ddsql          = $ci->db->query("select DATABASE()");
		$ddres          = $ddsql->row_array();
		$dbname         = $ddres['DATABASE()'];
		$sql            = "SHOW table status from $dbname where name = '" . $tblname . "' ";
		$query          = $ci->db->query($sql);
		$result         = $query->row_array();
		$auto_increment = $prefix.$result["Auto_increment"];
		return $auto_increment;

	}



}


function db_option_value($varg=array()){

	$varg['default_text']=!array_key_exists('default_text',$varg) ? "Select " : $varg['default_text'];

	$opt_val_fld=!array_key_exists('opt_val_fld',$varg) ? "id" : $varg['opt_val_fld'];

	$opt_txt_fld=!array_key_exists('opt_txt_fld',$varg) ? "" : $varg['opt_txt_fld'];

	$cond=!array_key_exists('cond', $varg)?"":$varg["cond"];

	$table_name=!array_key_exists('table_name',$varg) ? "" : $varg['table_name'];

	$orderby=!array_key_exists('orderby',$varg) ? "id ASC" : $varg['orderby'];



	$selected_val=!array_key_exists('current_selected_val',$varg) ? "" : $varg['current_selected_val'];

	$CI = CI();

	$CI->db->select('*');

	if($cond!="")$CI->db->where($cond);

	$CI->db->order_by($orderby);

	$query=$CI->db->get($table_name);



	$option='';

	$arr=array();

	if($varg['default_text']!=""){

		$option .='<option value="">'.$varg['default_text'].'</option>';

	}

	if($query->num_rows() > 0){

		$res=$query->result();

		$opt_txt_fld=explode(",",$opt_txt_fld);

		foreach($res as $val){

			$cid=$val->$opt_val_fld;
			$sel="";
			
			if((is_array($selected_val) && in_array($cid,$selected_val)) || $selected_val==$cid) {
				$sel='selected="selected"';			
			}
			$option .='<option value="'.$cid.'" '.($sel).'>'.char_limiter($val->$opt_txt_fld[0],80).(isset($opt_txt_fld[1])?" ( ".$val->$opt_txt_fld[1]." ) ":"").'</option>';

		}

	}

	return $option;

}

 function percentvalue($ordprice,$discountprice){
     
	 if($discountprice > 0 && $ordprice > $discountprice){
	   $pervalue=$ordprice-$discountprice;
	   $dispercent=ceil($pervalue*100/$ordprice);
	   return $dispercent;
	 }
 }
 
  function percentvaluetext($ordprice,$discountprice){
     
	 if($discountprice > 0 && $ordprice > $discountprice){
	   $pervalue=$ordprice-$discountprice;
	   $dispercent=ceil($pervalue*100/$ordprice);
	   return $dispercent.'% Off';
	 }
 }
 
 function getorigin($originids){
    $rwdata=get_db_multiple_row("wl_countries","country_name","status ='1' AND id IN ($originids)");
	$orginArr='';
	$ctr=1;
	if(is_array($rwdata) && count($rwdata) > 0){
	  foreach($rwdata as $orgVal){
	   $orginArr.=$orgVal['country_name'];
	   if($ctr < count($rwdata)){
	     $orginArr.=",";
	   }
	   $ctr++;
	  }
	}
	return $orginArr;
 }	
 

  function getmem_wallet($memId){
	$rwdata=get_db_multiple_row("wl_wallet","transaction_amount,transaction_type","user_id ='".$memId."' ");
	$total=0;
	$totcr=0;
	$totdr=0;
	if(is_array($rwdata) && count($rwdata) > 0){
		foreach($rwdata as $val){
			if($val['transaction_type']=='Cr'){
				$totcr=$totcr+$val['transaction_amount'];
			}
			if($val['transaction_type']=='Dr'){
				$totdr=$totdr+$val['transaction_amount'];
			}
		}
	}
	return $totcr-$totdr;
   }
 


 function getcountry($contID){
   return $weigtunit=get_db_field_value("wl_countries","country_name"," AND id='$contID'");
 }
 
  function getstate($stateID){
   return $weigtunit=get_db_field_value("wl_states","title"," AND id='$stateID'");
 }
 
  function getcity($cityid){
   return $weigtunit=get_db_field_value("wl_cities","title"," AND id='$cityid'");
 }
 
 function getcategoryName($catID){
   return get_db_field_value("tbl_categories","category_name"," AND category_id='$catID'");
 } 
 
 function membership_invoice($order,$meminfo,$pintinv=FALSE){
	   $CI = CI();
	   $rwmship=get_db_single_row("wl_membership","*"," AND id='".$order['memship_id']."'");
	   $durArr=$CI->config->item('durArr');
	  ?>
	 <div class="invoice">
<div class="thnku-bg">
<p class="fs18 red">Thank You, <?php echo @$meminfo['first_name'];?> <?php echo @$meminfo['last_name'];?>!</p>
<p>Your Order has been placed successfully.</b></p>
<div class=" clearfix"></div>
</div>

<div class="mt-3">
<p class="float-right pr-3"><img src="<?php echo theme_url();?>images/logo.jpg" width="150" alt=""></p>
<div class="addrs">
<p><b class="black fs18">ZEB Organic</b> <?php echo @$CI->admin_info->address;?><br>
<span><strong>Ph No. :</strong> <?php echo @$CI->admin_info->phone;?></span>, <strong>Date:</strong> <?php echo getdateFormat($order['order_date'],1);?></p>
</div>
<p class="clearfix"></p>

<div class="row">
<div class="col-lg-6 p-1 mt-2">
<div class="inv_box3">
<h5 class="black">Order Summary</h5>
<div class="mt-1">
<p><span class="font-weight-bold black">Invoice ID:</span> <?php echo $order['order_id'];?></p>
<p><span class="font-weight-bold black">Payment Status:</span> <?php echo ($order['pay_status']==1)?'Pending':'Paid';?></p>
<p class="mt-2"><span class="font-weight-bold black">Sub Total:</span> <?php echo display_price($order['price']);?></p>
<p class="red font-weight-bold fs15">Total Payable Amount : <?php echo display_price($order['price']);?></p>
</div>
</div>
</div>

<div class="col-lg-6 p-1 mt-2">
<div class="inv_box3">
<p class="black fs16 text-uppercase weight700">Delivery Address</p>
<p class="blue fs15 weight700 mt-1"><?php echo @$meminfo['first_name'];?> <?php echo @$meminfo['last_name'];?></p>
<p class="mt-1"><b>Phone No.:</b> <?php echo @$meminfo['mobile_number'];?> </p>
<p class="mt-1"><b>Email</b>: <?php echo @$meminfo['user_name'];?></p>
</div>
</div>

</div>

<div class="black border1 mt-3 bg-light m-1">
<div class="p-3">
<p class="fs22"><?php echo @$rwmship['title'];?></p>
<div class="row">
<div class="col-md-6 p-3 mt-3 bg-white">
<p class="fs16 text-success text-uppercase">Feature Details</p>
<p class="mt-1">Free Delivery</p>
<p class="mt-1">Cash on Delivery</p>
<p class="mt-1">Membership Price on Products</p>
</div>

<div class="col-md-6 p-3 mt-3 bg-white">
<div class="row fs16">
<p class="col-12 no_pad mt-1"><b>Duration: </b><br> <?php echo $durArr[$order['duration']];?></p>
<p class="col-12 no_pad mt-3"><b>Amount: </b><br>  <?php echo display_price($order['price']);?></p>
</div>
</div>
</div>
<div class="mt-3"><?php echo @$rwmship['description'];?></div>
</div>
</div>
  
  <div class="mt-4">
  <p class="text-center fs18 red font-weight-bold">Grand Total: <?php echo display_price($order['price']);?></p>
  </div>
</div>
      </div>
      <p class="text-center d-none d-lg-block">
   <?php if($pintinv){?>
          <a href="javascript:print();" ><b class="fas fa-print"></b> Print Invoice</a>
   <?php }else{?>
          <a href="<?php echo site_url("member/printmemshipinvoice/".md5($order['order_id']));?>" class="pop3" data-fancybox data-type="iframe"><b class="fas fa-print"></b> Print Invoice</a>
   <?php } ?>   
      </p>
 <?php }