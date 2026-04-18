<?php
class Notifications extends Private_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('notification/notification_model');
		$this->form_validation->set_error_delimiters("<div class='required'>","</div>");
	}

	public function index(){
		is_access_method($permission_type=1,$sec_id='1');
		$user_id = $this->userId;
		$page_heading = "Notifications";
		$this->mem_top_menu_section = 'notifications';
		$is_xhr = $this->input->is_ajax_request();
		$per_page_res = validate_per_page();
		$per_page = $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$keyword = $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$status = $this->db->escape_str($this->input->get_post('status',TRUE));
		
		if($this->mres['member_type'] == '1'){
			$where_notification="";
			if($keyword!='')
			{
				$where_notification.=" AND  ( wn.notification_title like '%$keyword%' ) ";
			}
	
			if($status!='')
			{
				$where_notification.=" AND  wn.status='".$status."' ";
			}
	
			$where_notification = ltrim($where_notification," AND ");
	
			$params_notification = array(	
							'offset'=>$db_offset,
							'limit'=>$per_page,
							'where'=>$where_notification,
							'debug'=>FALSE
						);
						
		}else{
			
			$this->notification_model->safe_update('wl_notification_customer',array('read_status'=>'R'),array('customer_id'=>$user_id),FALSE);
			
			$where_notification="nc.customer_id='".$user_id."' AND wn.status='1' AND (wn.member_type='1' OR wn.member_type='0' )";

			if($from_date!=''){
				$where_notification.=" AND  ( DATE(nc.created_at) >='$from_date' ) ";
			}
			if($to_date!=''){
				$where_notification.=" AND  ( DATE(nc.created_at) <='$to_date' ) ";
			}
	
			$params_notification = array(	
									'fields'=>'wn.notification_title,wn.description,wn.notification_image,nc.created_at,nc.read_status,nc.id,wn.url_hint,wn.url_params,wn.notification_id',
									'offset'=>$db_offset,
									'limit'=>$per_page,
									'orderby'=>'nc.created_at DESC',
									'exjoin'=>array(
												array('tbl'=>'wl_notification_customer as nc','condition'=>'nc.notification_id=wn.notification_id')
											),
									'where'=>$where_notification,
									'debug'=>FALSE
									);
			
		}
		
		$res_notification_data  =  $this->notification_model->get_notification($params_notification);
		$total_recs           =   $this->notification_model->total_rec_found;

		$params_pagination = array(
			'data_form'=>'#search_form',
			'base_link'=>$base_link,
			'per_page'=>$per_page,
			'total_recs'=>$total_recs,
			'uri_segment'=>$offset,
			'refresh'=>1
		);
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['res_notification_data']=$res_notification_data;
		$data['heading_title'] = $page_heading;
		if($is_xhr){
			$this->load->view('notifications/load_notification_data',$data);
		}else{
			$this->load->view('notifications/view_notification_list',$data);
		}
	}

	public function add()
	{
		is_access_method($permission_type=2,$sec_id='1');
		$data['heading_title'] = 'Add Notification';
		if($this->input->post('action',TRUE)!=''){
			$this->form_validation->set_rules('notification_title','Notification Title',"trim|required|max_length[220]|unique[wl_notification.notification_title='".$this->db->escape_str($this->input->post('notification_title'))."' AND status!='2']");		
			$this->form_validation->set_rules('description','Description',"trim|required|max_length[500]");

			if($this->form_validation->run()==TRUE)
			{
				$mem_nature = 1;
				$posted_data = array(
				'notification_title'=>$this->input->post('notification_title',TRUE),
				'description'=>$this->input->post('description',TRUE),
				'member_type'=>$mem_nature,
				'nf_type'=>'3',
				'status'=>'1',
				'created_at'=>$this->config->item('config.date.time')
				);
				
				$posted_data = $this->security->xss_clean($posted_data);
				$notification_id = $this->notification_model->safe_insert('wl_notification',$posted_data,FALSE);
				//$this->send_notification(array('notification_id'=>$notification_id,'mem_nature'=>$mem_nature));
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',lang('success'));
				redirect_top('admin/notifications', '');
			}
		}
		$this->load->view('notifications/view_notification_add',$data);
	}

	private function send_notification($param =array()){
		$this->load->library(array("dmailer"));
		$this->load->model(array('admin/admin_model'));
		$notification_id = $param['notification_id'] ?? 0;
		if($notification_id>0) 
		{
			$mem_nature = $param['mem_nature'] ?? 1;
			$condition ="m.status != '2' AND m.member_type='3'";
			if($mem_nature > 0){
				$condition .=" AND m.mem_nature='".$mem_nature."'";
			}
			$paramc_emp = array(
					'fields'=>"m.customers_id,CONCAT_WS(' ',m.first_name,m.last_name) AS mem_name,m.user_name",
					'where'=>$condition,
					'groupby'=>'m.customers_id',
					'debug'=>FALSE
				);
			$res_emp = $this->admin_model->get_members($paramc_emp);
		 	if(is_array($res_emp) && !empty($res_emp)){
          		$notification_res = get_db_single_row('wl_notification','notification_title,description',array("notification_id"=>$notification_id));
				$notification_rec_exists = is_array($notification_res) && !empty($notification_res) ? true : false;
          		$notification_mail_control_value = 'Y';
				if($notification_mail_control_value=='Y'){
					$content    =  get_content('wl_auto_respond_mails','7');
					$subject    =  $content->email_subject;
					$subject	=	str_replace('{site_name}',$this->config->item('site_name'),$subject);
				}
				foreach ($res_emp as $key => $val){
					$check_notification = count_record('wl_notification_customer',"notification_id='".$notification_id."' AND customer_id='".$val['customers_id']."'");
					if($check_notification=='0'){
						$posted_data = array(
								'notification_id'=>$notification_id,
								'customer_id'=>$val['customers_id'],
								'created_at'=>$this->config->item('config.date.time'),							
							);
							
						$posted_data = $this->security->xss_clean($posted_data);
						$this->notification_model->safe_insert('wl_notification_customer',$posted_data,FALSE);
						if($notification_mail_control_value=='Y' && $notification_rec_exists){
							$body       	=  $content->email_content;
							$body			=	str_replace('{mem_name}',$val['mem_name'],$body);
							$body			=	str_replace('{notification_title}',$notification_res['notification_title'],$body);
							$body			=	str_replace('{description}',$notification_res['description'],$body);
							$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
							$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
							$mail_conf =  array(
								'subject'    => $subject,
								'to_email'   => $val['user_name'],
								'from_email' => $this->admin_info->admin_email,
								'from_name'  => $this->config->item('site_name'),
								'body_part'  => $body
							);
							//@$this->dmailer->mail_notify($mail_conf);
						}
					}
				}
          	}
		}
	}
	
	public function edit()
	{
		is_access_method($permission_type=2,$sec_id='1');
		$data['heading_title'] = 'Edit Notification';
		$Id = (int) $this->uri->segment(4);
		$where_notification = "wn.notification_id='".$Id."'";
		$params_notification = array(	
							'where'=>$where_notification,
							'fetch_type'=>'row_array'
						);
		$res   =  $this->notification_model->get_notification($params_notification);
		if( is_array($res) && !empty($res) )
		{
			$this->form_validation->set_rules('notification_title','Notification  Title',"trim|required|max_length[220]|unique[wl_notification.notification_title='".$this->db->escape_str($this->input->post('notification_title'))."' AND status!='2' AND notification_id != ".$Id."]");
			
			$this->form_validation->set_rules('description','Description',"trim|required|max_length[500]");
			
			
			if($this->form_validation->run()==TRUE)
			{				
				$posted_data = array(
					'notification_title'=>$this->input->post('notification_title',TRUE)	,
					'description'=>$this->input->post('description',TRUE),
					'member_type'=>$this->input->post('member_type',TRUE),			
				);
				
				$posted_data = $this->security->xss_clean($posted_data);
				$where = "notification_id = '".$res['notification_id']."'";
				$this->notification_model->safe_update('wl_notification',$posted_data,$where,FALSE);
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',lang('successupdate'));
				
				redirect_top('admin/notifications/'.query_string(), '');
				
			}
			
			$data['res']=$res;
			$this->load->view('notifications/view_notification_edit',$data);
			
		}
		else
		{
			redirect('admin/notifications', '');
		}
	}
	
	public function notification_delete()
	{
		is_access_method($permission_type=2,$sec_id='1');
		$Id = $this->uri->segment(4);
		$is_exist = count_record('wl_notification'," md5(notification_id)='".$Id."' ");
		if($is_exist>0)
		{
			$where = "md5(notification_id) = '".$Id."'";
			$this->notification_model->safe_update('wl_notification',array('status'=>'2'),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Record has been Deleted successfully.");
			redirect('admin/notifications'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/notifications'); 
		}		
	}
	
}
/* End of file*/