<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

if( !function_exists('order_invoice_mail_content')){
	function order_invoice_mail_content(&$params=array()){
		$CI = CI();
		$arr_config_payment_types = $CI->config->item('arr_config_payment_types');
		$param_api_data = array();
		$order_id = (int) (isset($params['order_id']) ? $params['order_id'] : 0);
		if(isset($params['res_ordmaster'])){
			$param_api_data['res_ordmaster'] = $params['res_ordmaster'];
		}
		if(isset($params['res_ord_details'])){
			$param_api_data['res_ord_details'] = $params['res_ord_details'];
		}

		$param_api_data['order_id'] = $order_id;
		$invoice_data = get_invoice_api_data($param_api_data);
		$package_dtls_heading="Invoice";

		

		$ordmaster = $invoice_data['order_master_data'];
		$orddetail = $invoice_data['order_item_details_data'];

		$params['invoice_master_data']=$ordmaster;
		$params['invoice_item_details_data']=$orddetail;

		$admin_info_data = $invoice_data['company_info_data'];

		$admin_address="";
		if($admin_info_data['address']) {
			$admin_address.=	$admin_info_data['address'].', ';
		}
		if($admin_info_data['city']!='') {
			$admin_address.=	$admin_info_data['city'].', ';
		}
		if($admin_info_data['state']!='') {
			$admin_address.=	$admin_info_data['state'];
		}
		if($admin_info_data['zipcode']!='') {
			$admin_address.=	'-'.$admin_info_data['zipcode'];
		}
		if($admin_info_data['country']) {
			$admin_address.=	' <strong>'.$admin_info_data['country'].'</strong>';
		}

		//Billing Address
		$billing_address="";
		if($ordmaster['billing_address']) {
			$billing_address.=	$ordmaster['billing_address'].', ';
		}
		if($ordmaster['billing_city_name']!='') {
			$billing_address.=	$ordmaster['billing_city_name'].', ';
		}
		if($ordmaster['billing_state_name']!='') {
			$billing_address.=	$ordmaster['billing_state_name'];
		}
		if($ordmaster['billing_zipcode_name']!='') {
			$billing_address.=	'-'.$ordmaster['billing_zipcode_name'];
		}
		if($ordmaster['billing_country_name']) {
			$billing_address.=	' <strong>'.$ordmaster['billing_country_name'].'</strong>';
		}

		$payment_type = $arr_config_payment_types[$ordmaster['payment_type']];

		$igst = $ordmaster['gst_cent'];
		$igst = fmtZerosDecimal(formatNumber($igst,2));
		$cgst=$sgst=$igst/2;
		$cgst=$sgst = fmtZerosDecimal(formatNumber($cgst,2));

		$total_payable_amount = $ordmaster['total_payable_amount'];
		$invoice_amount= ($ordmaster['total_amount'] + (float) $ordmaster['shipping_amount'] + (float) $ordmaster['gst_amount'] + (float) $ordmaster['cod_amount']) - (float) $ordmaster['coupon_discount_amount'] -  (float) $ordmaster['wallet_amount'];
		$total_quantity=0;

		$currency_symbol = $ordmaster['currency_symbol'];
		//$amt_in_words = getIndianCurrency($invoice_amount);

		$is_pop_up = !empty($params['section']) && $params['section']=='popup' ? 1 : 0;

		ob_start();
		if($is_pop_up){
			require_once(APPPATH.'views/invoices/raw_invoice_with_style.php');
		}else{
			require_once(APPPATH.'views/invoices/raw_invoice_with_style.php');
		}
		$content_for_layout=ob_get_clean();
		return $content_for_layout;
	}

}

if(!function_exists('get_invoice_api_data')){
	function get_invoice_api_data($params=array()){
		$CI = CI();
		$order_id = (int) (isset($params['order_id']) ? $params['order_id'] : 0);
		if(!isset($params['res_ordmaster']) || !is_array($params['res_ordmaster'])){
			if($order_id > 0){
				$res_ordmaster = $CI->db->get_where('wl_order',array('order_id'=>$order_id))->row_array();
			}
		}else{
			$res_ordmaster = $params['res_ordmaster'];
		}
		if(!isset($params['res_ord_details']) || !is_array($params['res_ord_details'])){
			if($order_id > 0){
				$res_ord_details = $CI->db->get_where('wl_order_details',array('orders_id'=>$order_id))->result_array();
			}
		}else{
			$res_ord_details = $params['res_ord_details'];
		}
		$ordmaster_data = array();
		if(is_array($res_ordmaster) && !empty($res_ordmaster)){
			$total_payable_amount = $res_ordmaster['total_amount'];
			$total_payable_amount = formatNumber($total_payable_amount,2);	
			$ordmaster_data = array(
															'customers_id'=>$res_ordmaster['customers_id'],
															'order_id'=>$res_ordmaster['order_id'],
															'order_type'=>$res_ordmaster['order_type'],
															'txn_site_unique_code'=>$res_ordmaster['txn_site_unique_code'],
															'invoice_number'=>$res_ordmaster['invoice_number'],
															'first_name'=>$res_ordmaster['first_name'],
															'last_name'=>$res_ordmaster['last_name'],
															'email'=>$res_ordmaster['email'],
															'mobile_number'=>$res_ordmaster['mobile_number'],
															'total_payable_amount'=>$total_payable_amount,
															'currency_symbol'=>$res_ordmaster['currency_symbol'],
															'currency_code'=>$res_ordmaster['currency_code'],
															'payment_status'=>$res_ordmaster['payment_status'],
															'order_status'=>$res_ordmaster['order_status'],
															'payment_type'=>$res_ordmaster['payment_type'],
															'payment_method'=>$res_ordmaster['payment_method'],
															'total_amount'=>$res_ordmaster['total_amount'],
															'shipping_amount'=>$res_ordmaster['shipping_amount'],
															'gst_cent'=>$res_ordmaster['vat_applied_cent'],
															'gst_amount'=>$res_ordmaster['vat_amount'],
															'cod_amount'=>$res_ordmaster['cod_amount'],
															'coupon_discount_amount'=>$res_ordmaster['coupon_discount_amount'],
															'wallet_amount'=>$res_ordmaster['wallet_amount'],
															'wallet_credits_used'=>$res_ordmaster['wallet_credits_used'],
															'order_date'=>date("d M Y",strtotime($res_ordmaster['order_received_date'])),
															'billing_name'=>$res_ordmaster['billing_name'],
															'billing_address'=>$res_ordmaster['billing_address'],
															'billing_company_name'=>$res_ordmaster['billing_company_name'],
															'billing_country_name'=>$res_ordmaster['billing_country_name'],
															'billing_state_name'=>$res_ordmaster['billing_state_name'],
															'billing_city_name'=>$res_ordmaster['billing_city_name'],
															'billing_zipcode_name'=>$res_ordmaster['billing_zipcode_name'],
															'billing_gst_no'=>$res_ordmaster['billing_gst_no'],
															'admin_invoice_settings'=>unserialize($res_ordmaster['admin_invoice_settings'])
														);
		}
		$order_item_details=array();
		if(is_array($res_ord_details) && !empty($res_ord_details)){
			foreach($res_ord_details as $item_key=>$item_dtls){
				$loop_item_price = $item_dtls['price'];
				$loop_item_price = formatNumber($loop_item_price,2);
				$order_item_details[$item_key] = array(
																						'orders_dtl_id'=>$item_dtls['orders_dtl_id'],
																						'sub_order_id'=>$item_dtls['unique_order_id'],
																						'order_dtl_subtype'=>$item_dtls['order_dtl_subtype'],
																						'ref_id'=>$item_dtls['ref_id'],
																						'service_name'=>$item_dtls['service_name'],
																						'service_img'=>$item_dtls['service_img'],
																						'price'=>$loop_item_price,
																						'category_id'=>$item_dtls['category_id'],
																						'category_links'=>$item_dtls['category_links']
																					);
			}
		}
		$company_info_data = get_db_single_row('tbl_admin',"address,city,state,country,zipcode,phone,company_name,admin_email"," AND admin_id='1' ");
		$ret_arr = array('order_master_data'=>$ordmaster_data,'order_item_details_data'=>$order_item_details,'company_info_data'=>$company_info_data);
		$ret_arr['package_image_path'] = base_url()."uploaded_files/orders";
		$ret_arr['package_no_image_path']  = base_url()."uploaded_files/products/noimg.png";
		return $ret_arr;
	}
}

if( !function_exists('send_order_notification')){
	function send_order_notification($params){
			$CI = CI();
			$CI->load->library('Dmailer');
			//Mail
			$admin_info = $CI->admin_info;
			$order_id = (int) (isset($params['order_id']) ? $params['order_id'] : 0);
			$order_type = (isset($params['order_type']) ? $params['order_type'] : 0);
			$mail_subject = $CI->config->item('site_name')." Order overview";
			ob_start();
			switch($order_type){
				case 1:
				case 2:
						$mail_subject = $CI->config->item('site_name')." Donation overview";
						$params_mailcontent=array(
																	'order_id'=>$params['order_id'],
																	'order_type'=>$params['order_type']
																);
						$mailcontent=order_invoice_mail_content($params_mailcontent);
				break;
				
				default:
					die("Unable to get data");
			}
			$invoice_master_data = $params_mailcontent['invoice_master_data'];
			$invoice_item_details_data = $params_mailcontent['invoice_item_details_data'][0];
			$user_email = $invoice_master_data['email'];
			$user_id = $invoice_master_data['customers_id'];
			$invoice_mail_content= $mailcontent;
			ob_clean();
			//Send Mail to User
			$mail_conf =  array(
											'subject'    => $mail_subject,
											'to_email'   => $user_email,
											'from_email' => $admin_info->admin_email,
											'from_name'  => $CI->config->item('site_name'),
											'body_part'  => $invoice_mail_content
											);

			@$CI->dmailer->mail_notify($mail_conf);
			
			//Send Mail to Admin
			//$mail_subject = $CI->config->item('site_name')." Donation overview";
			$mail_conf =  array(
											'subject'    => $mail_subject,
											'to_email'   => $admin_info->admin_email,
											'from_email' => $admin_info->admin_email,
											'from_name'  => $CI->config->item('site_name'),
											'body_part'  => $invoice_mail_content
											);

			@$CI->dmailer->mail_notify($mail_conf);
		}
}

if(!function_exists('get_configuration_settings')){
	function get_configuration_settings($params=array()){
		$CI =& get_instance();
		$type = !empty($params['type']) ? $params['type'] : "";
		if($type!=''){
			if(empty($CI->configuration_settings_gx[$type])){
				$configuration_res = $CI->db->get_where('wl_configuration',array('type'=>$type))->row_array();
				$CI->configuration_settings_gx[$type]=!empty($configuration_res) ? unserialize($configuration_res['value']) : array();
			}
			return $CI->configuration_settings_gx[$type];
		}else{
			return '';
		}
	}
}

if(!function_exists('generate_invoice_serial_number')){
	function generate_invoice_serial_number(){
		$CI = CI();
		$prefix_order = 'C';
		$offset_number = 0;
		$year_str = date('y');
		$year_str.=$year_str+1;
		$res_last_order = $CI->db->select('invoice_number')->order_by('order_id','DESC')->get_where('wl_order')->row_array();
		if(!empty($res_last_order)){
			preg_match("~(\d){4}/$prefix_order(\d+)$~",$res_last_order['invoice_number'],$matches);	
			if($matches && $matches[1]==$year_str){
				$offset_number = $matches[2];
			}
		}
		$ret_serial_number = $year_str."/".$prefix_order.pad_number($offset_number+1,6,0);
		return $ret_serial_number;
	}
}