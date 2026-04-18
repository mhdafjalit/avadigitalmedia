<?php

/**

* CI-CMS Upload class overwrite

* This file is part of CI-CMS

* @package   CI-CMS

* @copyright 2008 Hery.serasera.org

* @license   http://www.gnu.org/licenses/gpl.html

* @version   $Id$

*/



if (!defined('BASEPATH'))

{



	exit('No direct script access allowed');

}





class MY_Cart extends CI_Cart

{



	public  function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		// $this->CI->load->model(array('cart/cart_model'));
	}



	public function get_coupon_list($params=array()){
		$ret_cpn_arr = array();
		$list_from = !empty($params['list_from']) ? $params['list_from'] : "";
		$login_userId         = $this->CI->session->userdata('user_id');
		$guest_user           = $this->CI->session->userdata('guest');
		$user_id              = ( $login_userId>0 ) ? $login_userId : $guest_user;
		if($list_from=='cart'){
			if($this->total_items() > 0){
				$where = "status='1' AND start_date  <= '".$this->CI->config->item('config.date')."' AND end_date  >= '".$this->CI->config->item('config.date')."'";
				if(!$user_id){
					$where.=" AND coupon_for='0'";
				}
				$res_coupons = $this->CI->db->get_where('wl_coupons',$where)->result_array();
			}
		}else{
			$cat_id = !empty($params['cat_id']) ? (int) $params['cat_id'] : 0;
			$brand_id = !empty($params['brand_id']) ? (int) $params['brand_id'] : 0;
			$where = "status='1' AND start_date  <= '".$this->CI->config->item('config.date')."' AND end_date  >= '".$this->CI->config->item('config.date')."'";
			$where .= " AND cp_cat_id='".$cat_id."' AND cp_brand_id='".$brand_id."'";
			if(!$user_id){
				$where.=" AND coupon_for='0'";
			}
			$res_coupons = $this->CI->db->get_where('wl_coupons',$where)->result_array();
		}
		if(!empty($res_coupons)){
			foreach($res_coupons as $key=>$val){
				$opts = array('only_check'=>1);
				$res_cart_eligibility = $this->apply_coupon_code($val,$opts);
				if(!$res_cart_eligibility['err']){
					$ret_cpn_arr[] = $val;
				}
			}
		}
		if(!empty($ret_cpn_arr)){
			foreach($ret_cpn_arr as $key=>$val){
				$cpn_text = "<strong>(".$val['coupon_code'].")</strong> ";
				$min_order_amount = $val['minimum_order_amount'];
				$maximum_coupon_discount = $val['maximum_coupon_discount'];
				switch($val['coupon_type']){
					case 'p':
						$cpn_text .= '<strong class="blue4">'.fmtZerosDecimal($val['coupon_discount'])."%</strong> off";
						if($maximum_coupon_discount > 0){
							$cpn_text .= ' upto maximum<strong class="blue4">'.display_price($maximum_coupon_discount).'</strong>';
						}
						if($min_order_amount > 0){
							$cpn_text .= ' on<strong class="blue4">'.display_price($min_order_amount).'</strong> & Above.';
						}
					break;
					case 'a':
						$cpn_text .= '<strong class="blue4">'.display_symbol().fmtZerosDecimal($val['coupon_discount'])."</strong> off";
						if($maximum_coupon_discount > 0){
							$cpn_text .= ' upto maximum<strong class="blue4">'.display_price($maximum_coupon_discount).'</strong>';
						}
						if($min_order_amount > 0){
							$cpn_text .= ' on<strong class="blue4">'.display_price($min_order_amount).'</strong> & Above.';
						}
					break;
				}
				$ret_cpn_arr[$key]['cpn_txt'] = $cpn_text;
			}
		}
		return $ret_cpn_arr;
	}


	public function apply_coupon_code($discount_res,$opts=array()) {
		$login_userId         = $this->CI->session->userdata('user_id');
		$guest_user           = $this->CI->session->userdata('guest');
		$user_id              = ( $login_userId>0 ) ? $login_userId : $guest_user;
		if( is_array($discount_res) && !empty($discount_res)){
			$is_cat_type_coupon = $discount_res['category_id']>0;
			$cp_cat_id = $discount_res['category_id'];
			$cp_brand_id = 0;//$discount_res['cp_brand_id'];
			if($is_cat_type_coupon){
				$cart_total = 0;
				if($this->total_items() > 0){
					foreach($this->contents() as $items){
						 $prod_price = ($items['price']*$items['qty']);	
						if($items['last_node_cat_id'] == $cp_cat_id){
							if($cp_brand_id>0){
								if($items['options']['brand_id'] == $cp_brand_id){
									$cart_total+=	$prod_price;
								}
							}else{
								$cart_total+=	$prod_price;
							}
						}
					}
				}
			}else{
				$cart_total      = $this->total();
			}
			$current_order_amount = $cart_total;
			$current_order_discount=0;
			$discount_type   =  $discount_res['coupon_type'];
			$coupon_id = $discount_res['coupon_id'];

			$cpn_err = 0;

			$min_order_amount = $discount_res['minimum_order_amount'];

			/*Check Usage*/
			$coupon_usage = $discount_res['coupon_usage'];
			$coupon_for = $discount_res['coupon_for'];
			$usage_count = $coupon_usage=='single' ? 1 : $discount_res['usage_count'];
			if($user_id > 0){
				if($coupon_for=='1'){
					/*Whether user eligible to use*/
					$user_assigned =  $this->CI->cart_model->findCount('wl_coupon_customers',"coupon_id = '$coupon_id' AND customer_id = '$user_id'");
					if(!$user_assigned){
						$cpn_err=1;
						$cpn_err_code="CPN_ERR400";
						$cpn_msg="Coupon cannot be redeemed";
						$cpn_msg_type="error";
					}
				}
				if(!$cpn_err){
					$used_num =  $this->CI->cart_model->findCount('wl_order',"discount_coupon_id = '$coupon_id' AND customers_id = '$user_id'");
					if($used_num>=$usage_count){
						$cpn_err=1;
						$cpn_err_code="CPN_ERR400";
						$cpn_msg=$usage_count==1 ? "You have already redeemed the coupon." : "You have already redeemed the coupon.";//You can change the message for multiple here
						$cpn_msg_type="error";
					}
				}
			}else{
				if($coupon_for=='1'){
					$cpn_err=1;
					$cpn_err_code="CPN_ERR401";
					$cpn_msg="Coupon cannot be redeemed";
					$cpn_msg_type="error";
				}

			}



			/*Validate Minimum Amount if set*/
			if(empty($opts['only_check'])){
				if(!$cpn_err && !$cart_total){
					$cpn_err=1;
					$cpn_err_code="CPN_ERR403";
					$cpn_msg="Coupon not applicable for the items added.";
					$cpn_msg_type="warning";
				}
				if(!$cpn_err && $min_order_amount>0 && $min_order_amount>$cart_total){
					$cpn_err=1;
					$cpn_err_code="CPN_ERR403";
					$cpn_msg="Minimum order amount should be ".display_price($min_order_amount);
					if($is_cat_type_coupon){
						$cpn_msg.=". Current applicable order amount is ".display_price($cart_total);
					}
					$cpn_msg_type="warning";
				}

				if(!$cpn_err){
					if( $discount_type=='p' ){
						$maximum_coupon_discount = $discount_res['maximum_coupon_discount'];
						$current_order_discount = $current_order_amount*$discount_res['coupon_discount']*.01;
						$current_order_discount = formatNumber($current_order_discount,2);
						if($maximum_coupon_discount>0 && $maximum_coupon_discount<$current_order_discount){
								$current_order_discount = $maximum_coupon_discount;
								$cpn_msg="Coupon applied but maximum order discount restricted to ".display_price($maximum_coupon_discount);
								$cpn_msg_type="success";
						}
					}else{
						$current_order_discount = $discount_res['coupon_discount'];
						$current_order_discount = formatNumber($current_order_discount,2);
					}
				}
			}
			$discount_res['applied_coupon_amt'] = $current_order_discount;
			//It is the return step.All validation are checked
			$ret_arr =array();
			if(!$cpn_err){
				if(empty($opts['only_check'])){
					$this->CI->session->set_userdata(array('coupon_id'=>$discount_res['coupon_id'], 'discount_amount'=>$current_order_discount,'coupon_data'=>$discount_res) );
				}
				$ret_arr['err']=0;
			}else{
				$session_coupon_id = $this->CI->session->userdata('coupon_id');
				if($session_coupon_id==$coupon_id){
					$data2 = array('coupon_id', 'discount_amount','coupon_data');
					$this->CI->session->unset_userdata($data2);
				}
				$ret_arr['err']=1;
			}
			$ret_arr['msg']=!empty($cpn_msg) ? $cpn_msg : "Coupon has been applied successfully.";
			$ret_arr['msg_type']=!empty($cpn_msg_type) ? $cpn_msg_type : "success";
			$ret_arr['applied_coupon_res'] = $discount_res;
		}else{
			$ret_arr = array('err'=>1,'msg'=>'Invalid Coupon','msg_type'=>'error');
		}
		$ret_arr['cpn_err_code'] = !empty($cpn_err_code) ? $cpn_err_code : "";
		return $ret_arr;
	}
	
	public function apply_coupon_code_item($discount_res,$opts=array()) {
		$login_userId         = $this->CI->session->userdata('user_id');
		$guest_user           = $this->CI->session->userdata('guest');
		$user_id              = ( $login_userId>0 ) ? $login_userId : $guest_user;
		if( is_array($discount_res) && !empty($discount_res) && !empty($opts['cart_row'])){
			$is_cat_type_coupon = $discount_res['category_id']>0;
			$cp_cat_id = $discount_res['category_id'];
			$cp_brand_id = 0;//$discount_res['cp_brand_id'];
			/*if($is_cat_type_coupon){
				$cart_total = 0;
				if($this->total_items() > 0){
					foreach($this->contents() as $items){
						 $prod_price = ($items['price']*$items['qty']);	
						if($items['last_node_cat_id'] == $cp_cat_id){
							if($cp_brand_id>0){
								if($items['options']['brand_id'] == $cp_brand_id){
									$cart_total+=	$prod_price;
								}
							}else{
								$cart_total+=	$prod_price;
							}
						}
					}
					
				}
			}else{
				$cart_total      = $this->total();
			}*/
			$cart_total      = $opts['cart_row']['price']*$opts['cart_row']['qty'];
			$current_order_amount = $cart_total;
			$discount_type   =  $discount_res['coupon_type'];
			$coupon_id = $discount_res['coupon_id'];

			$cpn_err = 0;
			$current_order_discount=0;

			$min_order_amount = $discount_res['minimum_order_amount'];

			/*Check Usage*/
			$coupon_usage = $discount_res['coupon_usage'];
			$coupon_for = $discount_res['coupon_for'];
			$usage_count = $coupon_usage=='single' ? 1 : $discount_res['usage_count'];
			if($user_id > 0){
				if($coupon_for=='1'){
					/*Whether user eligible to use*/
					$user_assigned =  $this->CI->cart_model->findCount('wl_coupon_customers',"coupon_id = '$coupon_id' AND customer_id = '$user_id'");
					if(!$user_assigned){
						$cpn_err=1;
						$cpn_err_code="CPN_ERR400";
						$cpn_msg="Coupon cannot be redeemed";
						$cpn_msg_type="error";
					}
				}
				if(!$cpn_err){
					$used_num =  $this->CI->cart_model->findCount('wl_order',"discount_coupon_id = '$coupon_id' AND customers_id = '$user_id'");
					if($used_num>=$usage_count){
						$cpn_err=1;
						$cpn_err_code="CPN_ERR400";
						$cpn_msg=$usage_count==1 ? "You have already redeemed the coupon." : "You have already redeemed the coupon.";//You can change the message for multiple here
						$cpn_msg_type="error";
					}
				}
			}else{
				if($coupon_for=='1'){
					$cpn_err=1;
					$cpn_err_code="CPN_ERR401";
					$cpn_msg="Coupon cannot be redeemed";
					$cpn_msg_type="error";
				}

			}



			/*Validate Minimum Amount if set*/
			if(!$cpn_err && !$cart_total){
				$cpn_err=1;
				$cpn_err_code="CPN_ERR403";
				$cpn_msg="Coupon not applicable for the items added.";
				$cpn_msg_type="warning";
			}
			if(!$cpn_err && $min_order_amount>0 && $min_order_amount>$cart_total){
				$cpn_err=1;
				$cpn_err_code="CPN_ERR403";
				$cpn_msg="Minimum order amount should be ".display_price($min_order_amount);
				if($is_cat_type_coupon){
					$cpn_msg.=". Current applicable order amount is ".display_price($cart_total);
				}
				$cpn_msg_type="warning";
			}

			if(!$cpn_err){
				if( $discount_type=='p' ){
					$maximum_coupon_discount = $discount_res['maximum_coupon_discount'];
					$current_order_discount = $current_order_amount*$discount_res['coupon_discount']*.01;
					$current_order_discount = formatNumber($current_order_discount,2);
					if($maximum_coupon_discount>0 && $maximum_coupon_discount<$current_order_discount){
							$current_order_discount = $maximum_coupon_discount;
							$cpn_msg="Coupon applied but maximum order discount restricted to ".display_price($maximum_coupon_discount);
							$cpn_msg_type="success";
					}
				}else{
					$current_order_discount = $current_order_amount<$discount_res['coupon_discount'] ? $current_order_amount : $discount_res['coupon_discount'];
					$current_order_discount = formatNumber($current_order_discount,2);
				}
			}
			
			$discount_res['applied_coupon_amt'] = $current_order_discount;

			//It is the return step.All validation are checked
			$ret_arr =array();
			if(!$cpn_err){
				$ret_arr['err']=0;
			}else{
				$ret_arr['err']=1;
			}
			$ret_arr['msg']=!empty($cpn_msg) ? $cpn_msg : "Coupon has been applied successfully.";
			$ret_arr['msg_type']=!empty($cpn_msg_type) ? $cpn_msg_type : "success";
			$ret_arr['applied_coupon_res'] = $discount_res;
		}else{
			$ret_arr = array('err'=>1,'msg'=>'Invalid Coupon','msg_type'=>'error');
		}
		$ret_arr['cpn_err_code'] = !empty($cpn_err_code) ? $cpn_err_code : "";
		return $ret_arr;
	}
	
	public function get_discount_coupon($params=array()){
		$login_userId         = $this->CI->session->userdata('user_id');
		$guest_user           = $this->CI->session->userdata('guest');
		$user_id              = ( $login_userId>0 ) ? $login_userId : $guest_user;
		$cat_id = !empty($params['cat_id']) ? (int) $params['cat_id'] : 0;
		$brand_id = !empty($params['brand_id']) ? (int) $params['brand_id'] : 0;
		$coupon_code = !empty($params['coupon_code']) ? $params['coupon_code'] : "";
		$coupon_id = !empty($params['coupon_id']) ? (int) $params['coupon_id'] : 0;
		$where = "status='1' AND start_date  <= '".$this->CI->config->item('config.date')."' AND end_date  >= '".$this->CI->config->item('config.date')."'";
		$where .= " AND category_id='".$cat_id."' ";
		if(!$user_id){
			$where.=" AND coupon_for='0'";
		}
		if($coupon_id>0){
			$where.=" AND coupon_id='".$coupon_id."'";
		}
		if($coupon_code!=''){
			$where.=" AND coupon_code='".$this->CI->db->escape_str($coupon_code)."'";
		}
		$res_coupon = $this->CI->db->get_where('wl_coupons',$where)->row_array();
		return $res_coupon;
	}

	public function reform_cart_items(){
		$total_discount = 0;
		$discount_cent="";
		$cpn_msg="";
		$cpn_err_code="";
		$cpn_err=0;
		$cart_items = array();
		if($this->total_items() > 0){
			$coupon_id         = (int) $this->CI->session->userdata('coupon_id');
			if($coupon_id > 0){
				$prod_discount_res = $this->get_discount_coupon(array('coupon_id'=>$coupon_id));
			}
			$apply_cpn_res = array();
			if(!empty($prod_discount_res)){
				$apply_cpn_res =  $this->apply_coupon_code($prod_discount_res);
				$cpn_err=!empty($apply_cpn_res['err']);
				$cpn_msg=$apply_cpn_res['msg'];
				$cpn_err_code=$apply_cpn_res['cpn_err_code'];
			}
			$has_coupon_data = !empty($apply_cpn_res['applied_coupon_res']) ? 1 : 0; 
			$has_coupon_err = !empty($apply_cpn_res['err']); 
			if($has_coupon_data && !$has_coupon_err){
					$applied_coupon_res = $apply_cpn_res['applied_coupon_res'];
					$total_discount = $applied_coupon_res['applied_coupon_amt'];
					if($applied_coupon_res['coupon_type']=='p'){
						$discount_cent = $applied_coupon_res['coupon_discount'];
						$discount_cent = fmtZerosDecimal($discount_cent);
					}
			}
			
			$cart_items = $this->contents();
			/*foreach($cart_items as $key=>$items){
					$brand_id = 0;
					$cat_id = $items['last_node_cat_id'];
					if($items['options']['brand_id']>0){
						$brand_id = $items['options']['brand_id'];
					}
					$cart_items[$key] = $items;
			}*/
		}
		$ret_cart_items = array('cart_items'=>$cart_items,'coupon_discount'=>$total_discount,'discount_cent'=>$discount_cent,'coupon_applied_res'=>(!empty($applied_coupon_res) ? $applied_coupon_res : array()),'cpn_err'=>$cpn_err,'cpn_err_code'=>$cpn_err_code,'cpn_msg'=>$cpn_msg);
		unset($cart_items);
		return $ret_cart_items;
	}

}





?>