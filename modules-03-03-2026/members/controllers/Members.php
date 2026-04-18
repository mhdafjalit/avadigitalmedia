<?php
class Members extends Private_Member_Controller
{
	private $mId;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('admin/admin_model','events/events_model'));
		$this->load->library(array('safe_encrypt', 'Dmailer'));
		$this->form_validation->set_error_delimiters("<div class='required'>","</div>");
	}

	public function index()
	{
		$this->myaccount();
	}

	public function myaccount(){
		//trace($this->session->userdata());
		$data['heading_title'] = "Dashboard";
		$this->mem_top_menu_section = 'dashboard';

		$condition = " AND member_id = '".$this->userId."' ";

		$data['total_music_releases'] = count_record('wl_signed_albums'," status!='2' AND is_verify_meta='0' AND is_pdl_submit='0' ".$condition);
		$data['total_music_process_releases'] = count_record('wl_signed_albums'," status!='2' AND is_verify_meta='1' AND is_pdl_submit='0' ".$condition);
		$data['total_music_final_releases'] = count_record('wl_signed_albums'," status!='2' AND is_verify_meta='1' AND is_pdl_submit='1' ".$condition);

		//$data['total_music_releases'] = count_record('wl_releases'," status='0' AND album_type='1' AND member_id='".$this->userId."'");
		$data['total_video_releases'] = count_record('wl_releases'," status='0' AND album_type='2' AND member_id='".$this->userId."'");
		//$data['total_music_process_releases'] = count_record('wl_releases'," status='5' AND album_type='1' AND member_id='".$this->userId."'");
		$data['total_video_process_releases'] = count_record('wl_releases'," status='5' AND album_type='2' AND member_id='".$this->userId."'");
		//$data['total_music_final_releases'] = count_record('wl_releases'," status='1' AND album_type='1' AND member_id='".$this->userId."'");
		$data['total_video_final_releases'] = count_record('wl_releases'," status='1' AND album_type='2' AND member_id='".$this->userId."'");
		$data['total_music_rejected_releases'] = count_record('wl_releases'," status='3' AND album_type='1' AND member_id='".$this->userId."'");
		$data['total_video_rejected_releases'] = count_record('wl_releases'," status='3' AND album_type='2' AND member_id='".$this->userId."'");
		$data['total_music_takedown_releases'] = count_record('wl_releases'," status='4' AND album_type='1' AND member_id='".$this->userId."'");
		$data['total_video_takedown_releases'] = count_record('wl_releases'," status='4' AND album_type='2' AND member_id='".$this->userId."'");
		$data['total_earning'] 	= 0;
		$data['total_dabit'] 	= 0;
		$data['balance_amt'] 	= 0;
		$data['commission_amt'] = 0;
        $params_events = array(
			'fields'=>'n.*,nm.media',
			'limit'=>2,
			'where'=>"n.status='1'",
			'exjoin'=>array(
				array('tbl'=>'wl_events_media as nm','condition'=>"nm.news_id=n.news_id AND nm.media_type='photo'",'type'=>'LEFT')
			),
			'orderby'=>"n.news_id DESC",
			'groupby'=>'n.news_id',
			'debug'=>FALSE
		);
		$res_events   = $this->events_model->get_events($params_events);
        $data['res_events']= $res_events;
		$data['x_dsg_page'] = 'home';
		$this->load->view('dashboard',$data);
	}

	public function edit_account()
	{
		$this->mem_top_menu_section = 'my_profile';
		if($this->input->post("action")=="edit_account")
		{
			$is_xhr = $this->input->is_ajax_request();
			$img_allow_size =  $this->config->item('allow.file.size');
			$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
			$this->form_validation->set_rules('first_name', 'Name', 'trim|required|alpha|max_length[80]');
			$this->form_validation->set_rules('mobile_number', 'Mobile', 'trim|required|numeric|min_length[10]|max_length[12]');
			$this->form_validation->set_rules('country','Country', 'trim|required');
			$this->form_validation->set_rules('state', 'State', 'trim|required');
			$this->form_validation->set_rules('city', 'City', 'trim|required');
			$this->form_validation->set_rules('pin_code', 'Post Code', 'trim|max_length[6]');
			$this->form_validation->set_rules('profile_photo','Profile Photo',"file_allowed_type[image]|file_size_max[$img_allow_size]|check_dimension[$img_allow_dim]");
			$this->form_validation->set_rules('address', 'Address', 'trim|required|max_length[200]');
				$this->form_validation->set_rules('aadhar_doc','Aadhar Card',"file_allowed_type[pdf]|file_size_max[$img_allow_size]");
				$this->form_validation->set_rules('agreement_doc','User Agreement',"file_allowed_type[pdf]|file_size_max[$img_allow_size]");
			if ($this->form_validation->run() == TRUE){
				$profile_photo = $this->mres['profile_photo'];
				$unlink_image = array('source_dir'=>"profiles",'source_file'=>$profile_photo);
				if($this->input->post('prof_img_delete')==='Y'){
					removeImage($unlink_image);
					$profile_photo = NULL;
				}
				if( !empty($_FILES) && $_FILES['profile_photo']['name']!='' ){
					
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('profile_photo','profiles');
					
					if( is_array($uploaded_data)  && !empty($uploaded_data) ){
						$profile_photo = $uploaded_data['upload_data']['file_name'];
						removeImage($unlink_image);
					}
				}
				$aadhar_doc = $this->mres['aadhar_doc'];
				$unlink_aadhar_doc = array('source_dir'=>"members",'source_file'=>$aadhar_doc);
				if($this->input->post('aadhar_doc_delete')==='Y'){
					removeImage($unlink_aadhar_doc);
					$aadhar_doc = NULL;
				}
				if( !empty($_FILES) && $_FILES['aadhar_doc']['name']!='' ){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('aadhar_doc','members');
					if( is_array($uploaded_data)  && !empty($uploaded_data) ){
						$aadhar_doc = $uploaded_data['upload_data']['file_name'];
						removeImage($unlink_aadhar_doc);
					}
				}
				$agreement_doc = $this->mres['agreement_doc'];
				$unlink_agreement_doc = array('source_dir'=>"members",'source_file'=>$agreement_doc);
				if($this->input->post('agreement_doc_delete')==='Y'){
					removeImage($unlink_agreement_doc);
					$agreement_doc = NULL;
				}
				if( !empty($_FILES) && $_FILES['agreement_doc']['name']!='' ){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('agreement_doc','members');
					if( is_array($uploaded_data)  && !empty($uploaded_data) ){
						$agreement_doc = $uploaded_data['upload_data']['file_name'];
						removeImage($unlink_agreement_doc);
					}
				}	
				$country_id = $this->input->post('country');
				/*
				$state_id = $this->input->post('state');
				$city_id = $this->input->post('city');

				$city_res = $city_id > 0  ? log_fetched_rec($city_id,'city','title') : '';
				$city_name = !empty($city_res['rec_data']) ? $city_res['rec_data']['title'] : '';
				$state_res = $state_id > 0  ? log_fetched_rec($state_id,'state','title') : '';
				$state_name = !empty($state_res['rec_data']) ? $state_res['rec_data']['title'] : '';
				*/
				$country_res = $country_id > 0  ? log_fetched_rec($country_id,'country','country_name') : '';
				$country_name = !empty($country_res['rec_data']) ? $country_res['rec_data']['country_name'] : '';
				$posted_user_data = array(
					"first_name"	=> $this->input->post('first_name',TRUE), 
					"mobile_number"	=> $this->input->post('mobile_number',TRUE),
					'profile_photo'	=> $profile_photo,
					'aadhar_doc'	=> $aadhar_doc,
					'agreement_doc'	=> $agreement_doc,
					'address' 		=> $this->input->post('address',TRUE),
					'country'		=> $this->input->post('country',TRUE),
					'pin_code'		=> $this->input->post('pin_code',TRUE),
					'country_name'	=> $country_name,
					'state_name'	=> $this->input->post('state',TRUE),	
					'city_name'		=> $this->input->post('city',TRUE)
				); 
				$posted_user_data = $this->security->xss_clean($posted_user_data);
				$where = "customers_id = '".$this->mres['customers_id']."'";
				$this->admin_model->safe_update('wl_customers',$posted_user_data,$where,FALSE);
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',$this->config->item('myaccount_update'));
				if($is_xhr){
					$ret_data = array('status'=>'1');
					echo json_encode($ret_data);
					exit;
				}
				redirect('members/edit_account', '');
			}
		}
		$data['heading_title'] = "My Profile";
		$this->load->view('edit_account_settings',$data);
	}
	
	public function change_password()
	{
		if($this->input->post('action')!=''){
			$this->form_validation->set_rules('old_password', 'Old Password', 'trim|required');
			$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|valid_password');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[new_password]');

			if ($this->form_validation->run() == TRUE)
			{
				$password_old   =  $this->safe_encrypt->encode($this->input->post('old_password',TRUE));
				$mres           =  $this->admin_model->get_member_row($this->userId," AND password='$password_old' ");

				if( is_array($mres) && !empty($mres) )
				{
					$password = $this->safe_encrypt->encode($this->input->post('new_password',TRUE));
					$data = array('password'=>$password);
					$where = "customers_id=".$this->userId." ";
					$this->admin_model->safe_update('wl_customers',$data,$where,FALSE);
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success',$this->config->item('myaccount_password_changed'));
				}
				else
				{
					$this->session->set_userdata(array('msg_type'=>'warning'));
					$this->session->set_flashdata('warning',$this->config->item('myaccount_password_not_match'));
				}
				redirect('members/change_password','');
			}
		}
		$this->mem_top_menu_section = 'change_password';
		$data['heading_title'] = "Change Password";
		$this->load->view('edit_account_password',$data);
	}

	public function edit_bank_info()
	{
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'Edit Profile';
		$img_allow_size =  $this->config->item('allow.file.size');
		$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
		if($this->input->post('action')!=''){
			
			$this->form_validation->set_rules('pan_number', 'PAN Card Number', 'trim|required|max_length[12]|callback_validate_pancard');
			$this->form_validation->set_rules('is_gst', 'GST','trim|required');
			if($this->input->post('is_gst',TRUE)=='1'){
				$this->form_validation->set_rules('gst_number', 'GST Number','trim|required|callback_validate_gst');
			}
			$this->form_validation->set_rules('bank_account_type', 'Account Type','trim|required');
			if($this->input->post('bank_account_type',TRUE)=='1')
			{
				$this->form_validation->set_rules('bank_service_provider', 'Service Provider','trim|required');
				$this->form_validation->set_rules('bank_email', 'Bank Email Id','trim|required');
				$this->form_validation->set_rules('bank_customer_id', 'Bank Customer Id','trim|required');
			}
			else
			{
				$this->form_validation->set_rules('bank_name', 'Bank Name','trim|required|alpha|max_length[50]');
				$this->form_validation->set_rules('ac_holder_name', 'Account Holder Name','trim|alpha|required|max_length[50]');
				$this->form_validation->set_rules('ifsc_code', 'IFSC Code','trim|required|callback_validate_ifsc');
				$this->form_validation->set_rules('account_no', 'Account No.','trim|required|numeric|min_length[10]|max_length[20]');
				$this->form_validation->set_rules('bank_address', 'Bank Address','trim|required|max_length[250]');
			}
			if ($this->form_validation->run() == TRUE)
			{	
				
				$updated_data = array(
					"ac_holder_name"		=> $this->input->post('ac_holder_name',TRUE), 
					"bank_name"				=> $this->input->post('bank_name',TRUE), 
					'account_no' 			=> $this->input->post('account_no',TRUE),
					'is_gst'				=> $this->input->post('is_gst',TRUE),
					'gst_number'			=> $this->input->post('gst_number',TRUE),	
					'ifsc_code'				=> $this->input->post('ifsc_code',TRUE),
					'pan_number'			=> $this->input->post('pan_number',TRUE),
					'bank_address'			=> $this->input->post('bank_address',TRUE),
					"bank_account_type"		=> $this->input->post('bank_account_type',TRUE),
					"bank_service_provider"	=> $this->input->post('bank_service_provider',TRUE),
					"bank_email"			=> $this->input->post('bank_email',TRUE),
					"bank_customer_id"		=> $this->input->post('bank_customer_id',TRUE),
				);  
				
				$updated_data = $this->security->xss_clean($updated_data); 
				$where = "customers_id = '".$this->mres['customers_id']."'";
				$this->admin_model->safe_update('wl_customers',$updated_data,$where,FALSE);
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success', 'Details has been updated successfully.');
				redirect(current_url_query_string(), '');
			}
		}
		$this->load->view('view_member_bank_info_edit',$data);
	}

	public function edit_user_rate()
	{
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'Edit Profile';
		if($this->input->post('action')!=''){

			$this->form_validation->set_rules('commission', 'Commission','trim|required|greater_than_equal_to[0]');
			if ($this->form_validation->run() == TRUE)
			{	
				
				$updated_data = array(
					"commission" => $this->input->post('commission',TRUE),
				);  
				
				$updated_data = $this->security->xss_clean($updated_data); 
				$where = "customers_id = '".$this->mres['customers_id']."'";
				$this->admin_model->safe_update('wl_customers',$updated_data,$where,FALSE);
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success', 'Details has been updated successfully.');
				redirect(current_url_query_string(), '');
			}
		}
		$this->load->view('view_member_commission_edit',$data);
	}

	public function view_profile()
	{
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'View Profile';
		$this->load->view('view_member_profile',$data);
	}

	public function labels()
	{
		$this->mem_top_menu_section = 'labels';
		$data['heading_title'] = "Manage Labels";
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "members/labels";
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$condition 		= "wl.status != '2' AND wl.member_id='".$this->userId."'";
		if($keyword!='')
		{
			$condition.=" AND  ( wl.channel_name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wl.label_id DESC";
		$param_label = array(
					'offset'=>$db_offset,
					'limit'=>$per_page,
					'where'=>$condition,
					'orderby'=>$sort_by_rec,
					'groupby'=>'wl.label_id',
					'debug'=>FALSE
				);
		$res_array 	= $this->admin_model->get_labels($param_label);
		$total_recs = $this->admin_model->total_rec_found;
		$params_pagination = array(
			'base_link'=>$base_link,
			'per_page'=>$per_page,
			'total_recs'=>$total_recs,
			'uri_segment'=>$offset,
			'refresh'=>1
		);
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['res'] 		= $res_array;
		$this->load->view("members/view_labels_list",$data);
	}

	public function add_label()
	{
		$this->mem_top_menu_section = 'labels';
		$img_allow_size =  $this->config->item('allow.file.size');
		$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
		$data['heading_title'] = 'Add Label';
		if($this->input->post('action')!=''){
			$this->form_validation->set_rules('channel_name', 'Channel Name', 'trim|required|alpha|max_length[80]');
			$this->form_validation->set_rules('channel_url', 'Channel URL', 'trim|required|valid_url');
			$this->form_validation->set_rules('email', 'Email ID','trim|required|valid_email|max_length[80]');
			$this->form_validation->set_rules('phone', 'Phone No.', 'trim|required|numeric|min_length[10]|max_length[15]');
			$this->form_validation->set_rules('user_rate', 'User Rate', 'trim|required|greater_than_equal_to[0]');
			$this->form_validation->set_rules('agreement_from', 'Agreement Start', 'trim|required');
			$this->form_validation->set_rules('agreement_to', 'Agreement End', 'trim|required');
			$this->form_validation->set_rules('government_doc','Government Id',"file_required|file_allowed_type[pdf]|file_size_max[$img_allow_size]");
			$this->form_validation->set_rules('agreement_doc','User Agreement',"file_required|file_allowed_type[pdf]|file_size_max[$img_allow_size]");
			if ($this->form_validation->run() == TRUE)
			{
				$government_doc = "";
				if( !empty($_FILES['government_doc']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('government_doc','labels');
					if( is_array($uploaded_data)  && !empty($uploaded_data) ){
						$government_doc = $uploaded_data['upload_data']['file_name'];
					}
				}
				$agreement_doc = "";
				if( !empty($_FILES['agreement_doc']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('agreement_doc','labels');
					if( is_array($uploaded_data)  && !empty($uploaded_data) ){
						$agreement_doc = $uploaded_data['upload_data']['file_name'];
					}
				}
				$posted_data = array(
					'member_id' 	=> $this->userId,
					"channel_name"	=> $this->input->post('channel_name',TRUE), 
					"channel_url"	=> $this->input->post('channel_url',TRUE),
					'email' 		=> $this->input->post('email',TRUE),
					'phone'			=> $this->input->post('phone',TRUE),
					'user_rate'		=> $this->input->post('user_rate',TRUE),
					'agreement_from'=> $this->input->post('agreement_from',TRUE),
					'agreement_to'	=> $this->input->post('agreement_to',TRUE),
					'government_doc'=> $government_doc,
					'agreement_doc'	=> $agreement_doc,
					'created_date'	=> $this->config->item('config.date.time')
				);  
				
				$posted_data = $this->security->xss_clean($posted_data); 
				$this->admin_model->safe_insert('wl_labels',$posted_data);
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success', 'You have added label successfully.');
				redirect('members/labels', '');
			}
		}
		$this->load->view('members/view_label_add',$data);
	}

	public function edit_label()
	{
		$data['heading_title'] = 'Edit Label';
		$this->mem_top_menu_section = 'labels';
		$img_allow_size 		    =  $this->config->item('allow.file.size');
		$Id = $this->uri->segment(3);
		$condition = "md5(wl.label_id)='".$Id."'";
		$param_label = array(
					'where'=>$condition,
					'return_type'=>'row_array',
					'debug'=>FALSE
				);
		$res = $this->admin_model->get_labels($param_label);
		if( is_array($res) && !empty($res) )
		{
			$this->form_validation->set_rules('channel_name', 'Channel Name', 'trim|required|alpha|max_length[80]');
			$this->form_validation->set_rules('channel_url', 'Channel URL', 'trim|required|valid_url');
			$this->form_validation->set_rules('email', 'Email ID','trim|required|valid_email|max_length[80]');
			$this->form_validation->set_rules('phone', 'Phone No.', 'trim|required|numeric|min_length[10]|max_length[15]');
			$this->form_validation->set_rules('user_rate', 'User Rate', 'trim|required|greater_than_equal_to[0]');
			$this->form_validation->set_rules('agreement_from', 'Agreement Start', 'trim|required');
			$this->form_validation->set_rules('agreement_to', 'Agreement End', 'trim|required');
			$this->form_validation->set_rules('government_doc','Government Id',"file_allowed_type[pdf]|file_size_max[$img_allow_size]");
			$this->form_validation->set_rules('agreement_doc','User Agreement',"file_allowed_type[pdf]|file_size_max[$img_allow_size]");
			if($this->form_validation->run()==TRUE)
			{		
				$government_doc = $res['government_doc'];
				$unlink_government_doc = array('source_dir'=>"labels",'source_file'=>$government_doc);
				if($this->input->post('government_doc_delete')==='Y'){
					removeImage($unlink_government_doc);
					$government_doc = NULL;
				}
				if( !empty($_FILES) && $_FILES['government_doc']['name']!='' ){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('government_doc','labels');
					if( is_array($uploaded_data)  && !empty($uploaded_data) ){
						$government_doc = $uploaded_data['upload_data']['file_name'];
						removeImage($unlink_government_doc);
					}
				}
				$agreement_doc = $res['agreement_doc'];
				$unlink_agreement_doc = array('source_dir'=>"labels",'source_file'=>$agreement_doc);
				if($this->input->post('agreement_doc_delete')==='Y'){
					removeImage($unlink_agreement_doc);
					$agreement_doc = NULL;
				}
				if( !empty($_FILES) && $_FILES['agreement_doc']['name']!='' ){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('agreement_doc','labels');
					if( is_array($uploaded_data)  && !empty($uploaded_data) ){
						$agreement_doc = $uploaded_data['upload_data']['file_name'];
						removeImage($unlink_agreement_doc);
					}
				}		
				$posted_data = array(
					'member_id' 	=> $this->userId,
					"channel_name"	=> $this->input->post('channel_name',TRUE), 
					"channel_url"	=> $this->input->post('channel_url',TRUE),
					'email' 		=> $this->input->post('email',TRUE),
					'phone'			=> $this->input->post('phone',TRUE),
					'user_rate'		=> $this->input->post('user_rate',TRUE),
					'agreement_from'=> $this->input->post('agreement_from',TRUE),
					'agreement_to'	=> $this->input->post('agreement_to',TRUE),
					'government_doc'=> $government_doc,
					'agreement_doc'	=> $agreement_doc,
					'updated_date'	=> $this->config->item('config.date.time')
				);  
				
				$posted_data = $this->security->xss_clean($posted_data); 
				$where = "label_id ='".$res['label_id']."'";
				$this->admin_model->safe_update('wl_labels',$posted_data,$where,FALSE);
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',lang('successupdate'));
				redirect('members/labels/'.query_string(), '');
			}
			$data['res'] = $res;
			$this->load->view('members/view_label_edit',$data);
			
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'warning'));
			$this->session->set_flashdata('warning',"Label Id is not exist.");
			redirect('members/labels', '');
		}
	}

	public function label_delete()
	{
		$labelId = $this->uri->segment(3);
		$is_exist = count_record('wl_labels'," md5(label_id)='".$labelId."' ");
		if($is_exist>0)
		{
			$where = "md5(label_id) = '".$labelId."'";
			$this->admin_model->safe_update('wl_labels',array('status'=>'2'),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Label has been Deleted successfully.");
			redirect('members/labels'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('members/labels'); 
		}		
	}

	public function meta_data()
	{
		redirect('admin/metas/'); 
		exit;
		
		$this->mem_top_menu_section = 'meta_data';
		$data['heading_title'] = "Meta Data";
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "members/meta_data";
		$condition 	="md.status != '2' AND md.member_id='".$this->userId."'";
		$sort_by_rec ="md.md_id DESC";
		$param_md = array(
				'fields'=>"md.*,wl.channel_name,wl.agreement_doc",
				'offset'=>$db_offset,
				'limit'=>$per_page,
				'where'=>$condition,
				'exjoin'=>array(
					array('tbl'=>'wl_labels as wl','condition'=>"wl.label_id=md.label_id AND wl.status='1'")
				),
				'orderby'=>$sort_by_rec,
				'groupby'=>'md.md_id',
				'debug'=>FALSE
			);
		$res_array = $this->admin_model->get_metadata($param_md);
		$total_recs = $this->admin_model->total_rec_found;
		$params_pagination = array(
			'base_link'=>$base_link,
			'per_page'=>$per_page,
			'total_recs'=>$total_recs,
			'uri_segment'=>$offset,
			'refresh'=>1
		);
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['res'] 		= $res_array;
		if($this->input->post('btn_sbt')!=''){
			$this->form_validation->set_rules('label_id', 'Level Id', "trim|required");
			$this->form_validation->set_rules('metadata_from', 'From Date', 'trim|required');
			$this->form_validation->set_rules('metadata_to', 'To Date', 'trim|required');
			if($this->form_validation->run()===TRUE){
				$posted_data = array(
					'member_id' 	=> $this->userId,
					'label_id'		=> $this->input->post('label_id',TRUE),	
					'metadata_from'	=> $this->input->post('metadata_from',TRUE),
					'metadata_to'	=> $this->input->post('metadata_to',TRUE),
					'created_date'	=> $this->config->item('config.date.time')

				);

				$posted_data = $this->security->xss_clean($posted_data);
				$this->admin_model->safe_insert('wl_metadata',$posted_data,FALSE);
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',"Meta data has been downloaded successfully.");
				redirect('members/meta_data', '');
			}
		}
		$data['res_labels'] = get_db_multiple_row('wl_labels','label_id,channel_name'," status='1'");
		$this->load->view("members/view_meta_data_list",$data);
	}

	public function channel_add_request()
	{
		$data['heading_title'] = "Channel Add Request";
		$this->mem_top_menu_section = 'channel_request';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        = "members/channel_add_request";
		$condition 		= "req.status != '2' AND req.request_type='1' AND req.member_id='".$this->userId."'";
		$sort_by_rec 	= "req.request_id DESC";
		$param_req = array(
			'fields'=>"req.*",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'orderby'=>$sort_by_rec,
			'groupby'=>'req.request_id',
			'debug'=>FALSE
		);
		$res_array = $this->admin_model->get_channel_request($param_req);
		$total_recs = $this->admin_model->total_rec_found;
		$params_pagination = array(
			'base_link'=>$base_link,
			'per_page'=>$per_page,
			'total_recs'=>$total_recs,
			'uri_segment'=>$offset,
			'refresh'=>1
		);
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['res'] 		= $res_array;
		if($this->input->post('btn_sbt')!=''){
			$first_show = $this->input->post('num_add_rows');
		    $this->form_validation->set_rules('request_type', "Channel Request", 'trim|required');
          	for($i = 0;$i<$first_show;$i++) {
			    $this->form_validation->set_rules('urls['.$i.']', "URL", 'trim|required|valid_url');
			}
			if ($this->form_validation->run() == TRUE){
			 	$urls = $this->input->post('urls');
	            foreach ($urls as $index => $url) {
	                $posted_data = array(
	                    'member_id' 	=> $this->userId,
	                    'request_type'	=> $this->input->post('request_type',TRUE),
	                    'url'      	  	=> $url,
	                    'created_date'  => $this->config->item('config.date.time')
	                );
	                $this->admin_model->safe_insert('wl_youtube_requests', $posted_data, FALSE);
	            }
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',"Request has been saved successfully.");
				redirect('members/channel_add_request', '');
			}
		}
		$this->load->view('view_channel_request_add',$data);
	}


	public function channel_white_list_request()
	{
		$data['heading_title'] = "Channel White List Request";
		$this->mem_top_menu_section = 'channel_request';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        = "members/channel_white_list_request";
		$condition 		= "req.status != '2' AND req.request_type='2' AND req.member_id='".$this->userId."'";
		$sort_by_rec 	= "req.request_id DESC";
		$param_req = array(
			'fields'=>"req.*",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'orderby'=>$sort_by_rec,
			'groupby'=>'req.request_id',
			'debug'=>FALSE
		);
		$res_array = $this->admin_model->get_channel_request($param_req);
		$total_recs = $this->admin_model->total_rec_found;
		$params_pagination = array(
			'base_link'=>$base_link,
			'per_page'=>$per_page,
			'total_recs'=>$total_recs,
			'uri_segment'=>$offset,
			'refresh'=>1
		);
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['res'] 		= $res_array;
		if($this->input->post('btn_sbt')!=''){
			$first_show = $this->input->post('num_add_rows');
		    $this->form_validation->set_rules('request_type', "Channel Request", 'trim|required');
          	for($i = 0;$i<$first_show;$i++) {
			    $this->form_validation->set_rules('urls['.$i.']', "URL", 'trim|required|valid_url');
			}
			if ($this->form_validation->run() == TRUE){
			 	$urls = $this->input->post('urls');
	            foreach ($urls as $index => $url) {
	                $posted_data = array(
	                    'member_id' 	=> $this->userId,
	                    'request_type'	=> $this->input->post('request_type',TRUE),
	                    'url'      	  	=> $url,
	                    'created_date'  => $this->config->item('config.date.time')
	                );
	                $this->admin_model->safe_insert('wl_youtube_requests', $posted_data, FALSE);
	            }
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',"Request has been saved successfully.");
				redirect('members/channel_white_list_request', '');
			}
		}
		$this->load->view('view_channel_white_request_add',$data);
	}

    public function claim_release_request()
	{
		$data['heading_title'] = "Claim Release Request";
		$this->mem_top_menu_section = 'channel_request';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        = "members/claim_release_request";
		$condition 		= "req.status != '2' AND req.request_type='3' AND req.member_id='".$this->userId."'";
		$sort_by_rec 	= "req.request_id DESC";
		$param_req = array(
			'fields'=>"req.*",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'orderby'=>$sort_by_rec,
			'groupby'=>'req.request_id',
			'debug'=>FALSE
		);
		$res_array = $this->admin_model->get_channel_request($param_req);
		$total_recs = $this->admin_model->total_rec_found;
		$params_pagination = array(
			'base_link'=>$base_link,
			'per_page'=>$per_page,
			'total_recs'=>$total_recs,
			'uri_segment'=>$offset,
			'refresh'=>1
		);
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['res'] 		= $res_array;
		if($this->input->post('btn_sbt')!=''){
			$first_show = $this->input->post('num_add_rows');
		    $this->form_validation->set_rules('request_type', "Channel Request", 'trim|required');
          	for($i = 0;$i<$first_show;$i++) {
			    $this->form_validation->set_rules('urls['.$i.']', "URL", 'trim|required|valid_url');
			}
			if ($this->form_validation->run() == TRUE){
			 	$urls = $this->input->post('urls');
	            foreach ($urls as $index => $url) {
	                $posted_data = array(
	                    'member_id' 	=> $this->userId,
	                    'request_type'	=> $this->input->post('request_type',TRUE),
	                    'url'      	  	=> $url,
	                    'created_date'  => $this->config->item('config.date.time')
	                );
	                $this->admin_model->safe_insert('wl_youtube_requests', $posted_data, FALSE);
	            }
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',"Request has been saved successfully.");
				redirect('members/claim_release_request', '');
			}
		}
		$this->load->view('view_claim_release_request_add',$data);
	}

	public function channel_request_delete()
	{
		$request_id = $this->uri->segment(3);
		$is_exist = count_record('wl_youtube_requests'," md5(request_id)='".$request_id."' ");
		if($is_exist>0)
		{
			$where = "md5(request_id) = '".$request_id."'";
			$this->admin_model->safe_update('wl_youtube_requests',array('status'=>'2'),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Channel request has been deleted successfully.");
			redirect($this->input->server('HTTP_REFERER'));
			//redirect('members/channel_request'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect($this->input->server('HTTP_REFERER'));
			//redirect('members/channel_request'); 
		}		
	}

	public function invoice(){
		$err=1;
		$err_code="ERR400";
		$err_msg=lang('inv_request');
		$order_token_id = $this->uri->segment(4);
		$order_id = (int) $this->uri->segment(3);
		if(($order_token_id=='' && $order_id==0)){
			$err_code="ERR401";
			$err_msg=lang('inv_request');
			
		}else{
			$res_ordmaster = $this->db->get_where('wl_order',array('customers_id'=>$this->userId,'order_id'=>$order_id,'txn_site_unique_code'=>$order_token_id))->row_array();
			if(is_array($res_ordmaster) && !empty($res_ordmaster)){
				$err=0;
			}
		}
		$order_history_url = '';
		$order_history_title = '';
		if($err){
			$data['err_msg']= $err_msg;
		}else{
			$qs = query_string('');
			switch($res_ordmaster['order_type']){
				case 1:
				case 2:
					$order_history_url = 'members/orders'.$qs;
					$order_history_title = 'Order History';
					$res_ord_details = $this->db->get_where('wl_order_details',array('orders_id'=>$res_ordmaster['order_id']))->result_array();
				break;
				default:
					$res_ord_details="";
			}
			$this->load->helper('order/order');
			$params_invoice_content = array(
									'res_ordmaster'=>$res_ordmaster,
									'res_ord_details'=>$res_ord_details,
									'order_type'=>$res_ordmaster['order_type']
								);
			$data['params_invoice_content'] = $params_invoice_content;
		}
		$data['heading_title'] = "Invoice";
		$data['err']= $err;
		$data['order_history_url']= $order_history_url;
		$data['order_history_title']=$order_history_title;
		$this->load->view('view_invoice',$data);
	}

	public function validate_mobile_otp($val){
	  $mobile_number = $this->input->post('mobile_number',TRUE);
	  if($mobile_number!=''){
			if($val!=''){
				$this->load->library('otp');
				$param_otp = array('user_id'=>$this->userId,'otp'=>$val,'otp_type'=>'update_mobile','temp_value'=>$mobile_number);
				$otp_res = $this->otp->verify_otp($param_otp);
				if($otp_res['err']){
					$this->form_validation->set_message('validate_mobile_otp', $otp_res['err_msg']);
					return FALSE;
				}
			}
			return TRUE;
	  }else{
			$this->form_validation->set_message('validate_mobile_otp', "Please specify mobile number");
			return FALSE;
	  }
	}

	public function validate_email_otp($val){
	   $user_name = $this->input->post('user_name',TRUE);
	  if($user_name!=''){
			if($val!=''){
				$this->load->library('otp');
				$param_otp = array('user_id'=>$this->userId,'otp'=>$val,'otp_type'=>'update_email');
				$otp_res = $this->otp->verify_otp($param_otp);
				if($otp_res['err']){
					$this->form_validation->set_message('validate_email_otp', $otp_res['err_msg']);
					return FALSE;
				}
			}
			return TRUE;
	  }else{
			$this->form_validation->set_message('validate_email_otp', "Please specify Email ID");
			return FALSE;
	  }
	}

}