<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class Admin extends Private_Admin_Controller
{
	private $mId;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('admin/admin_model','order/order_model','events/events_model'));
		$this->load->library(array('safe_encrypt', 'Dmailer'));
		$this->form_validation->set_error_delimiters("<div class='required'>","</div>");
	}

	public function index()
	{
		$this->myaccount();
	}

	public function myaccount(){
		$data['heading_title'] = "Dashboard";
		$this->mem_top_menu_section = 'dashboard';
		$offset =0;

		$member_id = $this->session->userdata('user_id');
		$member_type = $this->session->userdata('member_type');
		// echo "<pre>";
		// print_r($member_id);
		// die();
        /*
		$conditions = '';
		if($member_type=='2' && $member_id>0){
			$conditions .= "member_id = '{$member_id}' ";
	    }
	    
		$data['total_music_releases'] = count_record('wl_signed_albums'," status!='2' AND is_verify_meta='0' AND is_pdl_submit='0' ".$conditions);
		$data['total_music_process_releases'] = count_record('wl_signed_albums'," status!='2' AND is_verify_meta='1' AND is_pdl_submit='0' ".$conditions);
		$data['total_music_final_releases'] = count_record('wl_signed_albums'," status!='2' AND is_verify_meta='1' AND is_pdl_submit='1' ".$conditions);
        */
        $conditions = [];
		if ($member_type == '2' && $member_id > 0) {
		    $conditions[] = "(cus.parent_id = '{$member_id}' OR wr.member_id = '{$member_id}')";
		}
		$where = function($extra = '') use ($conditions) {
		    $base = $conditions ? implode(" AND ", $conditions) : '';
		    return $base . ($base && $extra ? " AND $extra" : $extra);
		};
		$data['total_music_releases'] = count_record_with_join(
		    'wl_releases as wr',
		    "LEFT JOIN wl_signed_albums as wsa 
		        ON wsa.release_ref_id = wr.release_id 
		     LEFT JOIN wl_customers as cus 
		        ON cus.customers_id = wr.member_id 
		        AND cus.status='1'",
		    $where("wr.status='0'")
		);
		// echo_sql();
		$data['total_music_process_releases'] = count_record_with_join(
		    'wl_releases as wr',
		    "LEFT JOIN wl_signed_albums as wsa 
		        ON wsa.release_ref_id = wr.release_id 
		     LEFT JOIN wl_customers as cus 
		        ON cus.customers_id = wr.member_id 
		        AND cus.status='1'",
		    $where("wr.status IN (5,6)")
		);
		$data['total_music_final_releases'] = count_record_with_join(
		    'wl_releases as wr',
		    "LEFT JOIN wl_signed_albums as wsa 
		        ON wsa.release_ref_id = wr.release_id 
		     LEFT JOIN wl_customers as cus 
		        ON cus.customers_id = wr.member_id 
		        AND cus.status='1'",
		    $where("wr.status='1' AND wsa.is_verify_meta='1' AND wsa.is_pdl_submit='1'")
		);

		$data['total_music_rejected_releases'] = count_record_with_join(
		    'wl_releases as wr',
		    "LEFT JOIN wl_signed_albums as wsa 
		        ON wsa.release_ref_id = wr.release_id 
		     LEFT JOIN wl_customers as cus 
		        ON cus.customers_id = wr.member_id 
		        AND cus.status='1'",
		    $where("wr.status='3'")
		);

		$data['total_music_takedown_releases'] = count_record_with_join(
		    'wl_releases as wr',
		    "LEFT JOIN wl_signed_albums as wsa 
		        ON wsa.release_ref_id = wr.release_id 
		     LEFT JOIN wl_customers as cus 
		        ON cus.customers_id = wr.member_id 
		        AND cus.status='1'",
		    $where("wr.status='4' AND wsa.is_verify_meta='1' AND wsa.is_pdl_submit='1'")
		);
		
		// $data['total_music_releases'] = count_record('wl_releases'," status IN (0,5) AND album_type='1'");
		$data['total_video_releases'] = count_record('wl_releases'," status IN (0,5) AND album_type='2'");
		// $data['total_music_process_releases'] = count_record('wl_releases'," status='5' AND album_type='1'");
		$data['total_video_process_releases'] = count_record('wl_releases'," status='5' AND album_type='2'");
		//$data['total_music_final_releases'] = count_record('wl_releases'," status='1' AND album_type='1'");
		$data['total_video_final_releases'] = count_record('wl_releases'," status='1' AND album_type='2'");
		// $data['total_music_rejected_releases'] = count_record('wl_releases'," status='3' AND album_type='1'");
		$data['total_video_rejected_releases'] = count_record('wl_releases'," status='3' AND album_type='2'");
		// $data['total_music_takedown_releases'] = count_record('wl_releases'," status='4' AND album_type='1'");
		$data['total_video_takedown_releases'] = count_record('wl_releases'," status='4' AND album_type='2'");

		$condition1 = '';
		if($member_type=='2'){
			if($member_id>0){
				$condition1 = " AND parent_id = '".$member_id."' ";
			}
	    }
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
		$data['recent_users'] = get_db_multiple_row('wl_customers',"first_name,profile_photo"," member_type='3' AND status!='2' ".$condition1." ORDER BY customers_id DESC limit 10");
		$data['total_earning'] 	= 0;
		$data['total_dabit'] 	= 0;
		$data['balance_amt'] 	= 0;
		$data['commission_amt'] = 0;
		$data['offset'] = $offset;
		$data['x_dsg_page'] = 'home';
		$this->load->view('dashboard',$data);
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
				redirect('admin/change_password','');
			}
		}
		$this->mem_top_menu_section = 'change_password';
		$data['heading_title'] = "Change Password";
		$this->load->view('edit_account_password',$data);
	}

	public function sub_admins()
	{
		is_access_method($permission_type=1,$sec_id='3');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = "Sub User Manage";
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/sub_admins";
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$status 		= $this->db->escape_str($this->input->get_post('status',TRUE));
		$condition 	     ="m.status != '2' AND m.member_type='2'";
		if($keyword!='')
		{
			$condition.=" AND  ( m.first_name like '%$keyword%' OR m.last_name like '%$keyword%' OR m.mobile_number like '%$keyword%' OR m.user_name like '%$keyword%') ";
		}

		if($status!='')
		{
			$condition.=" AND  m.status='".$status."' ";
		}
		$sort_by_rec 	 ="m.customers_id DESC";
		$paramc_emp = array(
					'fields'=>"m.*,CONCAT_WS(' ',m.first_name,m.last_name) AS name",
					'offset'=>$db_offset,
					'limit'=>$per_page,
					'where'=>$condition,
					'orderby'=>$sort_by_rec,
					'groupby'=>'m.customers_id',
					'debug'=>FALSE
				);
		$res_array      = $this->admin_model->get_members($paramc_emp);
		$total_recs 	= $this->admin_model->total_rec_found;

		$params_pagination = array(
			'data_form'=>'#search_form',
			'base_link'=>$base_link,
			'per_page'=>$per_page,
			'total_recs'=>$total_recs,
			'uri_segment'=>$offset,
			'refresh'=>1
		);
		
		if($this->input->post('Send',TRUE)=='Send Notification')
		{
			$this->send_notification();	
			//trace($this->input->post());exit;
			$this->session->set_flashdata('success',"Mail has been send successfully.");
			redirect('admin/sub_admins'); 
		}
		
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['res'] 		= $res_array;

		$this->load->view("admin/view_sub_admin_list",$data);
	}

	public function create_sub_admin()
	{
		is_access_method($permission_type=2,$sec_id='3');
		$this->mem_top_menu_section = 'sub_admins';
		$img_allow_size =  $this->config->item('allow.file.size');
		$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
		$data['heading_title'] = 'Add Sub User';
		if($this->input->post('action')!=''){
			$this->form_validation->set_rules('first_name', 'Name', 'trim|required|alpha|max_length[50]');
			$this->form_validation->set_rules('mobile_number', 'Mobile', 'trim|required|numeric|min_length[10]|max_length[15]');
			$this->form_validation->set_rules('user_name', 'Email ID','trim|required|valid_email|max_length[80]|callback_email_check');
			$this->form_validation->set_rules('commission', 'Commission','trim|required|greater_than_equal_to[0]');
			$this->form_validation->set_rules('country', 'Country', 'trim|required');
			$this->form_validation->set_rules('state', 'State', 'trim|required');
			$this->form_validation->set_rules('city', 'City', 'trim|required');
			$this->form_validation->set_rules('pin_code', 'Pin Code', 'trim|required|max_length[6]');
			$this->form_validation->set_rules('profile_photo','Profile Photo',"file_required|file_allowed_type[image]|file_size_max[$img_allow_size]");
			$this->form_validation->set_rules('address', 'Address', 'trim|required|max_length[200]');
			$this->form_validation->set_rules('aadhar_doc','Aadhar Card',"file_required|file_allowed_type[pdf]|file_size_max[$img_allow_size]");
			$this->form_validation->set_rules('agreement_doc','User Agreement',"file_required|file_allowed_type[pdf]|file_size_max[$img_allow_size]");
			if ($this->form_validation->run() == TRUE)
			{
				$profile_photo = "";
				if( $_FILES['profile_photo']['name']!='' ){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('profile_photo','profiles');
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
						$profile_photo = $uploaded_data['upload_data']['file_name'];
					}
				}
				$aadhar_doc = "";
				if( !empty($_FILES['aadhar_doc']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('aadhar_doc','members');
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
						$aadhar_doc = $uploaded_data['upload_data']['file_name'];
					}
				}
				$agreement_doc = "";
				if( !empty($_FILES['agreement_doc']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('agreement_doc','members');
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
						$agreement_doc = $uploaded_data['upload_data']['file_name'];
					}
				}
				$user_name = $this->input->post('user_name',TRUE);
				$actkey = md5($user_name."-".random_string('numeric',4));
				$password = 'sub@123';
				$encoded_password  =  $this->safe_encrypt->encode($password);
				$country_id = $this->input->post('country',TRUE);
				$state_id = $this->input->post('state',TRUE);
				/*
				$city_id = $this->input->post('city');

				$city_res = $city_id > 0  ? log_fetched_rec($city_id,'city','title') : '';
				$city_name = !empty($city_res['rec_data']) ? $city_res['rec_data']['title'] : '';
				*/
				$state_res = $state_id > 0  ? log_fetched_rec($state_id,'state','title') : '';
				$state_name = !empty($state_res['rec_data']) ? $state_res['rec_data']['title'] : '';
				
				$country_res = $country_id > 0  ? log_fetched_rec($country_id,'country','country_name') : '';
				$country_name = !empty($country_res['rec_data']) ? $country_res['rec_data']['country_name'] : '';
				$sponsorId = generate_sponsorId();
				$posted_data = array(					
					'actkey'    	=> $actkey,
					'parent_id' 	=> $this->userId,
					'member_type'	=> '2', 
					'mem_nature'	=> '0', 
					'sponsor_id'	=> $sponsorId,
					'user_name'		=> $user_name,
					'password'		=> $encoded_password,
					"first_name"	=> $this->input->post('first_name',TRUE), 
					"mobile_number"	=> $this->input->post('mobile_number',TRUE),
					'profile_photo'	=> $profile_photo,
					'aadhar_doc'	=> $aadhar_doc,
					'agreement_doc'	=> $agreement_doc,
					'address' 		=> $this->input->post('address',TRUE),
					'pin_code'		=> $this->input->post('pin_code',TRUE),
					'country'		=> $country_id,
					'country_name'	=> $country_name,
					'state'	        => $state_id,
					'state_name'	=> $state_name,	
					'city_name'		=> $this->input->post('city',TRUE),
					'commission'	=> $this->input->post('commission',TRUE),
					'ip_address'  	=> $this->input->ip_address(),
					'added_by'  	=> '1',
					'is_verified'	=> '1',
					'account_created_date'=> $this->config->item('config.date.time')
				);  
				
				$posted_data = $this->security->xss_clean($posted_data); 
				$registerId = $this->admin_model->safe_insert('wl_customers',$posted_data);
				if($registerId > 0)
				{
					$first_name  = $this->input->post('first_name',TRUE);
					$last_name   = '';
					/* Send  mail to user */
					//$content    =  get_content('wl_auto_respond_mails','1');
					$content    =  get_content('1','wl_auto_respond_mails');
					$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
					$body       =  $content->email_content;
					$verify_url = "<a href=".base_url()."user/verify/".$actkey.">Click here </a>";
					$name 		= ucwords($first_name)." ".ucwords($last_name);
					$body		=	str_replace('{mem_name}',$name,$body);
					$body		=	str_replace('{username}',$user_name,$body);
					$body		=	str_replace('{password}',$password,$body);
					$body		=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
					$body		=	str_replace('{site_name}',$this->config->item('site_name'),$body);
					$body		=	str_replace('{url}',base_url(),$body);
					$body		=	str_replace('{verification_link}',$verify_url,$body);
					$mail_conf =  array(
						'subject'=>$subject,
						'to_email'=>$user_name,
						'from_email'=>$this->admin_info->admin_email,
						'from_name'=> $this->config->item('site_name'),
						'body_part'=>$body
					);
					
					$this->dmailer->mail_notify($mail_conf);
					/* End send  mail to user */
					$content    =  get_content('6','wl_auto_respond_mails');			
					$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
					$body       =  $content->email_content;
					$name 			= ucwords($first_name)." ".ucwords($last_name);
					$body			=	str_replace('{name}',$name,$body);
					$body			=	str_replace('{username}',$user_name,$body);
					$body			=	str_replace('{password}',$password,$body);
					$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
					$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
					$body			=	str_replace('{url}',base_url(),$body);

					$mail_conf =  array(
						'subject'=>$subject,
						'to_email'=>$this->admin_info->admin_email,
						'from_email'=>$this->input->post('user_name'),
						'from_name'=> $this->config->item('site_name'),
						'body_part'=>$body
					);
					$this->dmailer->mail_notify($mail_conf);
				}
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success', 'You have added sub user successfully.');
				redirect('admin/sub_admins', '');
			}
		}
		$this->load->view('admin/view_sub_admin_add',$data);
	}

	public function edit_sub_admin()
	{
		// is_access_method($permission_type=3,$sec_id='3');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'Edit Sub User';
		$customer_id = $this->uri->segment(3);
		$img_allow_size =  $this->config->item('allow.file.size');
		$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
		$res_subadmin = get_db_single_row('wl_customers','customers_id'," AND md5(customers_id)='".$customer_id."' ");
		if(is_array($res_subadmin) && !empty($res_subadmin))
		{
			$data['mres'] = $mres = $this->admin_model->get_member_row($res_subadmin['customers_id']);
			if($this->input->post('action')!=''){
				$this->form_validation->set_rules('first_name', 'Name', 'trim|required|alpha|max_length[50]');
				$this->form_validation->set_rules('mobile_number', 'Mobile','trim|required|numeric|min_length[10]|max_length[15]');
				$this->form_validation->set_rules('country','Country', 'trim|required');
				$this->form_validation->set_rules('state', 'State', 'trim|required');
				$this->form_validation->set_rules('city', 'City', 'trim|required');
				$this->form_validation->set_rules('pin_code', 'Pin Code', 'trim|required|max_length[6]');
				$this->form_validation->set_rules('profile_photo','Profile Photo',"file_allowed_type[image]|file_size_max[$img_allow_size]|check_dimension[$img_allow_dim]");
				$this->form_validation->set_rules('address', 'Address', 'trim|required|max_length[200]');
				$this->form_validation->set_rules('aadhar_doc','Aadhar Card',"file_allowed_type[pdf]|file_size_max[$img_allow_size]");
				$this->form_validation->set_rules('agreement_doc','User Agreement',"file_allowed_type[pdf]|file_size_max[$img_allow_size]");
				if ($this->form_validation->run() == TRUE)
				{	
					$profile_photo = $mres['profile_photo'];
					$unlink_profile_photo = array('source_dir'=>"profiles",'source_file'=>$profile_photo);
					if($this->input->post('aadhar_doc_delete')==='Y'){
						removeImage($unlink_profile_photo);
						$profile_photo = NULL;
					}
					if( !empty($_FILES) && $_FILES['profile_photo']['name']!='' ){
						$this->load->library('upload');
						$uploaded_data =  $this->upload->my_upload('profile_photo','profiles');
						if( is_array($uploaded_data) && !$uploaded_data['err'] ){
							$profile_photo = $uploaded_data['upload_data']['file_name'];
							removeImage($unlink_profile_photo);
						}
					}
					$aadhar_doc = $mres['aadhar_doc'];
					$unlink_aadhar_doc = array('source_dir'=>"members",'source_file'=>$aadhar_doc);
					if($this->input->post('aadhar_doc_delete')==='Y'){
						removeImage($unlink_aadhar_doc);
						$aadhar_doc = NULL;
					}
					if( !empty($_FILES) && $_FILES['aadhar_doc']['name']!='' ){
						$this->load->library('upload');
						$uploaded_data =  $this->upload->my_upload('aadhar_doc','members');
						if( is_array($uploaded_data) && !$uploaded_data['err'] ){
							$aadhar_doc = $uploaded_data['upload_data']['file_name'];
							removeImage($unlink_aadhar_doc);
						}
					}
					$agreement_doc = $mres['agreement_doc'];
					$unlink_agreement_doc = array('source_dir'=>"members",'source_file'=>$agreement_doc);
					if($this->input->post('agreement_doc_delete')==='Y'){
						removeImage($unlink_agreement_doc);
						$agreement_doc = NULL;
					}
					if( !empty($_FILES) && $_FILES['agreement_doc']['name']!='' ){
						$this->load->library('upload');
						$uploaded_data =  $this->upload->my_upload('agreement_doc','members');
						if( is_array($uploaded_data) && !$uploaded_data['err'] ){
							$agreement_doc = $uploaded_data['upload_data']['file_name'];
							removeImage($unlink_agreement_doc);
						}
					}	
					$country_id = $this->input->post('country',TRUE);
					$state_id = $this->input->post('state',TRUE);
					/*
					
					$city_id = $this->input->post('city');

					$city_res = $city_id > 0  ? log_fetched_rec($city_id,'city','title') : '';
					$city_name = !empty($city_res['rec_data']) ? $city_res['rec_data']['title'] : '';
					*/
					$state_res = $state_id > 0  ? log_fetched_rec($state_id,'state','title') : '';
					$state_name = !empty($state_res['rec_data']) ? $state_res['rec_data']['title'] : '';
					
					$country_res = $country_id > 0  ? log_fetched_rec($country_id,'country','country_name') : '';
					$country_name = !empty($country_res['rec_data']) ? $country_res['rec_data']['country_name'] : '';		
					$updated_data = array(
						"first_name"	=> $this->input->post('first_name',TRUE), 
						"mobile_number"	=> $this->input->post('mobile_number',TRUE),
						'profile_photo'	=> $profile_photo,
						'aadhar_doc'	=> $aadhar_doc,
						'agreement_doc'	=> $agreement_doc,
						'address' 		=> $this->input->post('address',TRUE),
    					'pin_code'		=> $this->input->post('pin_code',TRUE),
    					'country'		=> $country_id,
    					'country_name'	=> $country_name,
    					'state'	        => $state_id,
    					'state_name'	=> $state_name,	
    					'city_name'		=> $this->input->post('city',TRUE)
					);  
					
					$updated_data = $this->security->xss_clean($updated_data); 
					$where_subadmin = " md5(customers_id)='".$customer_id."' ";
					$this->admin_model->safe_update('wl_customers',$updated_data,$where_subadmin,FALSE);
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Details has been updated successfully.');
					redirect('admin/edit_bank_info/'.$customer_id, '');
				}
			}

			$this->load->view('admin/view_sub_admin_edit',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/sub_admins'); 
		}
	}

	public function edit_bank_info()
	{
		//is_access_method($permission_type=3,$sec_id='3');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'Edit Sub User';
		$customer_id = $this->uri->segment(3);
		$img_allow_size =  $this->config->item('allow.file.size');
		$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
		$res_subadmin = get_db_single_row('wl_customers','customers_id'," AND md5(customers_id)='".$customer_id."' ");
		if(is_array($res_subadmin) && !empty($res_subadmin))
		{
			$data['mres'] = $mres = $this->admin_model->get_member_row($res_subadmin['customers_id']);
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
					$where_subadmin = " md5(customers_id)='".$customer_id."' ";
					$this->admin_model->safe_update('wl_customers',$updated_data,$where_subadmin,FALSE);
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Details has been updated successfully.');
					redirect('admin/edit_user_rate/'.$customer_id, '');
				}
			}

			$this->load->view('admin/view_sub_admin_bank_info_edit',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/sub_admins'); 
		}
	}

	public function edit_user_rate()
	{
		//is_access_method($permission_type=3,$sec_id='3');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'Edit Sub User';
		$customer_id = $this->uri->segment(3);
		$res_subadmin = get_db_single_row('wl_customers','customers_id'," AND md5(customers_id)='".$customer_id."' ");
		if(is_array($res_subadmin) && !empty($res_subadmin))
		{
			$data['mres'] = $mres = $this->admin_model->get_member_row($res_subadmin['customers_id']);
			if($this->input->post('action')!=''){

				$this->form_validation->set_rules('commission', 'Commission','trim|required|greater_than_equal_to[0]');
				if ($this->form_validation->run() == TRUE)
				{	
					
					$updated_data = array(
						"commission" => $this->input->post('commission',TRUE),
					);  
					
					$updated_data = $this->security->xss_clean($updated_data); 
					$where_subadmin = " md5(customers_id)='".$customer_id."' ";
					$this->admin_model->safe_update('wl_customers',$updated_data,$where_subadmin,FALSE);
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Details has been updated successfully.');
					redirect('admin/view_profile/'.$customer_id, '');
				}
			}

			$this->load->view('admin/view_sub_admin_commission_edit',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/sub_admins'); 
		}
	}

	public function view_profile()
	{
		//is_access_method($permission_type=3,$sec_id='3');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'View User';
		$customer_id = $this->uri->segment(3);
		$img_allow_size =  $this->config->item('allow.file.size');
		$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
		$res_subadmin = get_db_single_row('wl_customers','customers_id'," AND md5(customers_id)='".$customer_id."' ");
		if(is_array($res_subadmin) && !empty($res_subadmin))
		{
			$data['mres'] = $this->admin_model->get_member_row($res_subadmin['customers_id']);
			$this->load->view('admin/view_sub_admin_profile',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/sub_admins'); 
		}
	}

	public function delete_sub_admin()
	{
		$customer_id = $this->uri->segment(3);
		$is_exist = count_record('wl_customers'," md5(customers_id)='".$customer_id."' AND parent_id='".$this->userId."' ");
		if($is_exist>0)
		{
			$this->db->query("UPDATE wl_customers SET status='2' WHERE md5(customers_id)='".$customer_id."' AND parent_id='".$this->userId."' ");
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Sub Admin has been Deleted successfully.");
			redirect('admin/sub_admins'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/sub_admins'); 
		}		
	}

	public function status_sub_admin()
	{
		$customer_id = $this->uri->segment(3);
		$status = $this->input->get_post('u_status');
		$is_exist = count_record('wl_customers'," md5(customers_id)='".$customer_id."' ");
		if($is_exist>0)
		{
			if($status=='active')
			{
				$sts = '1';
				$stsmsg = 'activated';
			}
			elseif($status=='deactive')
			{
				$sts = '0';
				$stsmsg = 'deactivated';
			}
			$where = "md5(customers_id) = '".$customer_id."'";
			$this->admin_model->safe_update('wl_customers',array('status'=>$sts),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"User has been $stsmsg successfully.");
			redirect('admin/sub_admins'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/sub_admins'); 
		}
	}

	public function permission()
	{
		//is_access_method($permission_type=3,$sec_id='3');
		$this->mem_top_menu_section = 'sub_admins';
		$data['page_heading'] = 'View Permission';
		$customer_id = $this->uri->segment(3);
		$res_subadmin = get_db_single_row('wl_customers','customers_id,sponsor_id,first_name'," AND md5(customers_id)='".$customer_id."' ");
		if(is_array($res_subadmin) && !empty($res_subadmin))
		{	
			$db_saved_data = [];
			$allocated_section_params = array(
				'fields'=>"tals.*,tas.section_title",
				'where'=>"tals.subadmin_id ='".$res_subadmin['customers_id']."' AND status='1'",
				'exjoin'=>array(
					array('tbl'=>'wl_customer_sections as tas','condition'=>"tals.sec_id=tas.id",'type'=>'LEFT')
				),
				'orderby'=>"tas.disp_order ASC",
				'debug'=>FALSE
				);
			$allocated_secid_arr = $this->admin_model->get_allocated_sections($allocated_section_params);
			$data['mres'] = $res_subadmin;
			$data['db_saved_data'] = $allocated_secid_arr;
			$this->load->view('admin/view_sub_admin_profile_permission',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/sub_admins'); 
		}
	}

	public function permission_add()
	{
		//is_access_method($permission_type=3,$sec_id='3');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'Add Roles';
		$customer_id = $this->uri->segment(3);
		$res_subadmin = get_db_single_row('wl_customers','customers_id'," AND md5(customers_id)='".$customer_id."' ");
		if(is_array($res_subadmin) && !empty($res_subadmin))
		{
			$subadmin_id = $res_subadmin['customers_id'];
			$section_res = $this->db->query("SELECT * FROM wl_customer_sections WHERE status='1' AND parent_id='0' ORDER BY disp_order ")->result_array();
			$db_saved_data = [];
			$allocated_section_params = array(
				'fields'=>"tals.*,tas.section_controller",
				'where'=>"tals.subadmin_id ='".$subadmin_id."'",
				'exjoin'=>array(
					array('tbl'=>'wl_customer_sections as tas','condition'=>"tals.sec_id=tas.id",'type'=>'LEFT')
				),
				'orderby'=>"tas.disp_order ASC",
				'debug'=>FALSE
				);
			$allocated_secid_arr = $this->admin_model->get_allocated_sections($allocated_section_params);
			if (is_array($allocated_secid_arr) && !empty($allocated_secid_arr)) {
			    foreach ($allocated_secid_arr as $val1) {
			        $controller_identifier = $val1['section_controller'];
			        if (!isset($db_saved_data[$controller_identifier])) {
			            $db_saved_data[$controller_identifier] = [];
			        }
			        foreach (explode(',', $val1['permission']) as $val2) {
			            $db_val = $val1['sec_id']."_".$val1['sec_parent_id']."_".$val2;
			            $db_saved_data[$controller_identifier][] = $db_val;
			        }
			    }
			}

			//trace($db_saved_data);
			$data['db_saved_data'] = $db_saved_data;
			if($this->input->post('action')!=''){
				$this->form_validation->set_rules('user_permission', 'Permission', 'trim|required');
				if($this->form_validation->run()==TRUE)
				{
					$this->admin_model->safe_delete('wl_customer_allowed_sections',array('subadmin_id'=>$subadmin_id));
					$insert_data = array();
					$posted_data =$this->input->post();
					foreach($posted_data as $key1=>$val1)
					{
						if(is_array($val1) && !empty($val1))
						{
							foreach($val1 as $key2=>$val2)
							{
								$dtl_arr = explode('_',$val2);
								$section_id = $dtl_arr[0];
								$ref_id = $dtl_arr[1];
								$insert_data[$section_id][$ref_id][] = $dtl_arr[2];
							}
						}
					}
					if(!empty($insert_data))
					{
						foreach($insert_data as $key1=>$val1)
						{
							foreach($val1 as $key2=>$val2)
							{  
								$permission = array_unique($val2);
								$data1 = array(
									'subadmin_id'	=>$subadmin_id,
								  	'sec_parent_id'	=>$key2,
								  	'sec_id'		=>$key1,
								  	'permission'	=>implode(',',$permission)
								  );		
								$this->admin_model->safe_insert("wl_customer_allowed_sections",$data1,FALSE);
							}
						}
					}
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success','Permissions have added successfully.');
					redirect('admin/sub_admins', '');
				}
			}
			$data['section_res'] = $section_res;
			$this->load->view('admin/view_sub_admin_permission_add',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/sub_admins'); 
		}
	}
	
	public function labels()
	{
		is_access_method($permission_type=1,$sec_id='3');
		$this->mem_top_menu_section = 'labels';
		$data['heading_title'] = "Manage Labels";
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/labels";
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		
		$condition 		= "wl.status != '2'";
		if ($this->mres['member_type'] == '2' && $this->userId > 0) {
            $condition .= " AND (cus.parent_id = '{$this->userId}' OR wl.member_id = '{$this->userId}') ";
        } 
        if ($this->mres['member_type'] == '3' && $this->userId > 0) {
            $condition .= " AND wl.member_id = '{$this->userId}' ";
        }
		
		if($keyword!='')
		{
			$condition.=" AND  ( wl.channel_name like '%$keyword%' OR cus.first_name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wl.label_id DESC";
		$param_label = array(
			'fields'=>"wl.*,cus.first_name",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wl.member_id AND cus.status='1'",'type'=>'LEFT')
			),
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
		if($this->input->post('btn_sbt')!=''){
			is_access_method($permission_type=5,$sec_id='3');
			$custom_error_flds = array();
			$label_id = $this->input->post('label_id',TRUE);
			$posted_status = $this->input->post('status',TRUE);
			$this->form_validation->set_rules('label_id', 'Level Id', "trim|required");
			$this->form_validation->set_rules('status', 'Status', "trim|required");
			if($posted_status=='3'){
				$this->form_validation->set_rules('reason', 'Reason', "trim|required|max_length[120]");
			}
			$form_validation = $this->form_validation->run();
			if($form_validation===TRUE && empty($custom_error_flds)){
				$posted_data = array(
					'status'=> $posted_status,
					'reason'=> $this->input->post('reason',TRUE)	
				);

				$posted_data = $this->security->xss_clean($posted_data);
				$where       = "label_id = '".$label_id."'";
				$this->admin_model->safe_update('wl_labels',$posted_data,$where,FALSE);
				$ret_data = array('status'=>'1','msg'=>'Updating...','label_id'=>$label_id);
			}else{
				$error_array = req_compose_errors($custom_error_flds);
				$ret_data = array('status'=>'0','error_flds'=>$error_array);
			}
			echo json_encode($ret_data);
			die;
		}
		$this->load->view("admin/view_labels_list",$data);
	}

	public function add_label()
	{
		is_access_method($permission_type=2,$sec_id='3');
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
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
						$government_doc = $uploaded_data['upload_data']['file_name'];
					}
				}
				$agreement_doc = "";
				if( !empty($_FILES['agreement_doc']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('agreement_doc','labels');
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
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
				redirect('admin/labels', '');
			}
		}
		$this->load->view('admin/view_label_add',$data);
	}

	public function edit_label()
	{
		is_access_method($permission_type=3,$sec_id='3');
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
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
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
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
						$agreement_doc = $uploaded_data['upload_data']['file_name'];
						removeImage($unlink_agreement_doc);
					}
				}		
				$posted_data = array(
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
				redirect('admin/labels/'.query_string(), '');
			}
			$data['res'] = $res;
			$this->load->view('admin/view_label_edit',$data);
			
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'warning'));
			$this->session->set_flashdata('warning',"Label Id is not exist.");
			redirect('admin/labels', '');
		}
	}

	public function label_delete()
	{
		is_access_method($permission_type=4,$sec_id='3');
		$labelId = $this->uri->segment(3);
		$is_exist = count_record('wl_labels'," md5(label_id)='".$labelId."' ");
		if($is_exist>0)
		{
			$where = "md5(label_id) = '".$labelId."'";
			$this->admin_model->safe_update('wl_labels',array('status'=>'2'),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Label has been Deleted successfully.");
			redirect('admin/labels'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/labels'); 
		}		
	}

	public function meta_data()
	{
		is_access_method($permission_type=1,$sec_id='4');
		$this->mem_top_menu_section = 'meta_data';
		$data['heading_title'] = "Meta Data";
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/meta_data";
		$condition 	="md.status != '2' ";
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
			is_access_method($permission_type=2,$sec_id='4');
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
				redirect('admin/meta_data', '');
			}
		}
		$label_cond = "wl.status='1'" . ($this->userId > 0 && $this->mres['member_type'] == '2' ? " AND (cus.parent_id = '{$this->userId}' OR wl.member_id = '{$this->userId}')" : '') 
            . ($this->userId > 0 && $this->mres['member_type'] == '3' ? " AND wl.member_id = '{$this->userId}'" : '');
        $param_label = [
            'where' => $label_cond,
            'exjoin' => [['tbl' => 'wl_customers as cus', 'condition' => "cus.customers_id=wl.member_id AND cus.status='1'", 'type' => 'LEFT']],
            'groupby' => 'wl.label_id',
            'debug' => FALSE
        ];
        $data['res_labels'] = $this->admin_model->get_labels($param_label);
		$this->load->view("admin/view_meta_data_list",$data);
	}
	
	public function meta_delete()
	{
		is_access_method($permission_type=4,$sec_id='3');
		$mId = $this->uri->segment(3);
		$is_exist = count_record('wl_metadata'," md5(md_id)='".$mId."' ");
		if($is_exist>0)
		{
			$where = "md5(md_id) = '".$mId."'";
			$this->admin_model->safe_update('wl_metadata',array('status'=>'2'),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Record has been Deleted successfully.");
			redirect('admin/meta_data'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/meta_data'); 
		}		
	}

	public function export_signed_albums() {
    	
    	$mId = $this->uri->segment(3);
    	$param_md = array(
			'fields'=>"md.metadata_from,md.metadata_to,wl.channel_name",
			'where'=>"md5(md_id)='{$mId}'",
			'exjoin'=>array(
				array('tbl'=>'wl_labels as wl','condition'=>"wl.label_id=md.label_id AND wl.status='1'")
			),
			'groupby'=>'md.md_id',
			'return_type'=>'row_array',
			'num_rows_required'=>FALSE,
			'debug'=>FALSE
		);
		$res_metadata = $this->admin_model->get_metadata($param_md);
		if(is_array($res_metadata) && !empty($res_metadata))
		{
			$metadata_from= $res_metadata['metadata_from'];
			$metadata_to = $res_metadata['metadata_to'];
			$channel_name = $res_metadata['channel_name'];
			$qry = $this->db->select('id,isrc,album_name,song_name,artist_name,genre,language,mood,
                image_signed_url,go_live_date,created_date,modified_date,
                  status,platforms_to_release')
		        ->from('wl_signed_albums')
		        ->where('label', $channel_name)
		        ->where("created_date BETWEEN '$metadata_from' AND '$metadata_to'")
		        ->order_by('created_date', 'DESC')
		        ->get();
			$res = $qry->result_array();
			if ($qry->num_rows() > 0) {
				$res = $qry->result_array();
				$spreadsheet = new Spreadsheet();
				$spreadsheet->getProperties()->setTitle('Signed Albums')->setDescription('Signed Albums Data');
				$sheet = $spreadsheet->getActiveSheet();
				
				foreach (range('A', 'P') as $column) {
					$sheet->getColumnDimension($column)->setAutoSize(true);
				}
				
				$headers = [
					'ID', 'ISRC', 'Album Name', 'Song Name', 'Artist Name', 
					'Genre', 'Language', 'Mood', 'Media Original Path', 'Go Live Date', 
					'Created Date', 'Modified Date', 'Status', 'Platforms to Release'
				];
				
				$column = 'A';
				$row = 1;
				foreach ($headers as $header) {
					$sheet->setCellValue($column . $row, ucwords(str_replace("_", " ", $header)));
					$column++;
				}
				
				$headerRange = 'A1:P1';
				$sheet->getStyle($headerRange)->getFont()->setBold(true);
				$sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
				$sheet->getStyle($headerRange)->getFill()->getStartColor()->setRGB('D3D3D3');
				
				$sheet->freezePane('A2');
				
				$row = 2;
				foreach ($res as $data) {
					$column = 'A';
					$currentRow = $row;
					
					foreach ($data as $field => $value) {
						$cellCoordinate = $column . $currentRow;
						
						$cellValue = $value ?? '';
						
						if ($field === 'media_orignal_path' && !empty($cellValue)) {
							$cellValue = $this->formatMediaPath($cellValue, $spreadsheet, $cellCoordinate);
						}
						
						if (in_array($column, ['J', 'K', 'L']) && !empty($cellValue)) {
							try {
								$cellValue = date('Y-m-d', strtotime($cellValue));
							} catch (Exception $e) {
								
							}
						}
						
						if ($column == 'M' && !is_null($cellValue) && $cellValue !== '') {
							$cellValue = ($cellValue == '1') ? 'Active' : (($cellValue == '0') ? 'Inactive' : 'Active');
						}
						
						if (!empty($cellValue) && is_string($cellValue)) {
							$cellValue = html_entity_decode($cellValue);
						}
						
						$sheet->setCellValue($cellCoordinate, $cellValue);
						
						if ($field === 'media_orignal_path' && !empty($value)) {
							$sheet->getStyle($cellCoordinate)->getFont()->setUnderline(true);
							$sheet->getStyle($cellCoordinate)->getFont()->getColor()->setARGB('FF0000FF'); // Blue color
						}
						
						$column++;
					}
					$row++;
				}
				
				$from_date = date('dMy', strtotime($metadata_from));
				$to_date = date('dMy', strtotime($metadata_to));
				$filename = 'Meta_Albums_' . $from_date . '_to_' . $to_date . '.xls';
				
				$writer = new Xls($spreadsheet);
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="' . $filename . '"');
				header('Cache-Control: max-age=0');
				$writer->save('php://output');
				exit;
			} else {
				
				return false;
			}
		}else{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/meta_data'); 
		}
	}
	
	private function formatMediaPath($fullPath, $spreadsheet, $cellCoordinate) {
		if (empty($fullPath)) {
			return '';
		}
		
		$patterns = [
			'/^\/home\/auvadigitalmedia\/public_html\//',
			'/^\/home\/[^\/]+\/public_html\//'
		];
		
		$displayPath = $fullPath;
		foreach ($patterns as $pattern) {
			if (preg_match($pattern, $fullPath)) {
				$displayPath = preg_replace($pattern, '', $fullPath);
				break;
			}
		}
		
		if ($displayPath === $fullPath) {
			$parts = explode('/public_html/', $fullPath);
			if (count($parts) > 1) {
				$displayPath = $parts[1];
			}
		}
		
		$baseUrl = 'https://' . $displayPath;
		
		$spreadsheet->getActiveSheet()->getCell($cellCoordinate)->getHyperlink()->setUrl($baseUrl);
		$spreadsheet->getActiveSheet()->getCell($cellCoordinate)->getHyperlink()->setTooltip('Click to open media file');
		
		return $displayPath;
	}
	
	// Helper method to check if data exists for date range
	



	public function download_permission()
	{
		is_access_method($permission_type=9,$sec_id='5');
		$this->mem_top_menu_section = 'meta_data';
		$data['heading_title'] = "Meta Data";
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/download_permission";
		$condition 		= "dp.status != '2' ";

		$member_type = $this->session->userdata('member_type'); 
	  if($member_type!='1'){
		if($this->userId>0){

			$condition .=" AND dp.member_id = '".$this->userId."'";
		}
       }
		$sort_by_rec 	= "dp.dp_id DESC";
		$param_dp = array(
			'fields'=>"dp.*,wc.user_name,wc.sponsor_id, CONCAT_WS(' ',wc.first_name,wc.last_name) AS name",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_customers as wc','condition'=>"wc.customers_id=dp.customers_id AND wc.status='1'",'type'=>'LEFT')
			),
			'orderby'=>$sort_by_rec,
			'groupby'=>'dp.dp_id',
			'debug'=>FALSE
		);
		$res_array = $this->admin_model->get_download_permission($param_dp);
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
			$this->form_validation->set_rules('customers_id', 'User Id', "trim|required");
			$this->form_validation->set_rules('permission', 'Permission', 'trim|required');
			if($this->form_validation->run()===TRUE){
				$posted_data = array(
					'member_id' 	=> $this->userId,
					'customers_id'	=> $this->input->post('customers_id',TRUE),	
					'permission'	=> $this->input->post('permission',TRUE),
					'created_date'	=> $this->config->item('config.date.time')
				);

				$posted_data = $this->security->xss_clean($posted_data);
				$this->admin_model->safe_insert('wl_download_permission',$posted_data,FALSE);
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',"Permission has been added successfully.");
				redirect('admin/download_permission', '');
			}
		}
		$condition  ="m.status = '1' AND m.member_type='3' AND dp.customers_id IS NULL";
        
	  if($member_type!='1'){
		if($this->userId>0){

			$condition .=" AND m.parent_id = '".$this->userId."'";
		}
	   }
		$paramc_emp = array(
			'fields'=>"m.customers_id,m.sponsor_id,CONCAT_WS(' ',m.first_name,m.last_name) AS name",
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_download_permission as dp','condition'=>"dp.customers_id=m.customers_id AND dp.status='1'",'type'=>'LEFT')
			),
			'groupby'=>'m.customers_id',
			'debug'=>FALSE
		);
		$res_customers      = $this->admin_model->get_members($paramc_emp);
		$data['res_customers'] = $res_customers;
		$this->load->view("admin/view_meta_data_download_permission",$data);
	}

	public function channel_add_request()
	{
		is_access_method($permission_type=1,$sec_id='6');
		$data['heading_title'] = "Channel Add Request";
		$this->mem_top_menu_section = 'channel_request';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/channel_add_request";
		$condition 		= "req.status != '2' AND req.request_type='1' ";
		if ($this->mres['member_type'] == '2' && $this->userId > 0) {
            $condition .= " AND (cus.parent_id = '{$this->userId}' OR req.member_id = '{$this->userId}') ";
        } 
        if ($this->mres['member_type'] == '3' && $this->userId > 0) {
            $condition .= " AND req.member_id = '{$this->userId}' ";
        }
		
		$sort_by_rec 	= "req.request_id DESC";
		$param_req = array(
			'fields'=>"req.*,cus.first_name",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=req.member_id AND cus.status='1'",'type'=>'LEFT')
			),
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
			is_access_method($permission_type=2,$sec_id='6');
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
				redirect('admin/channel_add_request', '');
			}
		}
		$this->load->view('view_channel_request_add',$data);
	}


	public function channel_white_list_request()
	{
		is_access_method($permission_type=1,$sec_id='6');
		$data['heading_title'] = "Channel White List Request";
		$this->mem_top_menu_section = 'channel_request';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/channel_white_list_request";
		$condition 		= "req.status != '2' AND req.request_type='2' ";
		
		if ($this->mres['member_type'] == '2' && $this->userId > 0) {
            $condition .= " AND (cus.parent_id = '{$this->userId}' OR req.member_id = '{$this->userId}') ";
        } 
        if ($this->mres['member_type'] == '3' && $this->userId > 0) {
            $condition .= " AND req.member_id = '{$this->userId}' ";
        }
		$sort_by_rec 	= "req.request_id DESC";
		$param_req = array(
			'fields'=>"req.*,cus.first_name",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=req.member_id AND cus.status='1'",'type'=>'LEFT')
			),
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
			is_access_method($permission_type=2,$sec_id='6');
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
				redirect('admin/channel_white_list_request', '');
			}
		}
		$this->load->view('view_channel_white_request_add',$data);
	}


	
	public function claim_release_request()
	{
		is_access_method($permission_type=1,$sec_id='6');
		$data['heading_title'] = "Claim Release Request";
		$this->mem_top_menu_section = 'channel_request';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/claim_release_request";
		$condition 		= "req.status != '2' AND req.request_type='3' ";
		
		if ($this->mres['member_type'] == '2' && $this->userId > 0) {
            $condition .= " AND (cus.parent_id = '{$this->userId}' OR req.member_id = '{$this->userId}') ";
        } 
        if ($this->mres['member_type'] == '3' && $this->userId > 0) {
            $condition .= " AND req.member_id = '{$this->userId}' ";
        }
		$sort_by_rec 	= "req.request_id DESC";
		$param_req = array(
			'fields'=>"req.*,cus.first_name",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=req.member_id AND cus.status='1'",'type'=>'LEFT')
			),
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
			is_access_method($permission_type=2,$sec_id='6');
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
				redirect('admin/claim_release_request', '');
			}
		}
		$this->load->view('view_claim_release_request_add',$data);
	}



	public function channel_request_status()
	{
		if($this->input->post('btn_sbt')!=''){
			$custom_error_flds = array();
			$request_id = $this->input->post('request_id',TRUE);
			$posted_status = $this->input->post('status',TRUE);
			$this->form_validation->set_rules('request_id', 'Request Id', "trim|required");
			$this->form_validation->set_rules('status', 'Status', "trim|required");
			if($posted_status=='3'){
				$this->form_validation->set_rules('reason', 'Reason', "trim|required|max_length[120]");
			}
			$form_validation = $this->form_validation->run();
			if($form_validation===TRUE && empty($custom_error_flds)){
				$posted_data = array(
					'status'=> $posted_status,
					'reason'=> $this->input->post('reason',TRUE)	
				);

				$posted_data = $this->security->xss_clean($posted_data);
				$where       = "request_id = '".$request_id."'";
				$this->admin_model->safe_update('wl_youtube_requests',$posted_data,$where,FALSE);
				$ret_data = array('status'=>'1','msg'=>'Updating...','request_id'=>$request_id);
			}else{
				$error_array = req_compose_errors($custom_error_flds);
				$ret_data = array('status'=>'0','error_flds'=>$error_array);
			}
			echo json_encode($ret_data);
			die;
		}
	}

	public function download_permission_delete()
	{
		$dpId = $this->uri->segment(3);
		 $is_exist = count_record('wl_download_permission'," md5(dp_id)='".$dpId."' ");

		
		if($is_exist>0)
		{
			
			$where = array("md5(dp_id)"=>$dpId);
			$this->admin_model->safe_delete('wl_download_permission',$where,true);

			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Meta download permission has been removed successfully.");
			redirect('admin/download_permission'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/download_permission'); 
		}		
	}

	public function channel_request_delete()
	{
		is_access_method($permission_type=4,$sec_id='6');
		$request_id = $this->uri->segment(3);
		$is_exist = count_record('wl_youtube_requests'," md5(request_id)='".$request_id."' ");
		if($is_exist>0)
		{
			$where = "md5(request_id) = '".$request_id."'";
			$this->admin_model->safe_update('wl_youtube_requests',array('status'=>'2'),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Channel request has been deleted successfully.");
			redirect($this->input->server('HTTP_REFERER'));
			//redirect('admin/channel_request'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect($this->input->server('HTTP_REFERER'));
			//redirect('admin/channel_request'); 
		}		
	}

	public function members()
	{
		is_access_method($permission_type=1,$sec_id='2');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = "Manage Users";
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/sub_admins";
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$status 		= $this->db->escape_str($this->input->get_post('status',TRUE));
		$condition 	     ="m.status != '2' AND m.member_type='3'";
		if($this->mres['member_type']!='1'){
			$condition .=" AND m.parent_id = '".$this->userId."'";
		}
		if($keyword!='')
		{
			$condition.=" AND  ( m.first_name like '%$keyword%' OR m.last_name like '%$keyword%' OR m.mobile_number like '%$keyword%' OR m.user_name like '%$keyword%') ";
		}

		if($status!='')
		{
			$condition.=" AND  m.status='".$status."' ";
		}
		$sort_by_rec 	 ="m.customers_id DESC";
		$paramc_emp = array(
					'fields'=>"m.*,CONCAT_WS(' ',m.first_name,m.last_name) AS name",
					'offset'=>$db_offset,
					'limit'=>$per_page,
					'where'=>$condition,
					'orderby'=>$sort_by_rec,
					'groupby'=>'m.customers_id',
					'debug'=>FALSE
				);
		$res_array              = $this->admin_model->get_members($paramc_emp);
		$data['total_recs'] 	= $total_recs = $this->admin_model->total_rec_found;

		$params_pagination = array(
			'data_form'=>'#search_form',
			'base_link'=>$base_link,
			'per_page'=>$per_page,
			'total_recs'=>$total_recs,
			'uri_segment'=>$offset,
			'refresh'=>1
		);
		
		if($this->input->post('Send',TRUE)=='Send Email')
		{
		$this->send_notification();	
		//trace($this->input->post());exit;
		$this->session->set_flashdata('success',"Mail has been send successfully.");
			redirect('admin/members'); 
		}
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['res'] 		= $res_array;

		$this->load->view("admin/view_member_list",$data);
	}

	public function member_add()
	{
		is_access_method($permission_type=2,$sec_id='2');
		$this->mem_top_menu_section = 'sub_admins';
		$img_allow_size =  $this->config->item('allow.file.size');
		$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
		$data['heading_title'] = 'Add User';
		if($this->input->post('action')!=''){
			$this->form_validation->set_rules('first_name', 'Name', 'trim|required|alpha|max_length[50]');
			$this->form_validation->set_rules('mobile_number', 'Mobile', 'trim|required|numeric|min_length[10]|max_length[15]');
			$this->form_validation->set_rules('user_name', 'Email ID','trim|required|valid_email|max_length[80]|callback_email_check');
			$this->form_validation->set_rules('commission', 'Commission','trim|required|greater_than_equal_to[0]');
			$this->form_validation->set_rules('country', 'Country', 'trim|required');
			$this->form_validation->set_rules('state', 'State', 'trim|required');
			$this->form_validation->set_rules('city', 'City', 'trim|required');
			$this->form_validation->set_rules('pin_code', 'Pin Code', 'trim|required|max_length[6]');
			$this->form_validation->set_rules('profile_photo','Profile Photo',"file_required|file_allowed_type[image]|file_size_max[$img_allow_size]");
			$this->form_validation->set_rules('address', 'Address', 'trim|required|max_length[200]');
			$this->form_validation->set_rules('aadhar_doc','Aadhar Card',"file_required|file_allowed_type[pdf]|file_size_max[$img_allow_size]");
			$this->form_validation->set_rules('agreement_doc','User Agreement',"file_required|file_allowed_type[pdf]|file_size_max[$img_allow_size]");
			if ($this->form_validation->run() == TRUE)
			{
				$profile_photo = "";
				if( $_FILES['profile_photo']['name']!='' ){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('profile_photo','profiles');
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
						$profile_photo = $uploaded_data['upload_data']['file_name'];
					}
				}
				$aadhar_doc = "";
				if( !empty($_FILES['aadhar_doc']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('aadhar_doc','members');
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
						$aadhar_doc = $uploaded_data['upload_data']['file_name'];
					}
				}
				$agreement_doc = "";
				if( !empty($_FILES['agreement_doc']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('agreement_doc','members');
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
						$agreement_doc = $uploaded_data['upload_data']['file_name'];
					}
				}
				$user_name = $this->input->post('user_name',TRUE);
				$actkey = md5($user_name."-".random_string('numeric',4));
				$password = 'user@123';
				$encoded_password  =  $this->safe_encrypt->encode($password);
				$country_id = $this->input->post('country',TRUE);
				$state_id = $this->input->post('state',TRUE);
				/*
				
				$city_id = $this->input->post('city');

				$city_res = $city_id > 0  ? log_fetched_rec($city_id,'city','title') : '';
				$city_name = !empty($city_res['rec_data']) ? $city_res['rec_data']['title'] : '';
				*/
				$state_res = $state_id > 0  ? log_fetched_rec($state_id,'state','title') : '';
				$state_name = !empty($state_res['rec_data']) ? $state_res['rec_data']['title'] : '';
				
				$country_res = $country_id > 0  ? log_fetched_rec($country_id,'country','country_name') : '';
				$country_name = !empty($country_res['rec_data']) ? $country_res['rec_data']['country_name'] : '';
				$sponsorId = generate_sponsorId();
				$posted_data = array(					
					'actkey'    	=> $actkey,
					'parent_id' 	=> $this->userId,
					'member_type'	=> '3', 
					'mem_nature'	=> '1', 
					'sponsor_id'	=> $sponsorId,
					'user_name'		=> $user_name,
					'password'		=> $encoded_password,
					"first_name"	=> $this->input->post('first_name',TRUE), 
					"mobile_number"	=> $this->input->post('mobile_number',TRUE),
					'profile_photo'	=> $profile_photo,
					'aadhar_doc'	=> $aadhar_doc,
					'agreement_doc'	=> $agreement_doc,
					'address' 		=> $this->input->post('address',TRUE),
					'pin_code'		=> $this->input->post('pin_code',TRUE),
					'country'		=> $country_id,
					'country_name'	=> $country_name,
					'state'	        => $state_id,
					'state_name'	=> $state_name,		
					'city_name'		=> $this->input->post('city',TRUE),
					'commission'	=> $this->input->post('commission',TRUE),
					'ip_address'  	=> $this->input->ip_address(),
					'added_by'  	=> '1',
					'is_verified'	=> '1',
					'account_created_date'=> $this->config->item('config.date.time')
				);  
				
				$posted_data = $this->security->xss_clean($posted_data); 
				$registerId = $this->admin_model->safe_insert('wl_customers',$posted_data);
				if($registerId > 0)
				{
					$first_name  = $this->input->post('first_name',TRUE);
					$last_name   = '';
					/* Send  mail to user */
					$content    =  get_content('1','wl_auto_respond_mails');
					$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
					$body       =  $content->email_content;
					$verify_url = "<a href=".base_url()."user/verify/".$actkey.">Click here </a>";
					$name 		= ucwords($first_name)." ".ucwords($last_name);
					$body		=	str_replace('{mem_name}',$name,$body);
					$body		=	str_replace('{username}',$user_name,$body);
					$body		=	str_replace('{password}',$password,$body);
					$body		=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
					$body		=	str_replace('{site_name}',$this->config->item('site_name'),$body);
					$body		=	str_replace('{url}',base_url(),$body);
					$body		=	str_replace('{verification_link}',$verify_url,$body);
					$mail_conf =  array(
						'subject'=>$subject,
						'to_email'=>$user_name,
						'from_email'=>$this->admin_info->admin_email,
						'from_name'=> $this->config->item('site_name'),
						'body_part'=>$body
					);

					$this->dmailer->mail_notify($mail_conf);
					/* End send  mail to user */
					$content    =  get_content('6','wl_auto_respond_mails');			
					$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
					$body       =  $content->email_content;
					$name 			= ucwords($first_name)." ".ucwords($last_name);
					$body			=	str_replace('{name}',$name,$body);
					$body			=	str_replace('{username}',$user_name,$body);
					$body			=	str_replace('{password}',$password,$body);
					$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
					$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
					$body			=	str_replace('{url}',base_url(),$body);

					$mail_conf =  array(
						'subject'=>$subject,
						'to_email'=>$this->admin_info->admin_email,
						'from_email'=>$this->input->post('user_name'),
						'from_name'=> $this->config->item('site_name'),
						'body_part'=>$body
					);
					$this->dmailer->mail_notify($mail_conf);
				}
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success', 'You have added sub user successfully.');
				redirect('admin/members', '');
			}
		}
		$this->load->view('admin/view_member_add',$data);
	}

	public function member_edit()
	{
		is_access_method($permission_type=3,$sec_id='2');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'Edit User';
		$customer_id = $this->uri->segment(3);
		$img_allow_size =  $this->config->item('allow.file.size');
		$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
		$res_subadmin = get_db_single_row('wl_customers','customers_id'," AND md5(customers_id)='".$customer_id."' ");
		if(is_array($res_subadmin) && !empty($res_subadmin))
		{
			$data['mres'] = $mres = $this->admin_model->get_member_row($res_subadmin['customers_id']);
			if($this->input->post('action')!=''){
				$this->form_validation->set_rules('first_name', 'Name', 'trim|required|alpha|max_length[50]');
				$this->form_validation->set_rules('mobile_number', 'Mobile','trim|required|numeric|min_length[10]|max_length[15]');
				$this->form_validation->set_rules('country','Country', 'trim|required');
				$this->form_validation->set_rules('state', 'State', 'trim|required');
				$this->form_validation->set_rules('city', 'City', 'trim|required');
				$this->form_validation->set_rules('pin_code', 'Pin Code', 'trim|required|max_length[6]');
				$this->form_validation->set_rules('profile_photo','Profile Photo',"file_allowed_type[image]|file_size_max[$img_allow_size]|check_dimension[$img_allow_dim]");
				$this->form_validation->set_rules('address', 'Address', 'trim|required|max_length[200]');
				$this->form_validation->set_rules('aadhar_doc','Aadhar Card',"file_allowed_type[pdf]|file_size_max[$img_allow_size]");
				$this->form_validation->set_rules('agreement_doc','User Agreement',"file_allowed_type[pdf]|file_size_max[$img_allow_size]");
				if ($this->form_validation->run() == TRUE)
				{	
					$profile_photo = $mres['profile_photo'];
					$unlink_profile_photo = array('source_dir'=>"profiles",'source_file'=>$profile_photo);
					if($this->input->post('aadhar_doc_delete')==='Y'){
						removeImage($unlink_profile_photo);
						$profile_photo = NULL;
					}
					if( !empty($_FILES) && $_FILES['profile_photo']['name']!='' ){
						$this->load->library('upload');
						$uploaded_data =  $this->upload->my_upload('profile_photo','profiles');
						if( is_array($uploaded_data) && !$uploaded_data['err'] ){
							$profile_photo = $uploaded_data['upload_data']['file_name'];
							removeImage($unlink_profile_photo);
						}
					}
					$aadhar_doc = $mres['aadhar_doc'];
					$unlink_aadhar_doc = array('source_dir'=>"members",'source_file'=>$aadhar_doc);
					if($this->input->post('aadhar_doc_delete')==='Y'){
						removeImage($unlink_aadhar_doc);
						$aadhar_doc = NULL;
					}
					if( !empty($_FILES) && $_FILES['aadhar_doc']['name']!='' ){
						$this->load->library('upload');
						$uploaded_data =  $this->upload->my_upload('aadhar_doc','members');
						if( is_array($uploaded_data) && !$uploaded_data['err'] ){
							$aadhar_doc = $uploaded_data['upload_data']['file_name'];
							removeImage($unlink_aadhar_doc);
						}
					}
					$agreement_doc = $mres['agreement_doc'];
					$unlink_agreement_doc = array('source_dir'=>"members",'source_file'=>$agreement_doc);
					if($this->input->post('agreement_doc_delete')==='Y'){
						removeImage($unlink_agreement_doc);
						$agreement_doc = NULL;
					}
					if( !empty($_FILES) && $_FILES['agreement_doc']['name']!='' ){
						$this->load->library('upload');
						$uploaded_data =  $this->upload->my_upload('agreement_doc','members');
						if( is_array($uploaded_data) && !$uploaded_data['err'] ){
							$agreement_doc = $uploaded_data['upload_data']['file_name'];
							removeImage($unlink_agreement_doc);
						}
					}	
					$country_id = $this->input->post('country',TRUE);
					$state_id = $this->input->post('state',TRUE);
					/*
					$city_id = $this->input->post('city');
					$city_res = $city_id > 0  ? log_fetched_rec($city_id,'city','title') : '';
					$city_name = !empty($city_res['rec_data']) ? $city_res['rec_data']['title'] : '';
					*/
					$state_res = $state_id > 0  ? log_fetched_rec($state_id,'state','title') : '';
					$state_name = !empty($state_res['rec_data']) ? $state_res['rec_data']['title'] : '';
					
					$country_res = $country_id > 0  ? log_fetched_rec($country_id,'country','country_name') : '';
					$country_name = !empty($country_res['rec_data']) ? $country_res['rec_data']['country_name'] : '';		
					$updated_data = array(
						"first_name"	=> $this->input->post('first_name',TRUE), 
						"mobile_number"	=> $this->input->post('mobile_number',TRUE),
						'profile_photo'	=> $profile_photo,
						'aadhar_doc'	=> $aadhar_doc,
						'agreement_doc'	=> $agreement_doc,
						'address' 		=> $this->input->post('address',TRUE),
    					'pin_code'		=> $this->input->post('pin_code',TRUE),
    					'country'		=> $country_id,
    					'country_name'	=> $country_name,
    					'state'	        => $state_id,
    					'state_name'	=> $state_name,		
						'city_name'		=> $this->input->post('city',TRUE)
					);  
					
					$updated_data = $this->security->xss_clean($updated_data); 
					$where_subadmin = " md5(customers_id)='".$customer_id."' ";
					$this->admin_model->safe_update('wl_customers',$updated_data,$where_subadmin,FALSE);
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Details has been updated successfully.');
					redirect(current_url_query_string(), '');
				}
			}

			$this->load->view('admin/view_member_edit',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/members'); 
		}
	}

	public function edit_member_bank_info()
	{
		is_access_method($permission_type=3,$sec_id='2');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'Edit User';
		$customer_id = $this->uri->segment(3);
		$res_subadmin = get_db_single_row('wl_customers','customers_id'," AND md5(customers_id)='".$customer_id."' ");
		if(is_array($res_subadmin) && !empty($res_subadmin))
		{
			$data['mres'] = $mres = $this->admin_model->get_member_row($res_subadmin['customers_id']);
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
					$where_subadmin = " md5(customers_id)='".$customer_id."' ";
					$this->admin_model->safe_update('wl_customers',$updated_data,$where_subadmin,FALSE);
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Details has been updated successfully.');
					redirect('admin/edit_member_rate/'.$customer_id, '');
				}
			}

			$this->load->view('admin/view_member_bank_info_edit',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/members'); 
		}
	}

	public function edit_member_rate()
	{
		is_access_method($permission_type=3,$sec_id='2');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'Edit User';
		$customer_id = $this->uri->segment(3);
		$res_subadmin = get_db_single_row('wl_customers','customers_id'," AND md5(customers_id)='".$customer_id."' ");
		if(is_array($res_subadmin) && !empty($res_subadmin))
		{
			$data['mres'] = $mres = $this->admin_model->get_member_row($res_subadmin['customers_id']);
			if($this->input->post('action')!=''){

				$this->form_validation->set_rules('commission', 'Commission','trim|required|greater_than_equal_to[0]');
				if ($this->form_validation->run() == TRUE)
				{	
					
					$updated_data = array(
						"commission" => $this->input->post('commission',TRUE),
					);  
					
					$updated_data = $this->security->xss_clean($updated_data); 
					$where_subadmin = " md5(customers_id)='".$customer_id."' ";
					$this->admin_model->safe_update('wl_customers',$updated_data,$where_subadmin,FALSE);
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Details has been updated successfully.');
					redirect('admin/member_profile/'.$customer_id, '');
				}
			}

			$this->load->view('admin/view_member_commission_edit',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/members'); 
		}
	}

	public function member_delete()
	{
		is_access_method($permission_type=4,$sec_id='2');
		$customer_id = $this->uri->segment(3);
		$is_exist = count_record('wl_customers'," md5(customers_id)='".$customer_id."' ");
		if($is_exist>0)
		{
			$where = "md5(customers_id) = '".$customer_id."'";
			$this->admin_model->safe_update('wl_customers',array('status'=>'2'),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"User has been Deleted successfully.");
			redirect('admin/members'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/members'); 
		}		
	}

	public function member_status()
	{
		is_access_method($permission_type=5,$sec_id='2');
		$customer_id = $this->uri->segment(3);
		$status = $this->input->get_post('status');
		$is_exist = count_record('wl_customers'," md5(customers_id)='".$customer_id."' ");
		if($is_exist>0)
		{
			if($status=='active')
			{
				$sts = '1';
				$stsmsg = 'activated';
			}
			elseif($status=='deactive')
			{
				$sts = '0';
				$stsmsg = 'deactivated';
			}
			$where = "md5(customers_id) = '".$customer_id."'";
			$this->admin_model->safe_update('wl_customers',array('status'=>$sts),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"User has been $stsmsg successfully.");
			redirect('admin/members'); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/members'); 
		}
	}

	public function member_profile()
	{
		is_access_method($permission_type=1,$sec_id='2');
		$this->mem_top_menu_section = 'sub_admins';
		$data['heading_title'] = 'View User';
		$customer_id = $this->uri->segment(3);
		$res_subadmin = get_db_single_row('wl_customers','customers_id'," AND md5(customers_id)='".$customer_id."' ");
		if(is_array($res_subadmin) && !empty($res_subadmin))
		{
			$data['mres'] = $this->admin_model->get_member_row($res_subadmin['customers_id']);
			$this->load->view('admin/view_member_profile',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/sub_admins'); 
		}
	}

	public function email_check()
	{
		$this->load->model('user/user_model');
		$email = $this->input->post('user_name');
		if ($this->user_model->is_email_exits(array('user_name' => $email)))
		{
			$this->form_validation->set_message('email_check', $this->config->item('exists_user_id'));
			return FALSE;
		}else
		{
			return TRUE;
		}
	}

	public function mobile_check()
	{
		$this->load->model('user/user_model');
		$mobile_number = $this->input->post('mobile_number');
		if ($this->user_model->is_email_exits(array('mobile_number' => $mobile_number)))
		{
			$this->form_validation->set_message('mobile_check', "Mobile number already exist.");
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	private function send_notification($param =array()){
		$this->load->library(array("dmailer"));
		$this->load->model(array('admin/admin_model','notification/notification_model'));
		$Id = $this->db->escape_str($this->input->get_post('notification_id',TRUE));
		$this->form_validation->set_rules('notification_id', 'Select notification', 'trim|required|max_length[80]');
				if ($this->form_validation->run() == TRUE)
				{	
		if($Id>0) 
		{
				$where_notification = "wn.notification_id='".$Id."'";
		$params_notification = array(	
							'where'=>$where_notification,
							'fetch_type'=>'row_array'
						);
		$res   =  $this->notification_model->get_notification($params_notification);
		if( is_array($res) && !empty($res) )
		{
			
			$arr_ids = $this->input->post('arr_ids');
			$str_ids =  is_array($arr_ids) ? implode(',', $arr_ids) : $arr_ids;
			$res_emp=$this->admin_model->getemail_by_id($str_ids);
			
			//trace($res_emp);exit;
			
		 	if(!empty($res_emp)){
          		$notification_res = get_db_single_row('wl_notification','notification_title,description',array("notification_id"=>$Id));
				$notification_rec_exists = is_array($notification_res) && !empty($notification_res) ? true : false;
          		$notification_mail_control_value = 'Y';
				if($notification_mail_control_value=='Y'){
					$content    =  get_content('7','wl_auto_respond_mails');
					$subject    =  $content->email_subject;
					$subject	=	str_replace('{site_name}',$this->config->item('site_name'),$subject);
				}
				//foreach ($res_emp as $key => $val){
					$mail_to = explode(",",$res_emp);
					
					foreach ($mail_to as $to){
						$cut_res = get_db_single_row('wl_customers','customers_id,first_name,user_name',array("user_name"=>$to));
					 $check_notification = count_record('wl_notification_customer',"notification_id='".$Id."' AND customer_id='".$cut_res['customers_id']."'");
					
					//$res_admin = get_db_single_row('tbl_admin','admin_email'," AND admin_id='1' ");
					
					if($check_notification=='0'){
						$posted_data = array(
								'notification_id'=>$Id,
								'customer_id'=>$cut_res['customers_id'],
								'created_at'=>$this->config->item('config.date.time'),							
							);
							
						$posted_data = $this->security->xss_clean($posted_data);
						$this->notification_model->safe_insert('wl_notification_customer',$posted_data,FALSE);
						if($notification_mail_control_value=='Y' && $notification_rec_exists){
							$body       	=  $content->email_content;
							$body			=	str_replace('{mem_name}',$cut_res['first_name'],$body);
							$body			=	str_replace('{notification_title}',$notification_res['notification_title'],$body);
							$body			=	str_replace('{description}',$notification_res['description'],$body);
							$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
							$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
							$mail_conf =  array(
								'subject'    => $subject,
								'to_email'   => $cut_res['user_name'],
								'from_email' => $this->admin_info->admin_email,
								'from_name'  => $this->config->item('site_name'),
								'body_part'  => $body
							);
							
							//trace($mail_conf);exit;
							@$this->dmailer->mail_notify($mail_conf);
						}
					}
					}
				}
          	}
		}
		}
	}
}