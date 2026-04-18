<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Public_Controller extends MY_Controller
{
	public $meta_info;

	public function __construct()
	{		
		parent::__construct();
		$this->load->library('Custom_notification');
	}
	
	protected function send_order_notification($params){
		$this->load->library('Dmailer');
		if(!function_exists('order_package_invoice_mail_content')){
			$this->load->helper('apis/api_cart');
		}
		//Mail
		$admin_info = get_db_single_row('tbl_admin','admin_email,company_name'," AND admin_id='1' ");
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
				$url_params = array('order_token_id'=>$invoice_master_data['order_token_id'],'order_id'=>$invoice_master_data['order_id']);
				$url_hint = 'order_purchased';
			break;
			case 2:
				$duration_txt = $invoice_item_details_data['duration']." ".ucwords($invoice_item_details_data['duration_unit_type']);
				$msg_title = "Course Purchased";
				$msg_desc = "Title: ".$invoice_item_details_data['package_title'].", Duration: $duration_txt";
				$url_params = array('order_token_id'=>$invoice_master_data['order_token_id'],'order_id'=>$invoice_master_data['order_id']);
				$url_hint = 'order_purchased';
			break;
			case 3:
				$duration_txt = $invoice_item_details_data['duration']." ".ucwords($invoice_item_details_data['duration_unit_type']);
				$msg_title = "Video Course Purchased";
				$msg_desc = "Title: ".$invoice_item_details_data['package_title'].", Duration: $duration_txt";
				$url_params = array('order_token_id'=>$invoice_master_data['order_token_id'],'order_id'=>$invoice_master_data['order_id']);
				$url_hint = 'order_purchased';
			break;
			case 4:
				$duration_txt = $invoice_item_details_data['duration']." ".ucwords($invoice_item_details_data['duration_unit_type']);
				$msg_title = "Notes Purchased";
				$msg_desc = "Title: ".$invoice_item_details_data['package_title'].", Duration: $duration_txt";
				$url_params = array('order_token_id'=>$invoice_master_data['order_token_id'],'order_id'=>$invoice_master_data['order_id']);
				$url_hint = 'order_purchased';
			break;
			case 5:
				$duration_txt = $invoice_item_details_data['duration']." ".ucwords($invoice_item_details_data['duration_unit_type']);
				$msg_title = "Test Series Purchased";
				$msg_desc = "Title: ".$invoice_item_details_data['package_title'].", Duration: $duration_txt";
				$url_params = array('order_token_id'=>$invoice_master_data['order_token_id'],'order_id'=>$invoice_master_data['order_id']);
				$url_hint = 'order_purchased';
			break;
			default:
				return;
		}

		$params_notification = array(
																	'nf_type'=>3,
																	'message_title'=>$msg_title,
																	'message_desc'=>$msg_desc,
																	'user_id'=>$user_id,
																	'notification_type'=>'both',
																	'url_hint'=>$url_hint,
																	'url_params'=>$url_params
																);
		//trace($params_notification);
		$this->custom_notification->send_notification($params_notification);
	}

	

}