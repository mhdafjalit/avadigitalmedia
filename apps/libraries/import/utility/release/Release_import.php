<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/import/utility/import_activity.php');

trait  Release_import {

	private $customer_id;

	private $current_row_data;

	private $total_rec_inserted=0;

	private $total_rec_updated=0;

	private $is_new_record;

	private $max_ship_address_fields=2;

	public $last_processed_data_row=0;



	protected function get_heading_list(){

		$this->customers_custom_fields_res = get_default_configuration_value("customers_custom_fields",$this->company_id);

		$ret = array("Customer ID","Customer Name","Prospect","Inactive","Contact Name","Bill to Address-Line One","Bill to Address-Line Two","Bill to City","Bill to State","Bill to Zip","Bill to Country","Bill to Sales Tax ID");

		for($ix=1;$ix<=$this->max_ship_address_fields;$ix++){

			$loop_ship_add_fields = array("Ship to Address $ix-Line One","Ship to Address $ix-Line Two","Ship to City $ix","Ship to State $ix","Ship to Zipcode $ix","Ship to Country $ix","Ship to Sales Tax ID $ix");

			foreach($loop_ship_add_fields as $val){

				$ret[] = $val;

			}

		}	

		$ret = array_merge($ret,array("Customer Type","Telephone 1","Telephone 2","Fax Number","Customer E-mail","Website","Account #","Sales Representative ID","G/L Sales Account","Open Purchase Order Number","Ship Via","UPS Number","Resale Number","Pricing Level","Use Standard Terms","C.O.D. Terms","Prepaid Terms","Due Next Month","Due End Month","Due Days","Discount Days","Discount Percent","Credit Limit","Customer Since Date"));

		if(!empty($this->customers_custom_fields_res)){

			for($ix=1;$ix<=$this->max_custom_fields;$ix++){

				if($this->customers_custom_fields_res['custom_'.$ix.'_text']!='' && $this->customers_custom_fields_res['custom_'.$ix.'_status']==1){

					$ret[] = $this->customers_custom_fields_res['custom_'.$ix.'_text'];

				}

			}

		}

		return $ret;

	}



	private function add_general_details(){

		$account_created_date = $this->current_row_data['account_created_date'] ?? '';

		if($account_created_date=='' || $account_created_date=='1970-01-01' || $account_created_date=='1970-01-01 00:00:00'){

			$account_created_date = xc_get_cur_date_time('config.date.time');

			$account_updated_date = $account_created_date;

		}else{

			$account_updated_date = xc_get_cur_date_time('config.date.time');

		}

		$customer_code = $this->current_row_data['customer_code'];

		$sale_tax_name = $this->current_row_data['sale_tax_name'] ?? '';

		$sale_tax_id = $this->handle_sales_tax($sale_tax_name);

		$customer_type_name = $this->current_row_data['customer_type_name'] ?? '';

		$customer_type_id = $this->handle_customer_type_name($customer_type_name);

		$status = $this->current_row_data['status'] ?? 1;

		$terms_credit_type = $this->current_row_data['use_standard_terms'] ? 1 : 2;

		$posted_array = array(

														'company_id' => $this->company_id,

														'terms_credit_type'=>$terms_credit_type ?? 1,

														'customer_name' => $this->current_row_data['customer_name'] ?? '',

														'customer_code' => $customer_code,

														'status' => $this->current_row_data['status'] ?? 1,

														'credit_status'=>$credit_status ?? 1,

														'is_prospect' => $this->current_row_data['is_prospect'] ?? 0,

														'contact_name' => $this->current_row_data['contact_name'] ?? '',

														'account_number' => $this->current_row_data['account_number'] ?? '',

														'address' => $this->current_row_data['address'] ?? '',

														'address_2' => $this->current_row_data['address_2'] ?? '',

														'country' => $this->current_row_data['country'] ?? '',

														'state' => $this->current_row_data['state'] ?? '',

														'city' => $this->current_row_data['city'] ?? '',

														'zipcode' => $this->current_row_data['zipcode'] ?? '',

														'customer_type_id' => $customer_type_id,

														'customer_type_name' => $customer_type_name ?? '',

														'sale_tax_id' => $sale_tax_id,

														'sale_tax_name' => $sale_tax_name ?? '',

														'phone' => $this->current_row_data['phone'] ?? '',

														'phone2' => $this->current_row_data['phone2'] ?? '',

														'fax' => $this->current_row_data['fax'] ?? '',

														'email_id' => $this->current_row_data['email_id'] ?? '',

														'website' => $this->current_row_data['website'] ?? '',

														'custom_1' => $this->current_row_data['custom_1'] ?? '',

														'custom_2' => $this->current_row_data['custom_2'] ?? '',

														'custom_3' => $this->current_row_data['custom_3'] ?? '',

														'custom_4' => $this->current_row_data['custom_4'] ?? '',

														'custom_5' => $this->current_row_data['custom_5'] ?? '',

														'status'=>$status,

														'ip_address' => $this->ci->input->ip_address(),

														'account_created_date' => $account_created_date,

														'account_updated_date' => $account_updated_date

													);

		$posted_array = $this->ci->security->xss_clean($posted_array);

		if($this->debug_mode===TRUE){

			$this->customer_id = "TBA";

		}else{

			$this->customer_id = $this->ci->utils_model->safe_insert('wl_customers',$posted_array,FALSE);

			$this->ci->log_entry_db_import[] = array('type'=>'cust_gen_dtls_created','rec_id'=>$this->customer_id);

			$this->total_rec_inserted++;

		}

	}



	private function update_general_details(){

			if(isset($this->current_row_data['account_created_date'] )){

				$account_created_date = $this->current_row_data['account_created_date'] ?? '';

				if($account_created_date=='' || $account_created_date=='1970-01-01' || $account_created_date=='1970-01-01 00:00:00'){

					$account_created_date = xc_get_cur_date_time('config.date.time');

				}

			}

			$customer_code = $this->current_row_data['customer_code'];

			$sale_tax_name = $this->current_row_data['sale_tax_name'] ?? '';

			$sale_tax_id = $this->handle_sales_tax($sale_tax_name);

			$customer_type_name = $this->current_row_data['customer_type_name'] ?? '';

			$customer_type_id = $this->handle_customer_type_name($customer_type_name);

			$status = $this->current_row_data['status'] ?? 1;

			$terms_credit_type = $this->current_row_data['use_standard_terms'] ? 1 : 2;

			$posted_array = array(

														'terms_credit_type'=>$terms_credit_type ?? 1,

														'customer_name' => $this->current_row_data['customer_name'] ?? '',

														'customer_code' => $customer_code,

														'status' => $this->current_row_data['status'] ?? 1,

														'credit_status'=>$credit_status ?? 1,

														'is_prospect' => $this->current_row_data['is_prospect'] ?? 0,

														'contact_name' => $this->current_row_data['contact_name'] ?? '',

														'account_number' => $this->current_row_data['account_number'] ?? '',

														'address' => $this->current_row_data['address'] ?? '',

														'address_2' => $this->current_row_data['address_2'] ?? '',

														'country' => $this->current_row_data['country'] ?? '',

														'state' => $this->current_row_data['state'] ?? '',

														'city' => $this->current_row_data['city'] ?? '',

														'zipcode' => $this->current_row_data['zipcode'] ?? '',

														'customer_type_id' => $customer_type_id,

														'customer_type_name' => $customer_type_name ?? '',

														'sale_tax_id' => $sale_tax_id,

														'sale_tax_name' => $sale_tax_name ?? '',

														'phone' => $this->current_row_data['phone'] ?? '',

														'phone2' => $this->current_row_data['phone2'] ?? '',

														'fax' => $this->current_row_data['fax'] ?? '',

														'email_id' => $this->current_row_data['email_id'] ?? '',

														'website' => $this->current_row_data['website'] ?? '',

														'custom_1' => $this->current_row_data['custom_1'] ?? '',

														'custom_2' => $this->current_row_data['custom_2'] ?? '',

														'custom_3' => $this->current_row_data['custom_3'] ?? '',

														'custom_4' => $this->current_row_data['custom_4'] ?? '',

														'custom_5' => $this->current_row_data['custom_5'] ?? '',

														'status'=>$status,

														'ip_address' => $this->ci->input->ip_address(),

														'account_updated_date' => xc_get_cur_date_time('config.date.time')

													);

		if(isset($account_created_date)){

			$posted_array['account_created_date'] = $account_created_date;

		}

		$posted_array = $this->ci->security->xss_clean($posted_array);

		$posted_where = array('customer_id '=>$this->customer_id );

		if($this->debug_mode===TRUE){



		}else{

			$this->ci->utils_model->safe_update('wl_customers',$posted_array,$posted_where,FALSE);

			$this->ci->log_entry_db_import[] = array('type'=>'cust_gen_dtls_updated','rec_id'=>$this->customer_id);

			$this->total_rec_updated++;

		}

	}



	private function update_ship_addresses(){

		$this->ci->load->helper('customers/customer');

		$addresses = $this->current_row_data['ship_addresses'] ?? array();

		if($this->customer_id>0 && !$this->is_new_record){

			$db_customer_addresses = $this->ci->db->select('address_id')->order_by('addr_level_key')->get_where('  wl_customers_address_book',array('customer_id'=>$this->customer_id,'company_id'=>$this->company_id))->result_array();

		}else{

			$db_customer_addresses = array();

		}

		foreach($addresses as $ikey=>$ival){

			$sale_tax_name = $ival['sale_tax_name'] ?? '';

			$sale_tax_id = $this->handle_sales_tax($sale_tax_name);

			if(isset($db_customer_addresses[$ikey])){

				$address_data = array(

													'contact_name' => $ival['contact_name'] ?? '',

													'address' => $ival['address'],

													'address_2' => $ival['address_2'],

													'country' => $ival['country'],

													'state' => $ival['state'],

													'city' => $ival['city'],

													'zipcode' => $ival['zipcode'],

													'phone' => $ival['phone'] ?? '',

													'sale_tax_id' =>$sale_tax_id,

													'sale_tax_name' => $sale_tax_name,

													'receive_date' => xc_get_cur_date_time('config.date.time')

													);



				$address_data = $this->ci->security->xss_clean($address_data);

				$is_empty = is_customer_address_empty($address_data);

				$address_data['is_empty'] = $is_empty;

				$where_address_data = "address_id = '".$db_customer_addresses[$ikey]['address_id']."'";

				$this->ci->utils_model->safe_update('wl_customers_address_book',$address_data,$where_address_data,FALSE);

				$this->ci->log_entry_db_import[] = array('type'=>'cust_ship_addr_updated','rec_id'=>$db_customer_addresses[$ikey]['address_id']);

			}else{

				$address_data = array(

													'addr_level_key'=>$ikey,

													'company_id' => $this->company_id,

													'customer_id' => $this->customer_id,

													'contact_name' => $ival['contact_name'] ?? '',

													'address' => $ival['address'],

													'address_2' => $ival['address_2'],

													'country' => $ival['country'],

													'state' => $ival['state'],

													'city' => $ival['city'],

													'zipcode' => $ival['zipcode'],

													'phone' => $ival['phone'] ?? 0,

													'sale_tax_id' =>$sale_tax_id,

													'sale_tax_name' => $sale_tax_name,

													'receive_date' => xc_get_cur_date_time('config.date.time')

													);



					$address_data = $this->ci->security->xss_clean($address_data);

					$is_empty = is_customer_address_empty($address_data);

					$address_data['is_empty'] = $is_empty;

					$insert_id = $this->ci->utils_model->safe_insert('wl_customers_address_book',$address_data,FALSE);

					$this->ci->log_entry_db_import[] = array('type'=>'cust_ship_addr_created','rec_id'=>$insert_id);

			}

		}

	}



	private function update_sales_info_settings(){

		$posted_sales_info_settings = $this->current_row_data['cs_info_setting'];

		$ship_via = $posted_sales_info_settings['ship_via'] ?? '';

		/*Handle Ship via option*/

		if($ship_via!=''){

			$where_ship_methods = "company_id='".$this->company_id."' AND item_ship_method_text='".$this->ci->db->escape_str($ship_via)."' AND item_ship_method_status!='2'";

			$res_ship_method = $this->ci->db->select('item_ship_method_id')->get_where('wl_item_ship_methods',$where_ship_methods)->row_array();

			if(empty($res_ship_method)){

				$db_post_ship_data = array(

																'company_id' => $this->company_id,

																'item_ship_method_text'=>$ship_via,

																'item_ship_method_status'=>'1',

																'item_ship_method_added'=>xc_get_cur_date_time('config.date.time')

																);

				$db_post_ship_data = $this->ci->security->xss_clean($db_post_ship_data);

				$inserted_id = $this->ci->utils_model->safe_insert('wl_item_ship_methods',$db_post_ship_data,FALSE);

				$posted_sales_info_settings['ship_via'] = $inserted_id;

				$posted_sales_info_settings['ship_via_text'] = $ship_via;

				$this->ci->log_entry_db_import[] = array('type'=>'ship_method_created','rec_id'=>$inserted_id);

			}else{

				$posted_sales_info_settings['ship_via'] = $res_ship_method['item_ship_method_id'];

				$posted_sales_info_settings['ship_via_text'] = $ship_via;

			}

		}else{

			$posted_sales_info_settings['ship_via'] = 0;

			$posted_sales_info_settings['ship_via_text'] = '';

		}

		/*Ship via handling ends*/

		/*Handle Sales representative option*/

		$sales_representative_name = $posted_sales_info_settings['sales_representative_name'] ?? '';

		if($sales_representative_name!=''){

			$sales_rep_where = "company_id='".$this->company_id."'AND employee_code='".$this->ci->db->escape_str($sales_representative_name)."' AND  status != '2'";

			$sales_rep_res = $this->ci->db->select('employee_id,employee_type')->get_where('wl_employees',$sales_rep_where)->row_array();

			if(empty($sales_rep_res)){

				$db_post_sales_rep_data = array(

							'company_id' => $this->company_id,

							'first_name' => $sales_representative_name,

							'employee_code'=>$sales_representative_name,

							'employee_type'=>'1',

							'employee_type_name'=>'Sales Rep',

							'status'=>'1',

							'account_created_date'=>xc_get_cur_date_time('config.date.time')

							);

				$db_post_sales_rep_data = $this->ci->security->xss_clean($db_post_sales_rep_data);

				$inserted_id = $this->ci->utils_model->safe_insert('wl_employees',$db_post_sales_rep_data,FALSE);

				$posted_sales_info_settings['sales_rep_id'] = $inserted_id;

				$this->ci->log_entry_db_import[] = array('type'=>'cust_sales_rep_created','rec_id'=>$inserted_id);

			}else{

				if($sales_rep_res['employee_type']==0){

					$db_post_sales_rep_data = array(

							'employee_type'=>'2',

							'employee_type_name'=>'Both'

							);

					$db_post_sales_rep_data = $this->ci->security->xss_clean($db_post_sales_rep_data);

					$where_update_srp_data = "employee_id='".$sales_rep_res['employee_id']."'";

					$this->ci->utils_model->safe_update('wl_employees',$db_post_sales_rep_data,$where_update_srp_data,FALSE);

					$this->ci->log_entry_db_import[] = array('type'=>'cust_sales_rep_updated','rec_id'=>$sales_rep_res['employee_id']);

				}

				$posted_sales_info_settings['sales_rep_id'] = $sales_rep_res['employee_id'];

			}

		}else{

			$posted_sales_info_settings['sales_rep_id'] = 0;

		}

		unset($posted_sales_info_settings['sales_representative_name']);

		/*Handle Sales representative option ends*/

		/*Handle Account Number*/

		$sales_account_number = $posted_sales_info_settings['sales_account_number'] ?? '';

		if($sales_account_number!=''){

			$coa = $this->handle_coa($sales_account_number);

			$posted_sales_info_settings['sales_account_id'] = $coa;

		}else{

			$posted_sales_info_settings['sales_account_id'] = 0;

		}

		/*Account Number handling ends*/

		if($this->customer_id>0 && !$this->is_new_record){

			$configuration_res = $this->ci->db->select('sl')->get_where('wl_configuration',array('type'=>'customer_sales_info_setting','ref_company_id'=>$this->company_id,'ref_user_id'=>$this->customer_id))->row_array();

		}

		if(empty($configuration_res)){

			$insert_data = array(

															'type'=>'customer_sales_info_setting',

															'ref_company_id'=>$this->company_id,

															'ref_user_id'=>$this->customer_id,

															'value' => json_encode($posted_sales_info_settings,TRUE),

															'up_date' => xc_get_cur_date_time('config.date.time')

															);

			$insert_data = $this->ci->security->xss_clean($insert_data);

			$insert_id = $this->ci->utils_model->safe_insert('wl_configuration',$insert_data,FALSE);

			$this->ci->log_entry_db_import[] = array('type'=>'cust_sales_info_created','rec_id'=>$insert_id);

		}else{

			$update_data = array(

															'value' => json_encode($posted_sales_info_settings,TRUE),

															'up_date' => xc_get_cur_date_time('config.date.time')

															);

			$update_data = $this->ci->security->xss_clean($update_data);

			$where_update_data = "sl='".$configuration_res['sl']."'";

			$this->ci->utils_model->safe_update('wl_configuration',$update_data,$where_update_data,FALSE);

			$this->ci->log_entry_db_import[] = array('type'=>'cust_sales_info_updated','rec_id'=>$configuration_res['sl']);

		}

	}



	private function update_credit_info_settings(){

		if($this->customer_id>0 && !$this->is_new_record){

			$configuration_res = $this->ci->db->select('sl')->get_where('wl_configuration',array('type'=>'customer_defaults_payment_terms','ref_company_id'=>$this->company_id,'ref_user_id'=>$this->customer_id))->row_array();

			if($this->current_row_data['use_standard_terms']){

				if(!empty($configuration_res)){

					$this->ci->utils_model->safe_delete('wl_configuration',array('sl'=>$configuration_res['sl']),FALSE);

				}

			}

		}

		if($this->current_row_data['use_standard_terms']){

			return;

		}

		$payment_term = 0;

		if($this->current_row_data['cod_terms']){

			$payment_term = 1;

		}elseif($this->current_row_data['prepaid_terms']){

			$payment_term = 2;

		}elseif($this->current_row_data['due_num_days']){

			$payment_term = 3;

		}elseif($this->current_row_data['due_next_month']){

			$payment_term = 4;

		}elseif($this->current_row_data['due_end_month']){

			$payment_term = 5;

		}

		$net_due_in =0;

		$discount_in = 0;

		$discount_percentage = 0;

		$use_discount=0;

		if($this->current_row_data['cod_terms'] || $this->current_row_data['prepaid_terms']){

			$discount_in = 0;

			$discount_percentage = 0;

			$use_discount=0;

		}elseif($this->current_row_data['due_num_days'] || $this->current_row_data['due_next_month'] || $this->current_row_data['due_end_month']){

			$discount_in = $this->current_row_data['discount_days'];

			$discount_percentage = $this->current_row_data['discount_percent'];

			$use_discount=$discount_percentage>0 ? 1 : 0;

			$net_due_in =  (int) ($this->current_row_data['due_next_month'] ? 0 : $this->current_row_data['due_days']);

		}

		$credit_limit = $this->current_row_data['credit_limit'];

		$customer_terms = array(

															'payment_term'=>$payment_term,

															'use_discount'=>$use_discount,

															'discount_in'=>$discount_in,

															'discount_percentage'=>$discount_percentage,

															'credit_limit'=>$credit_limit,

															'net_due_in'=>$net_due_in

														);

					

		$terms_title = get_terms_credit_text($customer_terms);

		$customer_terms['terms_title'] = $terms_title;

		$terms_credit_data = array(

												'value' => json_encode($customer_terms,TRUE)

												);

		if(empty($configuration_res)){

			$terms_credit_data = array_merge(array(

																							'type'=>'customer_defaults_payment_terms',

																							'ref_company_id'=>$this->company_id,

																							'ref_user_id'=>$this->customer_id,

																							'up_date'=>xc_get_cur_date_time('config.date.time')

																							),

															$terms_credit_data);

			$terms_credit_data = $this->ci->security->xss_clean($terms_credit_data);

			$insert_id = $this->ci->utils_model->safe_insert('wl_configuration',$terms_credit_data,FALSE);

			$this->ci->log_entry_db_import[] = array('type'=>'cust_payment_terms_created','rec_id'=>$insert_id);

		}else{

			$terms_credit_data = array_merge(array(

																							'up_date'=>xc_get_cur_date_time('config.date.time')

																							),

															$terms_credit_data);

			$where_update_data = "sl='".$configuration_res['sl']."'";

			$this->ci->utils_model->safe_update('wl_configuration',$terms_credit_data,$where_update_data,FALSE);

			$this->ci->log_entry_db_import[] = array('type'=>'cust_payment_terms_updated','rec_id'=>$configuration_res['sl']);

		}

	}



	private function handle_coa($coa_text){

		$coa_text = mb_trim($coa_text);

		$coa_text_key = mb_trim(mb_strtolower($coa_text));

		if($coa_text_key!=''){

			if(!isset($this->coa_track[$coa_text_key])){

				$coa_exists = $this->ci->db->select('chart_of_account_id')->get_where('wl_chart_of_accounts',array('ref_company_id'=>$this->company_id,'chart_of_account_name'=>$coa_text,'status!='=>'2'))->row_array();

				$this->coa_track[$coa_text_key] = empty($coa_exists) ? array() : $coa_exists;

			}

			$ct_id = $this->coa_track[$coa_text_key]['chart_of_account_id'] ?? 0;

		}else{

			$ct_id = 0;

		}

		return $ct_id;

	}



	private function handle_coa_old($coa_text){

		$where_coa = "ref_company_id='".$this->company_id."' AND chart_of_account_name='".$this->ci->db->escape_str($coa_text)."' AND status!='2'";

		$res_coa = $this->ci->db->select('chart_of_account_id')->get_where('wl_chart_of_accounts',$where_coa)->row_array();

		if(!empty($res_coa)){

			$coa_id = $res_coa['chart_of_account_id'];

		}else{

			$coa_id = 0;

		}

		return $coa_id;

	}



	private function handle_sales_tax($sales_tax_text){

		/*Currently no tax is handled for the software*/

		return 0;

	}



	private function handle_customer_type_name($customer_type_name){

		$cmp_customer_type_name = mb_trim(mb_strtolower($customer_type_name));

		if($cmp_customer_type_name!='other'){

			$where_ct = "company_id='".$this->company_id."' AND customer_type_name='".$this->ci->db->escape_str($customer_type_name)."' AND status!='2'";

			$res_ct= $this->ci->db->select('customer_type_id')->get_where('wl_customer_types',$where_ct)->row_array();

			if(!empty($res_ct)){

				$ct_id = $res_ct['customer_type_id'];

			}else{

				$db_post_ct_data = array(

							'company_id'=>$this->company_id,

							'customer_type_name'=>$customer_type_name,

							'status'=>'1',

							'customer_type_date_added'=>xc_get_cur_date_time('config.date.time')

							);

				$db_post_ct_data = $this->ci->security->xss_clean($db_post_ct_data);

				$ct_id = $this->ci->utils_model->safe_insert('wl_customer_types',$db_post_ct_data,FALSE);

				$this->ci->log_entry_db_import[] = array('type'=>'customer_type_created','rec_id'=>$ct_id);

			}

		}else{

			$ct_id = 0;

		}

		return $ct_id;

	}



	public function upload_data(){

		$total_rec_inserted=0;

		if(!empty($this->data_results) && !empty($this->company_id)){

			foreach($this->data_results as $key=>$val){

				$this->ci->db->trans_start();

				$this->current_row_data = &$val['data'];

				$this->ci->log_entry_db_import = array();

				$customer_res_exists = $this->ci->db->select('customer_id')->get_where('wl_customers',array('company_id'=>$this->company_id,'customer_code'=>$this->current_row_data['customer_code'],'status!='=>'2'))->row_array();

				if(!empty($customer_res_exists)){

					$this->is_new_record=0;

					$this->customer_id = $customer_res_exists['customer_id'];

					$this->update_general_details();

				}else{

					$this->is_new_record=1;

					$this->add_general_details();

				}

				$this->update_ship_addresses();

				$this->update_sales_info_settings();

				$this->update_credit_info_settings();

				$loop_data_obj['inserted_rec_id']=$this->customer_id;

				log_import_activity($this->company_id);

				$this->ci->db->trans_complete();

				if ($this->ci->db->trans_status() === FALSE){

					if($this->is_new_record){

						if($this->total_rec_inserted>0){

							$this->total_rec_inserted--;

						}

					}else{

						if($this->total_rec_updated>0){

							$this->total_rec_updated--;

						}

					}

				}else{

					$this->last_processed_data_row = $val['row_index']-$this->data_initial_row_start+1;

					if(!empty($this->import_id)){

						$import_stats=array('processed_entries'=>$this->last_processed_data_row,'total_entries'=>$this->total_entries);

						update_import_processed($this->import_id,$import_stats);

					}

				}

			}

		}

		$ret = array('total_rec_inserted'=>$this->total_rec_inserted,'total_rec_updated'=>$this->total_rec_updated);

		return $ret;

	}



	protected function readDataValue($column_heading_value,$row_column_data_value,&$data_obj,&$error_obj){

		$column_matched=0;

		$has_data_error = FALSE;

		switch($column_heading_value){

			case 'customer id':

				$column_matched=1;

				$data_obj['customer_code'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						$error_obj[] = "Customer ID is missing";

						$this->has_data_error = $has_data_error = TRUE;

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,20)){

							$error_obj[] = "Customer ID should not be greater than 20 characters";

							$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'customer name':

				$column_matched=1;

				$data_obj['customer_name'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Customer Name is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,40)){

							$error_obj[] = "Customer Name should not be greater than 40 characters";

							$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'prospect':

				$column_matched=1;

				$row_column_data_value = convert_to_boolean($row_column_data_value);

				$data_obj['is_prospect'] = ($row_column_data_value) ? 1 : 0;

				if($this->apply_validation){

					if($row_column_data_value!='' && !preg_match("~^(true|false|0|1)$~i",$row_column_data_value)){

						$error_obj[] = "Prospect value is not valid";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'inactive':

				$column_matched=1;

				$row_column_data_value = convert_to_boolean($row_column_data_value);

				$data_obj['status'] = (!$row_column_data_value) ? 1 : 0;

				if($this->apply_validation){

					if($row_column_data_value!='' && !preg_match("~^(true|false|0|1)$~i",$row_column_data_value)){

						$error_obj[] = "Inactive value is not valid";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'contact name':

				$column_matched=1;

				$data_obj['contact_name'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Contact Name is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,40)){

							$error_obj[] = "Contact Name should not be greater than 40 characters";

							$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'account #':

				$column_matched=1;

				$data_obj['account_number'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Account Number is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif($row_column_data_value!='' && !$this->ci->form_validation->alpha_numeric($row_column_data_value)){

						$error_obj[] = "Account Number should be alphanumeric";

						$this->has_data_error = $has_data_error = TRUE;

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,40)){

						$error_obj[] = "Account Number should not be greater than 40 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'bill to address-line one':

				$column_matched=1;

				$data_obj['address'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Address Line1 is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,120)){

						$error_obj[] = "Address Line1 should not be greater than 120 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'bill to address-line two':

				$column_matched=1;

				$data_obj['address_2'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Address Line1 is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,80)){

						$error_obj[] = "Address Line2 should not be greater than 80 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'bill to country':

				$column_matched=1;

				$data_obj['country'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Bill to Country is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,80)){

						$error_obj[] = "Bill to Country should not be greater than 80 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'bill to state':

				$column_matched=1;

				$data_obj['state'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Bill to State is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,50)){

						$error_obj[] = "Bill to State should not be greater than 50 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'bill to city':

				$column_matched=1;

				$data_obj['city'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Bill to City is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,50)){

						$error_obj[] = "Bill to City should not be greater than 50 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'bill to zip':

				$column_matched=1;

				$data_obj['zipcode'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Bill to Zip is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,25)){

						$error_obj[] = "Bill to Zip should not be greater than 25 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'bill to sales tax id':

				$column_matched=1;

				$data_obj['sale_tax_name'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Bill to Zip is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,40)){

						$error_obj[] = "Bill to Sale tax should not be greater than 40 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'customer type':

				$column_matched=1;

				$data_obj['customer_type_name'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Customer Type is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,20)){

						$error_obj[] = "Customer Type should not be greater than 20 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'customer e-mail':

				$column_matched=1;

				$data_obj['email_id'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Customer E-mail is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->valid_email($row_column_data_value)){

						$error_obj[] = "Customer E-mail is valid";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'telephone 1':

				$column_matched=1;

				$data_obj['phone'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Telephone 1 is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,20)){

						$error_obj[] = "Telephone 1 should not be greater than 20 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'telephone 2':

				$column_matched=1;

				$data_obj['phone2'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Telephone 2 is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,20)){

						$error_obj[] = "Telephone 2 should not be greater than 20 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'fax number':

				$column_matched=1;

				$data_obj['fax'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Fax Number is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,20)){

						$error_obj[] = "Fax Number should not be greater than 20 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'website':

				$column_matched=1;

				$data_obj['website'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Website is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,80)){

						$error_obj[] = "Website should not be greater than 80 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'sales representative id':

				$column_matched=1;

				$this->loop_data_cs_info_setting['sales_representative_name'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Sales Representative ID is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,40)){

						$error_obj[] = "Sales Representative ID should not be greater than 40 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'g/l sales account':

				$column_matched=1;

				$this->loop_data_cs_info_setting['sales_account_number'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "G/L Sales Account is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,40)){

							$error_obj[] = "G/L Sales Account should not be greater than 40 characters";

							$this->has_data_error = $has_data_error = TRUE;

					}else{

						$acc_id = $this->handle_coa($row_column_data_value);

						if(empty($acc_id)){

							$error_obj[] = "G/L Sales Account - $row_column_data_value not exists";

							$this->has_data_error = $has_data_error = TRUE;

						}

					}

				}

			break;

			case 'open purchase order number':

				$column_matched=1;

				$this->loop_data_cs_info_setting['open_po_no'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Open Purchase Order Number is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,30)){

						$error_obj[] = "Open Purchase Order Number should not be greater than 30 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'ship via':

				$column_matched=1;

				$this->loop_data_cs_info_setting['ship_via'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Ship Via is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,30)){

						$error_obj[] = "Ship Via should not be greater than 30 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'ups number':

				$column_matched=1;

				$this->loop_data_cs_info_setting['ups_no'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "UPS Number is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,40)){

							$error_obj[] = "UPS Number should not be greater than 40 characters";

							$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'resale number':

				$column_matched=1;

				$this->loop_data_cs_info_setting['resale_no'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Resale Number is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,40)){

						$error_obj[] = "Resale Number should not be greater than 40 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'pricing level':

				$column_matched=1;

				$this->loop_data_cs_info_setting['pricing_level'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Pricing Level is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif($row_column_data_value!='' && ($row_column_data_value<0 || $row_column_data_value>9)){

							$error_obj[] = "Pricing Level must be between 0-9";

							$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'customer since date':

				$column_matched=1;

				if(is_numeric($row_column_data_value)){

					 $since_date_obj =  PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row_column_data_value);

					 $row_column_data_value = $since_date_obj->format('Y-m-d H:i:s');

				}

				$data_obj['account_created_date'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Balance Amount is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}

				}

			break;

			case 'use standard terms':

				$column_matched=1;

				$row_column_data_value = convert_to_boolean($row_column_data_value);

				$data_obj['use_standard_terms'] = ($row_column_data_value) ? 1 : 0;

				if($this->apply_validation){

					if($row_column_data_value!='' && !preg_match("~^(true|false|0|1)$~i",$row_column_data_value)){

						$error_obj[] = "Use Standard Terms value is not valid";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'c.o.d. terms':

				$column_matched=1;

				$row_column_data_value = convert_to_boolean($row_column_data_value);

				$data_obj['cod_terms'] = ($row_column_data_value) ? 1 : 0;

				if($this->apply_validation){

					if($row_column_data_value!='' && !preg_match("~^(true|false|0|1)$~i",$row_column_data_value)){

						$error_obj[] = "C.O.D. Terms value is not valid";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'prepaid terms':

				$column_matched=1;

				$row_column_data_value = convert_to_boolean($row_column_data_value);

				$data_obj['prepaid_terms'] = ($row_column_data_value) ? 1 : 0;

				if($this->apply_validation){

					if($row_column_data_value!='' && !preg_match("~^(true|false|0|1)$~i",$row_column_data_value)){

						$error_obj[] = "Prepaid Terms value is not valid";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'due next month':

				$column_matched=1;

				$row_column_data_value = convert_to_boolean($row_column_data_value);

				$data_obj['due_next_month'] = ($row_column_data_value) ? 1 : 0;

				if($this->apply_validation){

					if($row_column_data_value!='' && !preg_match("~^(true|false|0|1)$~i",$row_column_data_value)){

						$error_obj[] = "Due Next Month value is not valid";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'due end month':

				$column_matched=1;

				$row_column_data_value = convert_to_boolean($row_column_data_value);

				$data_obj['due_end_month'] = ($row_column_data_value) ? 1 : 0;

				if($this->apply_validation){

					if($row_column_data_value!='' && !preg_match("~^(true|false|0|1)$~i",$row_column_data_value)){

						$error_obj[] = "Due End Month value is not valid";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			case 'due days':

				$data_obj['due_days'] = $row_column_data_value;

			break;

			case 'discount days':

				$data_obj['discount_days'] = $row_column_data_value;

				if($row_column_data_value==''){

					/*$error_obj[] = "Due days is missing";

					$this->has_data_error = $has_data_error = TRUE;*/

				}elseif($row_column_data_value!='' && !preg_match("~^(\d+)$~",$row_column_data_value)){

						$error_obj[] = "Discount days should be numeric value greater than 0";

						$this->has_data_error = $has_data_error = TRUE;

				}elseif($row_column_data_value!='' && $row_column_data_value>1000){

						$error_obj[] = "Discount days should not be greater than 1000";

						$this->has_data_error = $has_data_error = TRUE;

				}

			break;

			case 'discount percent':

				$data_obj['discount_percent'] = $row_column_data_value;

				if($row_column_data_value==''){

					/*$error_obj[] = "Discount Percent is missing";

					$this->has_data_error = $has_data_error = TRUE;*/

				}elseif($row_column_data_value!='' && !$this->ci->form_validation->is_valid_amount($row_column_data_value)){

						$error_obj[] = "Discount percent is not valid";

						$this->has_data_error = $has_data_error = TRUE;

				}elseif($row_column_data_value!='' && $row_column_data_value>100){

						$error_obj[] = "Discount percent should not be greater than 100";

						$this->has_data_error = $has_data_error = TRUE;

				}elseif($row_column_data_value!='' && $row_column_data_value<0){

						$error_obj[] = "Discount percent should not be less than 0";

						$this->has_data_error = $has_data_error = TRUE;

				}

			break;

			case 'credit limit':

				$row_column_data_value = preg_replace("~\,~","",$row_column_data_value);

				$data_obj['credit_limit'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						$error_obj[] = "Credit Limit is missing";

						$this->has_data_error = $has_data_error = TRUE;

					}elseif(!$this->ci->form_validation->is_valid_amount($row_column_data_value)){

							$error_obj[] = "Credit Limit is not valid";

							$this->has_data_error = $has_data_error = TRUE;

					}

				}

			break;

			default:

				if(preg_match('/^Ship to Address (\d+)-Line One/i',$column_heading_value, $addr_match)){

					$column_matched=1;

					$loop_shipping_address_index = $addr_match[1];

					$this->loop_shipping_address_data[$loop_shipping_address_index-1]['address'] = $row_column_data_value;

					if($this->apply_validation){

						if($row_column_data_value==''){

							/*$error_obj[] = "Ship to Address $loop_shipping_address_index Line One is missing";

							$this->has_data_error = $has_data_error = TRUE;*/

						}elseif(!$this->ci->form_validation->max_length($row_column_data_value,120)){

							$error_obj[] = "Ship to Address $loop_shipping_address_index Line One should not be greater than 120 characters";

							$this->has_data_error = $has_data_error = TRUE;

						}

					}

			}elseif(preg_match('/^Ship to Address (\d+)-Line Two/i',$column_heading_value, $addr_match)){

				$column_matched=1;

				$loop_shipping_address_index = $addr_match[1];

				$this->loop_shipping_address_data[$loop_shipping_address_index-1]['address_2'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Ship to Address $loop_shipping_address_index Line Two is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,80)){

						$error_obj[] = "Ship to Address $loop_shipping_address_index Line Two should not be greater than 80 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			}elseif(preg_match('/^Ship to City (\d+)/i',$column_heading_value, $addr_match)){

				$column_matched=1;

				$loop_shipping_address_index = $addr_match[1];

				$this->loop_shipping_address_data[$loop_shipping_address_index-1]['city'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Ship to City $loop_shipping_address_index is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,50)){

						$error_obj[] = "Ship to City $loop_shipping_address_index should not be greater than 50 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			}elseif(preg_match('/^Ship to State (\d+)/i',$column_heading_value, $addr_match)){

				$column_matched=1;

				$loop_shipping_address_index = $addr_match[1];

				$this->loop_shipping_address_data[$loop_shipping_address_index-1]['state'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Ship to State $loop_shipping_address_index is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,50)){

						$error_obj[] = "Ship to State $loop_shipping_address_index should not be greater than 50 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			}elseif(preg_match('/^Ship to Zipcode (\d+)/i',$column_heading_value, $addr_match)){

				$column_matched=1;

				$loop_shipping_address_index = $addr_match[1];

				$this->loop_shipping_address_data[$loop_shipping_address_index-1]['zipcode'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Ship to Zip $loop_shipping_address_index is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,25)){

						$error_obj[] = "Ship to Zipcode $loop_shipping_address_index should not be greater than 25 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			}elseif(preg_match('/^Ship to Country (\d+)/i',$column_heading_value, $addr_match)){

				$column_matched=1;

				$loop_shipping_address_index = $addr_match[1];

				$this->loop_shipping_address_data[$loop_shipping_address_index-1]['country'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Ship to Country $loop_shipping_address_index is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,80)){

						$error_obj[] = "Ship to Country $loop_shipping_address_index should not be greater than 80 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			}elseif(preg_match('/^Ship to Sales Tax ID (\d+)/i',$column_heading_value, $addr_match)){

				$column_matched=1;

				$loop_shipping_address_index = $addr_match[1];

				$this->loop_shipping_address_data[$loop_shipping_address_index-1]['sale_tax_name'] = $row_column_data_value;

				if($this->apply_validation){

					if($row_column_data_value==''){

						/*$error_obj[] = "Ship to Sales Tax ID $loop_shipping_address_index is missing";

						$this->has_data_error = $has_data_error = TRUE;*/

					}elseif(!$this->ci->form_validation->max_length($row_column_data_value,40)){

						$error_obj[] = "Ship to Sales Tax ID $loop_shipping_address_index should not be greater than 40 characters";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			}

		}

		/*Custom Fields*/

		if(!$column_matched && !empty($this->customers_custom_fields_res)){

			for($ax=1;$ax<=$this->max_custom_fields;$ax++){

				$loop_custom_heading = $this->customers_custom_fields_res['custom_'.$ax.'_text'] ?? '';

				$loop_custom_heading = mb_trim($loop_custom_heading);

				$loop_custom_heading = mb_strtolower($loop_custom_heading);

				if($loop_custom_heading==$column_heading_value){

					$data_obj['custom_'.$ax] = $row_column_data_value;

					if($this->apply_validation){

						if(!$this->ci->form_validation->max_length($row_column_data_value,40)){

								$error_obj[] = "Resale Number should not be greater than 40 characters";

								$this->has_data_error = $has_data_error = TRUE;

						}

					}

				}

			}

		}

		/*Custom Fields Ends*/

	}



	protected function rowIterationAfterCallback(&$data_obj,&$error_obj){

		$has_data_error = FALSE;

		$checked_term_types = 0;

		$term_types = array('use_standard_terms','cod_terms','prepaid_terms','due_next_month','due_end_month');

		foreach($term_types as $val){

			if($data_obj[$val]){

				$checked_term_types++;

			}

		}

		$data_obj['due_num_days'] = !$checked_term_types ? 1 : 0;

		if($this->apply_validation){

			if($data_obj['country']=='' && ($data_obj['state']!='' || $data_obj['zipcode']!='' || $data_obj['city']!='')){

				$error_obj[] = "Bill to Country is missing";

				$this->has_data_error = $has_data_error = TRUE;

			}

		}

		if($this->apply_validation){

			if(!empty($this->loop_shipping_address_data)){

				foreach($this->loop_shipping_address_data as $shp_addr_key=>$shp_addr_val){

					if($shp_addr_val['country']=='' && ($shp_addr_val['state']!='' || $shp_addr_val['zipcode']!='' || $shp_addr_val['city']!='')){

						$error_obj[] = "Ship to Country".($shp_addr_key+1)." is missing";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}

			}

			/*Validate payment terms*/

			if($checked_term_types>1){

				$error_obj[] = "You cannot select multiple term types";

				$this->has_data_error = $has_data_error = TRUE;

			}else{

				if($data_obj['due_next_month']){

					if($data_obj['due_days']==''){

						$error_obj[] = "Due days is missing";

						$this->has_data_error = $has_data_error = TRUE;

					}elseif($data_obj['due_days']!='' && !preg_match("~^(0?[1-9]|[12][0-9]|3[01])$~",$data_obj['due_days'])){

						$error_obj[] = "Due days is not valid. Value must be from 1-31";

						$this->has_data_error = $has_data_error = TRUE;

					}

				}elseif($data_obj['due_num_days']){

					if($data_obj['due_days']==''){

						$error_obj[] = "Due days is missing";

						$this->has_data_error = $has_data_error = TRUE;

					}elseif($data_obj['due_days']!='' && !preg_match("~^(\d+)$~",$data_obj['due_days'])){

						$error_obj[] = "Due days should be numeric value greater or equal to 0";

						$this->has_data_error = $has_data_error = TRUE;

					}elseif($data_obj['due_days']!='' && $data_obj['due_days']>1000){

							$error_obj[] = "Discount days should not be greater than 1000";

							$this->has_data_error = $has_data_error = TRUE;

					}

				}

			}

		  /*Validate payment terms ends*/

		}

	}

	

}

/*End of file */