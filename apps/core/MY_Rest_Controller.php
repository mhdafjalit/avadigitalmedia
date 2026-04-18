<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* The MX_Controller class is autoloaded as required */
class MY_Rest_Controller extends REST_Controller
{

	public function __construct(){
		parent::__construct();
		/*$this->session->set_userdata('lng_theme','arabic');
		$this->config->set_item('language','arabic');
		$language = $this->config->item('language');
		$this->lang->load($language,$language);*/
		$this->load->helper(array('apis/api','wallets'));
		$this->load->library('Custom_notification');
		//trace($this->api_token_res);
		$this->logged_in_user_id=0;
		if(isset($this->api_token_res) && is_object($this->api_token_res)){
			$this->db->select('*,customers_id as user_id');
			$this->db->from('wl_customers');
			$this->db->where('customers_id',$this->api_token_res->user_id);
			$this->db->where('member_type','3');
			$this->db->where('status','1');
			$query = $this->db->get();
			if($query->num_rows()>0){
				$this->user = $query->row();
				$this->token=$this->request_token;
				$this->logged_in_user_id=$this->user->user_id;
			}else{
				$this->token='';
				$data = array('success'=>false,'error'=>'Invalid Username','err_code'=>'AUTH_MISMATCH400');
				$this->response($data, self::HTTP_UNAUTHORIZED);
				exit;
			}
		}
 	}

	public function index_get(){
		$status_code=self::HTTP_BAD_REQUEST;
		$msg = $this->http_status_codes[$status_code];
		$data = array('success'=>false,'error'=>$msg,'err_code'=>'DEFERR');
		$this->response($data, $status_code);
	}

	public function index_post(){
		$status_code=self::HTTP_BAD_REQUEST;
		$msg = $this->http_status_codes[$status_code];
		$data = array('success'=>false,'error'=>$msg,'err_code'=>'DEFERR');
		$this->response($data, $status_code);
	}


	public function check_password(){
		$password = $this->input->post('password');

		$password = strlen($password);

		if($password<=7 || $password>20)
		{			
			$this->form_validation->set_message('check_password','Password should be between 8 to 20 characters in length  and include at least 1 number.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	public function valid_mobile($field){
		if($field!='')
		{ 
			$mobile = $field; 
			$this->form_validation->set_message('valid_mobile', 'The %s may only contain number,+,-.'); 
			return ( ! preg_match("/^([0-9\,\-\+])+$/i", $mobile)) ? FALSE : TRUE;  
		} 
	}

	public function email_check(){
		$email = $this->input->post('user_name');
		if($email!='')
		{
			$userId = (int) $this->logged_in_user_id;
			$cust_where = array('user_name'=>$email,'status !='=>'2');
			if($userId>0){
				$cust_where['customers_id !=']=$userId;
			}
			$res_user = $this->db->select('customers_id')->get_where('wl_customers',$cust_where)->row_array();
			if (is_array($res_user) && !empty($res_user))
			{
				$this->form_validation->set_message('email_check', $this->config->item('exists_user_id'));
				return FALSE;
			}else
			{
				return TRUE;
			} 
		}
		else
		{
			return TRUE;	
		}
	}

	public function check_mobile_exists($val){
		if($val!=''){
			$userId = (int) $this->logged_in_user_id;
			$cust_where = array('mobile_number'=>$val,'status !='=>'2');
			if($userId>0){
				$cust_where['customers_id !=']=$userId;
			}
			$res_user = $this->db->select('customers_id')->get_where('wl_customers',$cust_where)->row_array();
			if(is_array($res_user) && !empty($res_user)){
				$this->form_validation->set_message('check_mobile_exists',"Mobile Number already exists");
				return FALSE;
			}
		}
		return TRUE;
	}

	protected function check_logged_user_valid($user_id,$opts=array()){
		$user_id = (int) $user_id;
		$should_exit = isset($opts['should_exit']) ? $opts['should_exit'] : true; 
		$is_valid = false;
		if(property_exists(__CLASS__,'user') && is_object($this->user)){
			//trace($user_id."=====".$this->user->user_id);
			//Hack the status of logged in user as needed
			if($user_id===$this->user->user_id){
				$this->logged_in_user_id=$this->user->user_id;
				$is_valid = true;
			}
		}else{
			$this->db->select('*,customers_id as user_id');
			$this->db->from('wl_customers');
			$this->db->where('customers_id',$user_id);
			$this->db->where('member_type','3');
			$this->db->where('status','1');
			$query = $this->db->get();
			if($query->num_rows()>0){
				$this->user = $query->row();
				$this->logged_in_user_id=$this->user->user_id;
				$this->token=$this->request_token;
				$is_valid = true;
			}
		}
		if(!$is_valid){
			$msg = 'Invalid Username';
			if(!$should_exit){
				$this->auth_mismatch_msg = $msg;
				return FALSE;
			}
			$data = array('success'=>"false",'error'=>$msg,'err_code'=>'AUTH_MISMATCH400');
			$this->response($data, self::HTTP_UNAUTHORIZED);
			exit;
		}
		return TRUE;
	}

	public function get_admin_info($params=array()){
		$fields = !empty($params['fields']) ? $params['fields'] : '*';
		$res = get_db_single_row('tbl_admin',$fields," AND admin_id='1' ");
		return $res;
	}

	protected function is_renewable($pkg_end_date,$renew_subscription_offset=''){
		$renewable=0;
		$current_time = strtotime($this->config->item('config.date'));
		$renew_subscription_offset = $renew_subscription_offset=='' ? (int) $this->config->item('renew_subscription_offset') : $renew_subscription_offset;
		if(isset($renew_subscription_offset) && $renew_subscription_offset>0){
			$loop_exptime_offset = strtotime($pkg_end_date) - $current_time;
			$loop_exptime_days=ceil($loop_exptime_offset/(24*3600));
			if($loop_exptime_days<=$renew_subscription_offset){
				$renewable=1;
			}else{
				$renewable=0;
			}
		}
		return $renewable;
	}

	protected function fetch_test_series_items_stats($mtp_id){
		//Item Details
		$this->load->model('courses/mock_test_model');
		$total_free_items=0;
		$where_mt="mt.status='1' AND mtpi.ref_mtp_id='".$mtp_id."'";
		$where_mt = ltrim($where_mt," AND ");

		$params_mt = array(
													'fields'=>'mtpi.pfd_type,mt.mock_test_type',
													'where'=>$where_mt,
													'exjoin'=>array(
																		array('tbl'=>'wl_mtp_items as mtpi','condition'=>"mtpi.item_id=mt.mt_id",'type'=>'INNER')
																		),
													'groupby'=>'mt.mt_id',
													'debug'=>FALSE
													);

		$res_items   = $this->mock_test_model->get_mock_test($params_mt);

		$total_items = $this->mock_test_model->total_rec_found;

		$mt_type = array();
		
		if($total_items>0){
			foreach($res_items as $val){
				if(!$val['pfd_type']){
					$total_free_items++;
				}
				$mt_type[] = $val['mock_test_type'];
			}
		}
		$mt_type = array_unique($mt_type);
		$ret_arr = array('total_items'=>$total_items,'total_free_items'=>$total_free_items,'mt_type'=>$mt_type);
		return $ret_arr;
	}


	protected function fetch_vc_items_stats($course_id){
		//Item Details
		$total_free_video=0;
		$where_bank="vbk.status='1' AND vci.ref_vc_id='".$course_id."'";
		$where_bank = ltrim($where_bank," AND ");

		$params_bank = array(
													'fields'=>'vbk.rec_title,vbk.rec_file,vbk.id,vci.downloadable,vci.pfd_type',
													'where'=>$where_bank,
													'exjoin'=>array(
																			//array('tbl'=>'wl_subjects as s','condition'=>'s.subject_id=vbk.ref_subject_id'),
																			//array('tbl'=>'wl_subject_folders as fdr','condition'=>'fdr.folder_id=vbk.ref_folder_id'),
																			array('tbl'=>'wl_video_course_items as vci','condition'=>"vci.item_id=vbk.id")
																		),
													'orderby'=>'vci.item_added_date DESC',
													'groupby'=>'vbk.id',
													'debug'=>FALSE
													);

		$res_video_items   = $this->banks_model->get_video_banks($params_bank);

		$total_video_items = $this->banks_model->total_rec_found;

		if($total_video_items>0){
			foreach($res_video_items as $val){
				if(!$val['pfd_type']){
					$total_free_video++;
				}
			}
		}
		$ret_arr = array('total_items'=>$total_video_items,'total_free_video'=>$total_free_video);
		return $ret_arr;
	}

	protected function fetch_notes_items_stats($notes_id){
		//Item Details
		$total_free_pdf=0;
		$where_bank="pbk.status='1' AND nti.ref_notes_id='".$notes_id."'";
		$where_bank = ltrim($where_bank," AND ");

		$params_bank = array(
													'fields'=>'pbk.rec_title,pbk.rec_file,pbk.id,nti.downloadable,nti.pfd_type',
													'where'=>$where_bank,
													'exjoin'=>array(
																			//array('tbl'=>'wl_subjects as s','condition'=>'s.subject_id=pbk.ref_subject_id'),
																			//array('tbl'=>'wl_subject_folders as fdr','condition'=>'fdr.folder_id=pbk.ref_folder_id'),
																			array('tbl'=>'wl_notes_items as nti','condition'=>"nti.item_id=pbk.id")
																		),
													'orderby'=>'nti.item_added_date DESC',
													'groupby'=>'pbk.id',
													'debug'=>FALSE
													);

		$res_pdf_items   = $this->banks_model->get_pdf_banks($params_bank);

		$total_pdf_items = $this->banks_model->total_rec_found;

		if($total_pdf_items>0){
			foreach($res_pdf_items as $val){
				if(!$val['pfd_type']){
					$total_free_pdf++;
				}
			}
		}
		$ret_arr = array('total_pdf_items'=>$total_pdf_items,'total_free_pdf'=>$total_free_pdf);
		return $ret_arr;
	}

	protected function upgrade_membership($order_id,$use_transaction=1){
		$this->load->model(array('order/order_model'));
		$order_id = (int) $order_id;
		$res_ordmaster = $this->db->get_where('wl_order',array('order_id'=>$order_id,'order_status !='=>'Deleted'))->row_array();
		if(is_array($res_ordmaster) && !empty($res_ordmaster)){
			$err=0;
			$order_id = $res_ordmaster['order_id'];
			$res_ord_details = $this->db->get_where('wl_order_subscription_pkg_details',array('orders_id'=>$order_id))->result_array();
			if($res_ordmaster['payment_status']=='Unpaid' && !empty($res_ord_details)){
				if($use_transaction){
					$this->db->trans_start();
				}

				$where = "order_id = '".$order_id."'";
				$this->order_model->safe_update('wl_order',array('payment_status'=>'Paid'),$where,FALSE);
				foreach($res_ord_details as $odval){
					$duration  = $odval['duration'];
					$duration_unit  = $odval['duration_unit'];
					$duration_unit_type = $odval['duration_unit_type'];
					$package_valid_to =  date("Y-m-d",strtotime("+".$duration." ".$duration_unit_type));
					$update_data_ord_dtl = array('package_paid_date'  => $this->config->item('config.date.time'),'package_valid_to'=>$package_valid_to);
					$order_dtl_where = array('orders_dtl_id'=>$odval['orders_dtl_id']);
					$this->order_model->safe_update('wl_order_subscription_pkg_details',$update_data_ord_dtl,$order_dtl_where,FALSE);
					$package_type=$res_ordmaster['order_type'];
					$res_pkg_sub_exists = $this->db->select('mp_sub_id,pkg_end_date')->get_where('wl_member_package_subscription',array('ref_pkg_type_id'=>$odval['package_id'],'member_id'=>$res_ordmaster['customers_id'],'package_type'=>$package_type))->row_array();
					if(is_array($res_pkg_sub_exists) && !empty($res_pkg_sub_exists)){
						$posted_data = array(
													'ref_order_id'=>$order_id,
													'pkg_end_date'=>$package_valid_to,
													'pkg_updated'=>$this->config->item('config.date.time')
													);
						$posted_data = $this->security->xss_clean($posted_data);
						$where = "mp_sub_id = '".$res_pkg_sub_exists['mp_sub_id']."'";
						$this->order_model->safe_update('wl_member_package_subscription',$posted_data,$where,FALSE);
					}else{
						$posted_data = array(
												'package_type'=>$package_type,
												'ref_pkg_type_id'=>$odval['package_id'],
												'member_id'=>$res_ordmaster['customers_id'],
												'ref_order_id'=>$order_id,
												'pkg_enroll_date'=>$this->config->item('config.date.time'),
												'pkg_end_date'=>$package_valid_to,
												'pkg_added'=>$this->config->item('config.date.time'),
												'pkg_updated'=>$this->config->item('config.date.time')
												);
						$posted_data = $this->security->xss_clean($posted_data);
						$this->order_model->safe_insert('wl_member_package_subscription',$posted_data,FALSE);
					}
				}
				if($use_transaction){
					$this->db->trans_complete();
				}
			}
		}
	}

	protected function debit_wallet_coins($params=array()){
		$this->load->model(array('order/order_model'));
		$user_id = !empty($params['user_id']) ? (int) $params['user_id'] : 0;
		$order_id = !empty($params['order_id']) ? (int) $params['order_id'] : 0;
		$debit_points_left = !empty($params['points']) ? (int) $params['points'] : 0;
		$actual_points_debit = $debit_points_left;
		$matter_type = !empty($params['matter_type']) ? trim($params['matter_type']) : '';
		$use_transaction = !empty($params['use_transaction']) ? (int) $params['use_transaction'] : 0;
		if($user_id>0 && $order_id>0 && $actual_points_debit>0 && $matter_type!=''){
			$wlt_qry1 = "SELECT w.id,w.left_points FROM wl_wallet as w  WHERE w.left_points>0 AND w.transaction_type='Cr' AND w.user_id='".$user_id."' ORDER BY w.receive_date ASC";
			$wallet_res = $this->db->query($wlt_qry1)->result_array();
			if(!empty($wallet_res)){
				if($use_transaction){
					$this->db->trans_start();
				}
				foreach($wallet_res as $val1){
					$loop_debit_points = $val1['left_points']>$debit_points_left ? $debit_points_left : $val1['left_points'];
					$loop_debit_points = $loop_debit_points<0 ? 0 : $loop_debit_points;
					$debit_points_left=$debit_points_left-$loop_debit_points;
					$loop_left_points = $val1['left_points']-$loop_debit_points;
					$wallet_log_data = array(
																'event_type'=>1,
																'transaction_type'=>'LDr',
																'user_id'=>$user_id,
																'matter_id'   => $order_id,
																'parent_id'   => $val1['id'],
																'points'=>$loop_debit_points,
																'matter_type'=>$matter_type,
																'receive_date'=>$this->config->item('config.date.time'),
																'status'=>1
														);

					$wallet_log_data = $this->security->xss_clean($wallet_log_data);
					$this->order_model->safe_insert('wl_wallet',$wallet_log_data,FALSE);
					$wallet_update_data = array(
															'left_points'=>$loop_left_points
													);

					$wallet_update_data = $this->security->xss_clean($wallet_update_data);
					$where_wallet_update = "id = '".$val1['id']."'";
					$this->order_model->safe_update('wl_wallet',$wallet_update_data,$where_wallet_update,FALSE);

					if($debit_points_left<=0){
						break;
					}
				}
				$wallet_log_data = array(
																'event_type'=>1,
																'transaction_type'=>'Dr',
																'user_id'=>$user_id,
																'matter_id'   => $order_id,
																'points'=>$actual_points_debit,
																'matter_type'=>$matter_type,
																'receive_date'=>$this->config->item('config.date.time'),
																'status'=>1
														);

				$wallet_log_data = $this->security->xss_clean($wallet_log_data);
				$this->order_model->safe_insert('wl_wallet',$wallet_log_data,FALSE);
				if($use_transaction){
					$this->db->trans_complete();
				}
			}
		}
	}

	protected function log_referral_code($params=array()){
		$this->load->model(array('order/order_model'));
		$user_id = !empty($params['user_id']) ? (int) $params['user_id'] : 0;
		$matter_id = !empty($params['matter_id']) ? (int) $params['matter_id'] : 0;
		$referral_user_id = !empty($params['referral_user_id']) ? $params['referral_user_id'] : 0;
		$used_type = !empty($params['used_type']) ? trim($params['used_type']) : '';
		$use_transaction = !empty($params['use_transaction']) ? (int) $params['use_transaction'] : 0;
		if($user_id>0 && $matter_id>0 && $referral_user_id>0 && $used_type!=''){
			if($use_transaction){
				$this->db->trans_start();
			}
			$rf_log_data = array(
														'user_id'=>$user_id,
														'matter_id'   => $matter_id,
														'referral_user_id'   => $referral_user_id,
														'used_type'=>$used_type,
														'log_date'=>$this->config->item('config.date.time')
														);

			$rf_log_data = $this->security->xss_clean($rf_log_data);
			$this->order_model->safe_insert('wl_referral_code_applied',$rf_log_data,FALSE);
			$this->db->query("UPDATE wl_customers SET my_ref_code_users=my_ref_code_users+1 WHERE customers_id='".$referral_user_id."'");
			if($use_transaction){
				$this->db->trans_complete();
			}
		}
	}

	protected function send_order_notification($params){
		$this->load->library('Dmailer');
		if(!function_exists('order_package_invoice_mail_content')){
			$this->load->helper('apis/api_cart');
		}
		//Mail
		$admin_info = $this->get_admin_info(array('fields'=>'admin_email,company_name'));
		ob_start();
		$params_mailcontent=array(
													'order_id'=>$params['order_id'],
													'order_type'=>$params['order_type']
												);
		$mailcontent=order_package_invoice_mail_content($params_mailcontent);
		$invoice_master_data = $params_mailcontent['invoice_master_data'];
		$invoice_item_details_data = $params_mailcontent['invoice_item_details_data'][0];
		$user_email = $invoice_master_data['email'];
		$user_id = $invoice_master_data['customers_id'];
		$invoice_mail_content= ob_get_contents();
		ob_clean();
		//Send Mail to User
		$mail_subject = $this->config->item('site_name')." Order overview";
		$mail_conf =  array(
										'subject'    => $mail_subject,
										'to_email'   => $user_email,
										'from_email' => $admin_info['admin_email'],
										'from_name'  => $this->config->item('site_name'),
										'body_part'  => $invoice_mail_content
										);

		@$this->dmailer->mail_notify($mail_conf);
		
		//Send Mail to Admin
		$mail_subject = $this->config->item('site_name')." Order overview";
		$mail_conf =  array(
										'subject'    => $mail_subject,
										'to_email'   => $admin_info['admin_email'],
										'from_email' => $admin_info['admin_email'],
										'from_name'  => $this->config->item('site_name'),
										'body_part'  => $invoice_mail_content
										);

		@$this->dmailer->mail_notify($mail_conf);

		//Notification Message
		$order_type = $invoice_master_data['order_type'];
		switch($order_type){
			case 1:
				$duration_txt = $invoice_item_details_data['duration']." ".ucwords($invoice_item_details_data['duration_unit_type']);
				$msg_title = "Category Subscription Purchased";
				$msg_desc = "Title: ".$invoice_item_details_data['package_title'].", Duration: $duration_txt";
			break;
			case 2:
				$duration_txt = $invoice_item_details_data['duration']." ".ucwords($invoice_item_details_data['duration_unit_type']);
				$msg_title = "Course Purchased";
				$msg_desc = "Title: ".$invoice_item_details_data['package_title'].", Duration: $duration_txt";
			break;
			case 3:
				$duration_txt = $invoice_item_details_data['duration']." ".ucwords($invoice_item_details_data['duration_unit_type']);
				$msg_title = "Video Course Purchased";
				$msg_desc = "Title: ".$invoice_item_details_data['package_title'].", Duration: $duration_txt";
			break;
			case 4:
				$duration_txt = $invoice_item_details_data['duration']." ".ucwords($invoice_item_details_data['duration_unit_type']);
				$msg_title = "Notes Purchased";
				$msg_desc = "Title: ".$invoice_item_details_data['package_title'].", Duration: $duration_txt";
			break;
			case 5:
				$duration_txt = $invoice_item_details_data['duration']." ".ucwords($invoice_item_details_data['duration_unit_type']);
				$msg_title = "Test Series Purchased";
				$msg_desc = "Title: ".$invoice_item_details_data['package_title'].", Duration: $duration_txt";
			break;
			default:
				return;
		}

		$params_notification = array(
																	'nf_type'=>3,
																	'message_title'=>$msg_title,
																	'message_desc'=>$msg_desc,
																	'user_id'=>$user_id,
																	'notification_type'=>'both'
																);
		//trace($params_notification);
		$this->custom_notification->send_notification($params_notification);
	}

}
/*End of file */