<?php
class Pages extends Public_Controller
{

	public function __construct() {
		parent::__construct();
		$this->load->helper(array('file'));
		$this->load->library(array('Dmailer'));
		$this->load->model(array('pages/pages_model'));
		$this->form_validation->set_error_delimiters("<div class='required'>","</div>");
	}

	public function index() {
		$friendly_url	 = @$this->meta_info['page_url'];
		$condition       = array('friendly_url'=>$friendly_url,'status'=>'1');
		$content         =  $this->pages_model->get_cms_page( $condition );
		$this->header_menu_section = $friendly_url;
		$data['content'] = $content;
		$this->load->view('pages/cms_page_view',$data);
	}
	
	public function merge_demo_old(){
	    die('Do not run this process');
		$signed_data = $this->db->get_where('wl_signed_albums',['status!='=>'2'])->result_array();
		if(is_array($signed_data) && !empty($signed_data)){
			foreach($signed_data as $key =>$val){
				if(!$val['release_ref_id']>0){
					$status = 5;
					if($val['is_verify_meta']==1 && $val['is_pdl_submit']==1){
						$status = 1;
					}elseif($val['is_verify_meta']==1 && $val['is_pdl_submit']==0){
						$status = 6;
					}
					$api_response = json_decode($val['metadata'],true);
					$api_data  = $api_response['data'];
			        $album     = $api_data['signed_albums'][0];
			        $song      = $album['songs'][0];
			        $song_data = $song['data'] ?? [];
					$posted_data = [
						'member_id' 		=> $val['member_id'],
						"album_type"		=> '1', 
						"release_title"		=> $val['album_name'], 
						"p_line"			=> $song_data['p_line'],
						'version'			=> '',
						'c_line'			=> $song_data['c_line'],
						'production_year'	=> '',
						'artist_name'		=> $val['artist_id'],
						'feature_artist'	=> $song['track_featured_artist'][0]['name'] ?? '',
						'upc_ean'			=> $song_data['upc_id'],
						'go_live_date'		=> $val['go_live_date'],
						'label_name'		=> $song_data['label'],
						'producer_catalogue'=> '',
						'genre'				=> $song_data['genre'],
						'sub_genre'			=> $song_data['sub_genre'],
						'is_various_artist'	=> '0',
						'release_banner'	=> $val['album_image'],
						'release_song'		=> $val['album_media'],
						"prim_track_type"	=> '1',
						'release_type'		=> 'Album',
						'content_type'		=> $song_data['content_type'],
						'is_instrumental'	=> ($val['is_instrumental']=='No') ? 0 :1,
						'crbt_title'		=> $song_data['crbt_cut_name'] ?? '',
						'time_for_crbt_cut'	=> $song_data['time_for_crbt_cut'] ?? '00',
						'song_name'			=> $song_data['song_name'],
						'composer'			=> $song['composers'][0]['name'] ?? '',
						'music_director'	=> $song['directors'][0]['name'] ?? '',
						'publisher'			=> $song_data['publisher'],
						'isrc'				=> $song_data['isrc'],
						'track_duration'	=> $song_data['track_duration'],
						'lyricist'			=> $song['lyricists'][0]['name'] ?? '',
						'is_isrc'			=> '0',
						'song_mood'			=> $song_data['mood'],
						'lyrics_lang'		=> $song_data['language'],
						'lyrics'			=> $song_data['description'],
						'track_title_lang'	=> $song_data['language'],
						'track_price'		=> '0.00',
						'original_release_date_of_music'=> $song_data['original_release_date_of_music'],
						'status'			=> $status,
						'created_date'		=> $val['created_date'],
					];
					trace($posted_data);
				// 	$inserted_release_id = $this->pages_model->safe_insert('wl_releases',$posted_data, FALSE);
				// 	$this->pages_model->safe_update('wl_signed_albums',['release_ref_id'=>$inserted_release_id],['id'=>$val['id']], FALSE);
				}
			}
			echo "Merging Process done.";
			die;
		}
	}
	
	
	public function customized_product() {
		$img_allow_size =  $this->config->item('allow.file.size');
		$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
		
		$this->form_validation->set_rules('first_name', 'Name', 'trim|alpha|required|max_length[80]'); 		
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[80]');		
		$this->form_validation->set_rules('mobile_number', 'Mobile Number', 'trim|required|is_numeric|max_length[15]');		 
		$this->form_validation->set_rules('description', 'Bulk Detail', 'trim|required|max_length[1000]');
		$this->form_validation->set_rules('product_image','Image',"file_allowed_type[image]|file_size_max[$img_allow_size]|check_dimension[$img_allow_dim]");
		
		//$this->form_validation->set_rules('verification_code', 'Verification code', 'trim|required|valid_captcha_code');
	$data['page_error'] = "";
	
    if ($this->form_validation->run() === TRUE) {
		
			$uploaded_file = "";			
			if( !empty($_FILES) && $_FILES['product_image']['name']!='' )
			{
				$this->load->library('upload');
				$uploaded_data =  $this->upload->my_upload('product_image','customize');
				
				if( is_array($uploaded_data)  && !empty($uploaded_data) )
				{
					$uploaded_file = $uploaded_data['upload_data']['file_name'];
				}
			}
		
			$posted_data=array(
			'first_name'     => $this->input->post('first_name'),
			'type'           => '2',
			'email'          => $this->input->post('email'),
			'mobile_number'  => $this->input->post('mobile_number'),
			'message'         => $this->input->post('description'),
			'product_image'    =>$uploaded_file,
			'product_service'    => '',
			'receive_date'     =>$this->config->item('config.date.time')
			);

			$posted_data = $this->security->xss_clean($posted_data);
			$this->pages_model->safe_insert('wl_enquiry',$posted_data,FALSE);	
			// Send  mail to user
			$content    =  get_content('wl_auto_respond_mails','8');
			$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
			$body       =  $content->email_content;
			$name = ucwords($this->input->post('first_name'));
			$body			=	str_replace('{mem_name}',$name,$body);
			$body 			=   str_replace('{body_text}', 'You have placed an enquiry and details are given below.', $body);
			$body			=	str_replace('{email}',$this->input->post('email'),$body);
			//$body			=	str_replace('{phone}',$this->input->post('phone_number'),$body);
			$body			=	str_replace('{mobile}',$this->input->post('mobile_number'),$body);
			$body			=	str_replace('{product_name}',$product_res['product_name'],$body);
			$body			=	str_replace('{comments}',$this->input->post('description'),$body);
			$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
			$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);

			$mail_conf =  array(
			'subject'=>$subject,
			'to_email'=>$this->input->post('email'),
			'from_email'=>$this->admin_info->admin_email,
			'from_name'=> $this->config->item('site_name'),
			'body_part'=>$body
			);			
		
			$this->dmailer->mail_notify($mail_conf);
			// End send  mail to user
			// Send  mail to admin
			$body       =  $content->email_content;
			$name = 'Admin';
			$body			=	str_replace('{mem_name}',ucwords($this->input->post('first_name')),$body);
			$body 			=   str_replace('{body_text}', 'You have received an enquiry and details are given below.', $body);
			$body			=	str_replace('{email}',$this->input->post('email'),$body);
			//$body			=	str_replace('{phone}',$this->input->post('phone_number'),$body);
			$body			=	str_replace('{mobile}',$this->input->post('mobile_number'),$body);
			$body			=	str_replace('{product_name}',$product_res['product_name'],$body);
			$body			=	str_replace('{comments}',$this->input->post('description'),$body);
			$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
			$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);

			$mail_conf =  array(
			'subject'=>$subject,
			'to_email'=>$this->admin_info->admin_email,
			'from_email'=>$this->admin_info->admin_email,
			'from_name'=> $this->config->item('site_name'),
			'body_part'=>$body
			);
			//trace($mail_conf);
			//exit;
			$this->dmailer->mail_notify($mail_conf);
			// End send  mail to admin
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success', 'Your enquiry has been submitted successfully.');
			//$link_url = base_url().$product_res['friendly_url'];
			//redirect_top($link_url, '');
			redirect('pages/thanks', '');
		}
    
	$data['title'] = 'Customize';
    $this->load->view('pages/view_customize_enquiry',$data);
	
  }
	
	public function sendenquiry() {
		$this->form_validation->set_rules('first_name', 'Name', 'trim|alpha|required|max_length[80]'); 		
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[80]');		
		$this->form_validation->set_rules('mobile_number', 'Mobile Number', 'trim|is_numeric|required|max_length[15]');		 
		$this->form_validation->set_rules('description', 'Bulk Detail', 'trim|required|max_length[1000]');
		$this->form_validation->set_rules('verification_code', 'Verification code', 'trim|required|valid_captcha_code');

	$data['page_error'] = "";
	
    if ($this->form_validation->run() === TRUE) {
			$posted_data=array(
			'first_name'     => $this->input->post('first_name'),
			'type'           => '3',
			'email'          => $this->input->post('email'),
			'mobile_number'  => $this->input->post('mobile_number'),
			'message'         => $this->input->post('description'),
			'product_service'    => '',
			'receive_date'     =>$this->config->item('config.date.time')
			);

			$posted_data = $this->security->xss_clean($posted_data);
			$this->pages_model->safe_insert('wl_enquiry',$posted_data,FALSE);	
			// Send  mail to user
			$content    =  get_content('wl_auto_respond_mails','8');
			$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
			$body       =  $content->email_content;
			$name = ucwords($this->input->post('first_name'));
			$body			=	str_replace('{mem_name}',$name,$body);
			$body 			=   str_replace('{body_text}', 'You have placed an enquiry and details are given below.', $body);
			$body			=	str_replace('{email}',$this->input->post('email'),$body);
			//$body			=	str_replace('{phone}',$this->input->post('phone_number'),$body);
			$body			=	str_replace('{mobile}',$this->input->post('mobile_number'),$body);
			$body			=	str_replace('{product_name}',$product_res['product_name'],$body);
			$body			=	str_replace('{comments}',$this->input->post('description'),$body);
			$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
			$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);

			$mail_conf =  array(
			'subject'=>$subject,
			'to_email'=>$this->input->post('email'),
			'from_email'=>$this->admin_info->admin_email,
			'from_name'=> $this->config->item('site_name'),
			'body_part'=>$body
			);			
		
			$this->dmailer->mail_notify($mail_conf);
			// End send  mail to user
			// Send  mail to admin
			$body       =  $content->email_content;
			$name = 'Admin';
			$body			=	str_replace('{mem_name}',ucwords($this->input->post('first_name')),$body);
			$body 			=   str_replace('{body_text}', 'You have received an enquiry and details are given below.', $body);
			$body			=	str_replace('{email}',$this->input->post('email'),$body);
			//$body			=	str_replace('{phone}',$this->input->post('phone_number'),$body);
			$body			=	str_replace('{mobile}',$this->input->post('mobile_number'),$body);
			$body			=	str_replace('{product_name}',$product_res['product_name'],$body);
			$body			=	str_replace('{comments}',$this->input->post('description'),$body);
			$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
			$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);

			$mail_conf =  array(
			'subject'=>$subject,
			'to_email'=>$this->admin_info->admin_email,
			'from_email'=>$this->admin_info->admin_email,
			'from_name'=> $this->config->item('site_name'),
			'body_part'=>$body
			);
			//trace($mail_conf);
			//exit;
			$this->dmailer->mail_notify($mail_conf);
			// End send  mail to admin
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success', 'Your enquiry has been submitted successfully.');
			//$link_url = base_url().$product_res['friendly_url'];
			//redirect_top($link_url, '');
			redirect_top('pages/thanks', '');
		}
    
	$data['product_name'] = 'Enquiry';
    $this->load->view('pages/view_send_enquiry',$data);
	
  }

	public function contactus()
	{
		$this->form_validation->set_error_delimiters("<div class='float-left required'>","</div>");
		$this->shareable_properties['top_banner_file']=0;
		if($this->input->post('action')!=''){
			$this->form_validation->set_rules('first_name','First Name','trim|alpha|required|max_length[30]');
			$this->form_validation->set_rules('last_name','Last Name','trim|alpha|max_length[30]');
			$this->form_validation->set_rules('mobile_number','Mobile Number','trim|required|is_numeric|min_length[10]|max_length[15]');
			$this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[80]');
			$this->form_validation->set_rules('comment','Message','trim|required|max_length[8500]');
			$this->form_validation->set_rules('verification_code','Verification code','trim|required|valid_captcha_code[contact]');
			$data['page_error'] = "";

			if($this->form_validation->run()==TRUE){
				$posted_data=array(
				'first_name'    => $this->input->post('first_name'),
				'last_name'     => $this->input->post('last_name'),
				'email'         => $this->input->post('email'),
				'phone_number'  => $this->input->post('phone_number'),
				'mobile_number'  => $this->input->post('mobile_number'),
				'company_name'  => '',
				'contact_mode'	=> $this->input->post('contact_mode'),
				'message'       => $this->input->post('comment'),
				'receive_date'     =>$this->config->item('config.date.time')
				);

				$posted_data = $this->security->xss_clean($posted_data);
				$this->pages_model->safe_insert('wl_enquiry',$posted_data,FALSE);

				/* Send  mail to user */

				$content    =  get_content('wl_auto_respond_mails','5');
				$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
				$body       =  $content->email_content;

				$verify_url = "<a href=".base_url().">Click here </a>";

				$name = $sender_name = ucwords($this->input->post('first_name').' '.$this->input->post('last_name'));

				$body			=	str_replace('{mem_name}',$name,$body);
				$body			=	str_replace('{sender_name}',$name,$body);
				$body			=	str_replace('{email}',$this->input->post('email'),$body);
				//$body			=	str_replace('{phone}',$this->input->post('phone_number'),$body);
				$body			=	str_replace('{mobile}',$this->input->post('mobile_number'),$body);
				$body			=	str_replace('{comments}',$this->input->post('message'),$body);
				$body			=	str_replace('{contact_mode}',$this->input->post('contact_mode'),$body);
				$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
				$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
				$body			=	str_replace('{url}',base_url(),$body);
				$body			=	str_replace('{link}',$verify_url,$body);

				$mail_conf =  array(
				'subject'=>$subject,
				'to_email'=>$this->input->post('email'),
				'from_email'=>$this->admin_info->admin_email,
				'from_name'=> $this->config->item('site_name'),
				'body_part'=>$body
				);

				$this->dmailer->mail_notify($mail_conf);

				/* End send  mail to user */

				/* Send  mail to admin */

				$body       =  $content->email_content;

				$verify_url = "<a href=".base_url().">Click here </a>";

				$name = 'Admin';

				$body			=	str_replace('{mem_name}',$name,$body);
				$body			=	str_replace('{sender_name}',$sender_name,$body);
				$body			=	str_replace('{email}',$this->input->post('email'),$body);
				//$body			=	str_replace('{phone}',$this->input->post('phone_number'),$body);
				$body			=	str_replace('{mobile}',$this->input->post('mobile_number'),$body);
				$body			=	str_replace('{comments}',$this->input->post('message'),$body);
				$body			=	str_replace('{contact_mode}',$this->input->post('contact_mode'),$body);
				$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
				$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
				$body			=	str_replace('{url}',base_url(),$body);
				$body			=	str_replace('{link}',$verify_url,$body);

				$mail_conf =  array(
				'subject'=>$subject,
				'to_email'=>$this->admin_info->admin_email,
				'from_email'=>$this->admin_info->admin_email,
				'from_name'=> $this->config->item('site_name'),
				'body_part'=>$body
				);

				$this->dmailer->mail_notify($mail_conf);

				/* End send  mail to admin */

				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success', 'Your enquiry has been submitted successfully. We will get back to you soon.');
				// redirect(site_url('contactus'), '');
				redirect('pages/thanks');
			}
		}
		$data['heading_title'] = "Contact Us";
		$this->header_menu_section = 'contactus';
		$this->load->view('pages/contactus',$data);

	}
	
	public function is_subscribed($data)
	{
		$this->db->select('subscriber_id');
		$this->db->from('wl_newsletters');
		$this->db->where($data);
		$this->db->where('status !=', '2');

		$query = $this->db->get();
		if ($query->num_rows() == 1)
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	public function sitemap()
	{
		//Our Destinations
		$our_destination_limit = 8;
		$where_our_destinations = "cat.status='1' AND cat.cat_type='2' AND parent_id='0'";
		$params_our_destinations = array(
											'fields'=>"cat.category_id,cat.category_name,cat.friendly_url",
											'from'=>'wl_categories as cat',
											'orderby'=>'cat.sort_order',
											'limit'=>$our_destination_limit,
											'where'=>$where_our_destinations,
											'debug'=>FALSE
											);
		$res_our_destinations   = $this->utils_model->custom_query_builder($params_our_destinations);
		$total_our_destinations = $this->utils_model->total_rec_found;
		$data['heading_title'] = "Sitemap";
		$data['res_our_destinations'] = $res_our_destinations;
		$data['total_our_destinations'] = $total_our_destinations;
		$data['our_destination_limit'] = $our_destination_limit;
		$this->load->view('sitemap',$data);
	}
	
	public function newsletter()
	{
		$data['default_email_text']= "Email Id";
		$this->form_validation->set_rules('subscriber_name','Name','trim|required|alpha|max_length[100]');
		$this->form_validation->set_rules('subscriber_email','Email','trim|required|valid_email|max_length[255]');
		//$this->form_validation->set_rules('subscribe_me','Status','trim|required');
		$this->form_validation->set_rules('verification_code','Verification Code','trim|required|valid_captcha_code');
		if($this->form_validation->run()==TRUE)
		{
			$res = $this->pages_model->add_newsletter_member();
			$this->session->set_userdata('msg_type',$res['error_type']);
			$this->session->set_flashdata($res['error_type'],$res['error_msg']);
			redirect('pages/newsletter', '');
		}
		$this->load->view('view_subscribe_newsletter',$data);
	}

	private function subscribe_newsletter($posted_data) {
		$query = $this->db->query("SELECT subscriber_email,status FROM  wl_newsletters WHERE subscriber_email='$posted_data[subscriber_email]'");
		$subscribe_me  = $posted_data['subscribe_me'];

		if( $query->num_rows() > 0 ) {
			$row = $query->row_array();
			if( $row['status']=='0' && ($subscribe_me=='Y') ){
				$where = "subscriber_email = '".$row['subscriber_email']."'";
				$this->pages_model->safe_update('wl_newsletters',array('status'=>'1'),$where,FALSE);
				$msg =  $this->config->item('newsletter_subscribed');
				$ret_msg = array('err'=>0,'msg'=>$msg,'msg_type'=>'success');
			}else if($row['status']=='0' && ($subscribe_me=='N')){
				$msg =  $this->config->item('newsletter_not_subscribe');
				$ret_msg = array('err'=>0,'msg'=>$msg,'msg_type'=>'success');
			}else if($row['status']=='1' && ($subscribe_me=='Y')){
				$msg =  $this->config->item('newsletter_already_subscribed');
				$ret_msg = array('err'=>0,'msg'=>$msg,'msg_type'=>'success');
			}else if($row['status']=='1' && ($subscribe_me=='N')){
				$where = "subscriber_email = '".$row['subscriber_email']."'";
				$this->pages_model->safe_update('wl_newsletters',array('status'=>'0'),$where,FALSE);
				$msg =  $this->config->item('newsletter_unsubscribed');
				$ret_msg = array('err'=>0,'msg'=>$msg,'msg_type'=>'success');
		  }
	  }else{
		  if($subscribe_me=='N' ){
			  $msg =  $this->config->item('newsletter_not_subscribe');
			  $ret_msg = array('err'=>1,'msg'=>$msg,'msg_type'=>'error');
		  }else{
			  $data =  array('status'=>'1', 'subscriber_name'=>$posted_data['subscriber_name'], 'subscriber_email'=>$posted_data['subscriber_email']);
			  $data = $this->security->xss_clean($data);
			  $this->pages_model->safe_insert('wl_newsletters',$data);
				$msg =  $this->config->item('newsletter_subscribed');
				$ret_msg = array('err'=>0,'msg'=>$msg,'msg_type'=>'success');
			}
		}
		return $ret_msg;
	}

	public function service_newsletter() {
		$this->form_validation->set_error_delimiters('<div class="fs12" style="color:#fff; ">',"</div>");
		//$subscriber_name        =  'Member';
		$subscriber_name        = $this->input->post('subscriber_name',TRUE);
		$subscriber_email       = $this->input->post('subscriber_email',TRUE);
		$subscribe_me           = 'Y';//$this->input->post('subscribe_me',TRUE);
		$this->form_validation->set_rules('subscriber_name', 'Name', "trim|required|alpha|max_length[100]");
		$this->form_validation->set_rules('subscriber_email', 'Email ID', "trim|required|valid_email|max_length[80]");
		//$this->form_validation->set_rules('terms_conditions', 'Terms & Conditions', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('verification_code','Verification code','trim|required|valid_captcha_code[newsletter]');
		if ($this->form_validation->run() == TRUE){
			$posted_data = array( 'subscriber_name'=>$subscriber_name,'subscriber_email'=>$subscriber_email,'subscribe_me'=>$subscribe_me);
			$posted_data = $this->security->xss_clean($posted_data);
			$result      =  $this->subscribe_newsletter($posted_data);
			$ret_data = array('status'=>!$result['err'],'msg'=>$result['msg'],'msg_type'=>$result['msg_type']);
			echo json_encode($ret_data);
		}else{
			$error_array=array();
			$err_frm_flds = $this->form_validation->error_array();
			if(is_array($err_frm_flds)){
				foreach($err_frm_flds as $key=>$val)
				{
					$error_array[$key] = $val;
				}
			}
			$ret_data = array('status'=>0,'error_flds'=>$error_array);
			echo json_encode($ret_data);
		}
	}

	public function refer_to_friends()
	{
		$productId        = (int) $this->uri->segment(3);

		$data['heading_title'] = "Refer to a Friend";
		$this->form_validation->set_rules('your_name','Name','trim|required|alpha|max_length[100]');
		$this->form_validation->set_rules('your_email','Email','trim|required|valid_email|max_length[100]');
		$this->form_validation->set_rules('friend_name','Friend\'s Name','trim|required|alpha|max_length[100]');
		$this->form_validation->set_rules('friend_email','Friend\'s Email','trim|required|valid_email|max_length[100]');

		$this->form_validation->set_rules('verification_code','Verification code','trim|required|valid_captcha_code[refer]');

		if($this->form_validation->run()==TRUE)
		{

			$your_name     = $this->input->post('your_name',TRUE);
			$your_email    =  $this->input->post('your_email',TRUE);
			$friend_name   = $this->input->post('friend_name',TRUE);
			$friend_email  = $this->input->post('friend_email',TRUE);

			$should_save_friend=0;

			if($should_save_friend){
				$conditions   = "your_email ='$your_email' AND friend_email ='$friend_email' ";
				$count_result = $this->pages_model->findCount('wl_invite_friends',$conditions);

				if( !$count_result )
				{
					$posted_data =  array('your_name'=>$your_name,
					'your_email'=>$your_email,
					'friend_name'=>$friend_name,
					'friend_email'=>$friend_email,
					'receive_date'=>$this->config->item('config.date.time')
					);
					$posted_data = $this->security->xss_clean($posted_data);
					$this->pages_model->safe_insert('wl_invite_friends',$posted_data);
				}
			}

			$content    =  get_content('wl_auto_respond_mails','3');
			$body       =  $content->email_content;

			if($productId > 0 )
			{
				$product_link_url = get_db_field_value('wl_products','friendly_url'," AND products_id='$productId'");
				//$product_link_url =  base_url()."products/detail/$productId";
				$link_url = base_url().$product_link_url;
				$link_url= "<a href=".$link_url.">Click here </a>";
				$text ="Product";
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',$this->config->item('product_referred_success'));
			}else
			{
				$link_url = base_url();
				$link_url= "<a href=".$link_url.">Click here </a>";
				$text ="Site";
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',$this->config->item('site_referred_success'));
			}

			$body			=	str_replace('{friend_name}',$friend_name,$body);
			$body			=	str_replace('{your_name}',$your_name,$body);
			$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
			$body			=	str_replace('{text}',$text,$body);
			$body			=	str_replace('{site_link}',$link_url,$body);

			$mail_conf =  array(
			'subject'=>"Invitation from ".$your_name." to see",
			'to_email'=>$friend_email,
			'from_email'=>$your_email,
			'from_name'=>$your_name,
			'body_part'=>$body
			);
			$this->dmailer->mail_notify($mail_conf);
			redirect('pages/refer_to_friends', '');
			$this->load->view('pages/view_refer_to_friend',$data);

		}

		$this->load->view('pages/view_refer_to_friend',$data);

	}

	public function unsubscribe(){
		$subscribe_id=$this->uri->segment(3);

		$this->pages_model->safe_update('wl_newsletters',array('status'=>'0'),array("md5(subscriber_id)"=>$subscribe_id),TRUE);
		
		$msg =  $this->config->item('newsletter_unsubscribed');
		$this->session->set_userdata(array('msg_type'=>'success'));
		$this->session->set_flashdata('success',$msg);
		redirect("pages/thanks");
	}

	public function thanks()
	{
		$data['page_heading'] = 'Thank You';
		$this->load->view('thanks',$data);
	}

	public function _404(){
		$this->load->view("404");
	}
	
	public function error_404(){
		$this->output->set_status_header(404); 
		$this->load->view("404");
	}
	
	public function download_file($filename="",$folder=""){
	
		$folder = $this->uri->segment(3);
		$filename = $this->uri->segment(4);
		if($filename!='' && $folder!='' ){
			$data=file_get_contents(UPLOAD_DIR."/$folder/$filename");
			$this->load->helper("download");
			force_download($filename,$data);
		}else{
			redirect("");
		}
	}

	public function download($filename=""){
		if($filename){
			$data=file_get_contents(UPLOAD_DIR."/$filename");
			$this->load->helper("download");
			force_download($filename,$data);
		}else{
			redirect("");
		}
	}
	
	public function track_order()
	{		
		$data['ordmaster']  = '';
		$this->form_validation->set_rules('order_number','Order Number','trim|required|max_length[225]');
		//$this->form_validation->set_rules('shipping_email','Email','trim|required|valid_email|max_length[255]');
		//$this->form_validation->set_rules('verification_code','Verification Code','trim|required|valid_captcha_code');
		if($this->form_validation->run()==TRUE)
		{
			$order_id     = $this->input->post('order_number',TRUE);
			$email    =  $this->input->post('shipping_email',TRUE);
			$query = $this->db->query("SELECT order_id FROM wl_order WHERE order_id='".$order_id."' ");
			if( $query->num_rows() > 0 ){
				$this->session->set_userdata( array('track_order_id'=>$order_id) );
				$ordId =  $this->session->userdata('track_order_id');
			
				$this->load->model(array('order/order_model'));
				$order_res          = $this->order_model->get_order_master( $ordId );
				//$order_details_res  = $this->order_model->get_order_detail($order_res['order_id']);
				//$data['orddetail']  = $order_details_res;
				$data['ordmaster']  = $order_res;
				
			}else{
				$this->session->set_userdata(array('msg_type'=>'warning'));
				$this->session->set_flashdata('warning','Invalid Credentials, Please enter correct details to view your order details');
				redirect('pages/track_order');
			}
		}
		
		$data['page_heading'] = 'Track Your Order';
		$this->load->view('view_track_order',$data);
	}
	
	public function updatepassword()
	{
		die;
		$this->load->library(array('safe_encrypt'));
		$query=$this->db->query("select customers_id, npassword from wl_customers ")->result_array();
		foreach($query as $res)
		{
			$npassword  =  $this->safe_encrypt->encode($res['npassword']);
			$this->db->query("update wl_customers set password='".$npassword."' where customers_id='".$res['customers_id']."' ");
		}
	}
	
	
	
	public function cron_product_low_stock_mail()
	{									 
		$query = $this->db->query("SELECT prd.products_id,prd.product_name,prd.status,
								   (SELECT SUM(product_quantity) FROM wl_product_stock 
								    WHERE product_id=prd.products_id) AS total_quantity,
								   (SELECT SUM(inventory) FROM wl_product_stock 
								   WHERE product_id=prd.products_id) AS min_stock
								   FROM wl_products AS prd  LEFT JOIN wl_product_stock AS stk ON
								   stk.product_id=prd.products_id AND prd.status='1'
								   GROUP BY prd.products_id  ORDER BY prd.products_id ASC " );	
		if($query->num_rows()> 0)
		{
			$list_res=$query->result_array();			
			foreach($list_res as $key=>$val)
			{				
				$quantity	=	$val['total_quantity'];
				$low_stock	=	$val['min_stock'];
				
				if($quantity < $low_stock)
				{					
					$mail_to      = $this->admin_info->admin_email;
					$mail_subject = 'Low Stock Badge Product'; 
					$from_email   = $mail_to;
					$from_name    =  $this->config->item('site_name_mail');
					
					$body = " Dear Admin,<br />
					This Product <b>".$val['product_name']."</b> has been low stock badge pls update quantity.<br />
					Thanks and Regards,<br />						   
					{site_name} Team  ";
					
					
					//$body			=	str_replace('{username}',$res_data->admin_username,$body);
					//$body			=	str_replace('{password}',$res_data->admin_password,$body);
					$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
					
					
					$this->email->from($from_email, $from_name);
					$this->email->to($mail_to);			
					$this->email->subject($mail_subject);				
					$this->email->message($body);
					$this->email->set_mailtype('html');
					//echo $body;
					@$this->email->send();					
				}				
			}			
		}		
	}
	
	
	
	
	public function terms_condition() 
	{		
		$friendly_url = 'terms-conditions';//$this->uri->rsegments[3]; 		
		
		$condition       = array('friendly_url'=>$friendly_url,'status'=>'1');
		$content         =  $this->pages_model->get_cms_page( $condition );
		
		$data['content'] = $content;
		$data['is_header'] = FALSE;
		$this->load->view('view_terms_condition',$data);		
	}
	
	
	
	
	public function get_api_token()
	{
		$id = $this->input->get_post('id');
		echo get_db_field_value('wl_customers','device_id',' AND customers_id="'.$id.'"');
	}
	
	
	public function post_multiple_address_cart()
	{
		$address_ids 	= $this->input->post('address_ids');
		$user_id 		= '94';
		$app_id 		= "#bMNGSrpQu#!rfqIIiAordvuufLJRMUkvJFGLIKVAzrCIcZ";
		$app_type 		= "";
			
		$get_array = array('2'=>array(
										array('cart_id'=>"147825",'pid'=>"324",'qty'=>'1','time'=>'10:30'),
										array('cart_id'=>"314056",'pid'=>"317",'qty'=>'2','time'=>'05:10'),
									),
								 
							'3'=>array(
										array('cart_id'=>"147825",'pid'=>"324",'qty'=>'1','time'=>'10:30'),
									),							  
						);
					
		if(is_array($get_array) && !empty($get_array))
		{
			foreach($get_array as $key=>$val)
			{
				$address_id = $key;
				$get_address = get_db_single_row('wl_customers_address_book','*',array("address_type"=>'Ship',"address_id"=>$address_id));
				
				$cart_data = $val;
			//	trace($cart_data);
				
				if(is_array($cart_data) && !empty($cart_data))
				{
					$posted_data = array(
										'user_id'		=> $user_id,
										'app_id'		=> $app_id,
										'app_type'		=> $app_type,
										'address_id'	=> $address_id,
										'address_type'	=> $get_address['address_type'],
										'first_name'	=> $get_address['first_name'],
										'last_name'		=> $get_address['last_name'],
										'mobile'		=> $get_address['mobile'],
										'phone'			=> $get_address['phone'],
										'zipcode'		=> $get_address['zipcode'],
										'address'		=> $get_address['address'],
										'area'			=> get_location_name($get_address['location']),
										'city'			=> get_city_name($get_address['city']),
										'state'			=> get_state_name($get_address['state']),
										'country'		=> get_country_name($get_address['country']),	
										'reciv_date'	=> $this->config->item('config.date.time')											
										);
					$posted_data = $this->security->xss_clean($posted_data);
					$insert_id = $this->pages_model->safe_insert('tbl_temp_delivery_address',$posted_data,FALSE);
		
					foreach($cart_data as $keyC=>$val_C)
					{
						$post_cart_data = array(
												'tmp_delivery_id' 	=> $insert_id,
												'address_id'		=> $address_id,
												'cart_id'			=> $val_C['cart_id'],
												'product_id'		=> $val_C['pid'],												
												'qty'				=> $val_C['qty'],
												'delivery_time'		=> $val_C['time'],
												);
						$post_cart_data = $this->security->xss_clean($post_cart_data);
						$this->pages_model->safe_insert('tbl_temp_delivery_address_cart',$post_cart_data,FALSE);
					}
				}										
			}
		}
	}

		public function test_whatsapp(){
			$this->load->library('Whatsapp_integration');
			try{
				$template_name = "product_canceled";
				$mobileno="9999728671,7827313292";
				switch($template_name){
					case 'product_canceled':
					case 'product_readyfordispatch':
					case 'product_dispatched':
					case 'product_delivered':
					case 'product_rejected':
					$params_whatsapp = array(
											'to'=>$mobileno,
											'message_template'=>array(
																			'template_name'=>$template_name,
																			'template_data'=>array(
																													array('data'=>'Joe Doe'),
																													array('data'=>"Glassy Icon"),
																													array('data'=>"WLO223"),
																													array('data'=>'29 Apr 2021'),
																													array('data'=>$this->config->item('site_name'))
																												)
																		)
										);
					$this->whatsapp_integration->send_message($params_whatsapp);
					break;
					case 'order_confirmation':
						$params_whatsapp = array(
										'to'=>$mobileno,
										'message_template'=>array(
																		'template_name'=>'order_confirmation'
																	)
									);
							$this->whatsapp_integration->send_message($params_whatsapp);
					break;
					case 'transaction_failed':
						try{
								$params_whatsapp = array(
												'to'=>$mobileno,
												'message_template'=>array(
																				'template_name'=>'transaction_failed'
																			)
											);
									$this->whatsapp_integration->send_message($params_whatsapp);
							}catch(Exception $e){
								//trace($e->getMessge());
							}
					break;
					case 'order_delivered':
						$params_whatsapp = array(
										'to'=>$mobileno,
										'message_template'=>array(
																		'template_name'=>'order_delivered',
																		'template_data'=>array(
																													array('data'=>'WL-12767')
																												)
																	)
									);
							$this->whatsapp_integration->send_message($params_whatsapp);
					break;
					case 'registration_confirmation':
						try{
							$params_whatsapp = array(
											'to'=>$mobileno,
											'message_template'=>array(
																			'template_name'=>'registration_confirmation',
																			'template_data'=>array(
																													array('data'=>$this->config->item('site_name'))
																												)
																		)
										);
								$this->whatsapp_integration->send_message($params_whatsapp);
						}catch(Exception $e){
							//trace($e->getMessge());
						}
					break;
				}
			}catch(Exception $e){
				trace($e->getMessge());
			}
		}

		public function test_whatsapp_request_data(){
			$this->load->library('Whatsapp_integration');
			try{
				$params = array(
										'debug'=>TRUE,
										'request_id'=>"556af21e-bd59-4c00-aea5-b773c688044e",
									);
				$this->whatsapp_integration->get_outbound_msg_data($params);
			}catch(Exception $e){
				trace($e->getMessge());
			}
		}

		public function test_answer(){
			$question_bank = array(
										array(
											'section_id'=>1,
											'questions'=>array(
																				array(
																					'question_id'=>17,
																					'user_answer'=>65
																				)
																				/*array(
																					'question_id'=>18,
																					'user_answer'=>''
																				)*/
																			)
										),
										/*array(
											'section_id'=>2,
											'questions'=>array(
																				array(
																					'question_id'=>11,
																					'user_answer'=>41
																				),
																				array(
																					'question_id'=>12,
																					'user_answer'=>''
																				),
																				array(
																					'question_id'=>14,
																					'user_answer'=>52
																				),
																				array(
																					'question_id'=>15,
																					'user_answer'=>59
																				)
																				array(
																					'question_id'=>16,
																					'user_answer'=>''
																				)
																			)
										),*/
										array(
											'section_id'=>3,
											'questions'=>array(
																				array(
																					'question_id'=>5,
																					'user_answer'=>18
																				),
																				/*array(
																					'question_id'=>6,
																					'user_answer'=>22
																				),*/
																				array(
																					'question_id'=>9,
																					'user_answer'=>''
																				)
																			)
										)
									);

				/*$question_bank = array(
										array(
											'section_id'=>8,
											'questions'=>array(
																				array(
																					'question_id'=>16,
																					'user_answer'=>63,
																					'is_visited'=>1,
																					'is_review'=>0,
																				),
																				array(
																					'question_id'=>17,
																					'user_answer'=>'',
																					'is_visited'=>1,
																					'is_review'=>0,
																				),
																				array(
																					'question_id'=>18,
																					'user_answer'=>69,
																					'is_visited'=>1,
																					'is_review'=>1,
																				),
																				array(
																					'question_id'=>19,
																					'user_answer'=>75,
																					'is_visited'=>1,
																					'is_review'=>0
																				),
																				array(
																					'question_id'=>20,
																					'user_answer'=>78,
																					'is_visited'=>1
																				)
																			)
										),
										array(
											'section_id'=>9,
											'questions'=>array(
																				array(
																					'question_id'=>7,
																					'user_answer'=>25,
																					'is_visited'=>1
																				),
																				array(
																					'question_id'=>8,
																					'user_answer'=>'',
																					'is_visited'=>1
																				),
																				array(
																					'question_id'=>9,
																					'user_answer'=>33,
																					'is_visited'=>1
																				),
																				array(
																					'question_id'=>10,
																					'user_answer'=>39,
																					'is_visited'=>1,
																					'is_review'=>1
																				),
																				array(
																					'question_id'=>11,
																					'user_answer'=>'',
																					'is_visited'=>0
																				)
																			)
										)
									);*/

				$param_req=array(
											'user_id'=>2,
											'mt_id'=>1,
											'package_id'=>1,
											'start_test_type'=>'general',
											'package_type'=>'mock_test_package',
											'question_bank'=>json_encode($question_bank)
										);

				$post_data = http_build_query($param_req);
				//echo $post_data;die;
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => base_url().'apis/test_series/mock_test_finish',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_SSL_VERIFYHOST=>0,
					CURLOPT_SSL_VERIFYPEER=>0,
					CURLOPT_POSTFIELDS =>$post_data,
					CURLOPT_HTTPHEADER => array(
						 "Content-Type: application/x-www-form-urlencoded",
						 "AUTHORIZATION: MLt!45ai#17xA68"
					  )
					));

				echo $response = curl_exec($curl);
				curl_close($curl);
				$response_obj = json_decode($response);
				trace($response_obj);
		}

		public function test_live_answer(){
			$question_bank = array(
										array(
											'section_id'=>1,
											'questions'=>array(
														array(
															'question_id'=>17,
															'user_answer'=>65,
															'is_visited'=>1
														),
														array(
															'question_id'=>18,
															'user_answer'=>''
														)
													)
										),
										array(
											'section_id'=>2,
											'questions'=>array(
															array(
																'question_id'=>11,
																'user_answer'=>41,
																'is_visited'=>1
															),
															array(
																'question_id'=>12,
																'user_answer'=>''
															),
															array(
																'question_id'=>14,
																'user_answer'=>52,
																'is_visited'=>1
															),
															array(
																'question_id'=>15,
																'user_answer'=>59,
																'is_visited'=>1,
															),
															array(
																'question_id'=>16,
																'user_answer'=>'',
																'is_visited'=>1
															)
														)
										),
										array(
											'section_id'=>3,
											'questions'=>array(
														array(
															'question_id'=>5,
															'user_answer'=>18,
															'is_visited'=>1
														),
														array(
															'question_id'=>6,
															'user_answer'=>22,
															'is_visited'=>1,
															'is_review'=>1
														),
														array(
															'question_id'=>9,
															'user_answer'=>''
														)
													)
										)
									);

				$param_req=array(
								'user_id'=>2,
								'live_mt_id'=>2,
								'lt_test_id'=>7,
								'start_test_type'=>'live',
								'question_bank'=>json_encode($question_bank)
							);

				$post_data = http_build_query($param_req);
				//$arr = explode('&',urldecode($post_data));
				//trace($post_data);
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => base_url().'apis/test_series/mock_test_finish',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_SSL_VERIFYHOST=>0,
					CURLOPT_SSL_VERIFYPEER=>0,
					CURLOPT_POSTFIELDS =>$post_data,
					CURLOPT_HTTPHEADER => array(
						 "Content-Type: application/x-www-form-urlencoded",
						 "AUTHORIZATION: MLt!45ai#17xA68"
					  )
					));

				echo $response = curl_exec($curl);
				curl_close($curl);
				$response_obj = json_decode($response);
				trace($response_obj);
		}

		public function test_sms(){
			$this->load->library('sms_integration');
			$params = array(
							'to'=>9999728671,
							'debug'=>TRUE
						);
			$this->sms_integration->send_message($params);
		}

		public function update_rank_general(){
			$res_mt = $this->db->select("DISTINCT(ref_mt_id)")->get_where('wl_mock_test_attempt_master',array('ref_mt_type'=>1))->result_array();
			if(!empty($res_mt)){
				foreach($res_mt as $val){
					$this->db->query("SET @rp=0");
					$qry_update = "UPDATE wl_user_mt_attempt SET user_rank=@rp:=@rp+1 WHERE ref_mt_set_id='".$val['ref_mt_id']."'  AND mt_type_master=1 AND is_pass=1 ORDER BY total_cent_attained DESC,user_mt_attempt_id";
					//echo '<br>';
					$this->db->query($qry_update);
				}
			}
		}

		public function test_notification(){
			$this->load->helper('push_notification');
			if($this->input->get('hint')!=''){
				$debug = FALSE;
				$hint = $this->input->get('hint',TRUE);
				$user_id = 5;
				$message_title = "Course Title";
				$message_desc = "Course Description Course Description Course Description Course Description";
				switch($hint){
					case 'wallet':
						$debug = TRUE;
						$notification_url_params = array('page'=>'wallet','id1'=>'','id2'=>'');
					break;
					case 'live_class_dtls':
						$notification_url_params = array('page'=>'live_class_dtls','id1'=>15,'id2'=>'');
					break;
					case 'live_mt_dtls':
						$notification_url_params = array('page'=>'live_mt_dtls','id1'=>5,'id2'=>'');
					break;
					case 'live_result_dtls':
						$notification_url_params = array('page'=>'live_result_dtls','id1'=>299,'id2'=>296);
					break;
					case 'order_purchased':
						$notification_url_params = array('page'=>'order_purchased','id1'=>23,'id2'=>'91exB60d0216f4d8c6');
					break;
					case 'video_course_detail':
						$notification_url_params = array('page'=>'video_course_detail','id1'=>6,'id2'=>'');
					break;
					case 'course_detail':
						$notification_url_params = array('page'=>'course_detail','id1'=>12,'id2'=>'');
					break;
					case 'mtp_detail':
						$notification_url_params = array('page'=>'mtp_detail','id1'=>4,'id2'=>'');
					break;
					case 'notes_detail':
						$notification_url_params = array('page'=>'notes_detail','id1'=>5,'id2'=>'');
					break;
					case 'subscription':
						$notification_url_params = array('page'=>'subscription','id1'=>1,'id2'=>'');
					break;
					case 'nf_course_by_expired':
						$notification_url_params = array('url_hint'=>'nf_course_by_expired','url_params'=>array('prod_type'=>1,'detail_id'=>12));
						$notification_url_params = $this->custom_notification->format_notification_params($notification_url_params);
						//trace($notification_url_params);
						//die;
					break;
					default:
						$notification_url_params = array('page'=>$hint,'id1'=>'','id2'=>'');
				}
				$msg_icon='5ecfa29fa60e029b3f323492aa12a2ec-brain-art-the-brain.jpg';
				$notification_img_path=base_url().'uploaded_files/notifications/'.$msg_icon;
				
				$push_message_icon="";
				$message_array  = array(
											"message_title"=>$message_title,
											"message"=>$message_desc,
											//'message_image'=>$msg_icon,
											'message_image'=>$notification_img_path,
											//"icon"=>$push_message_icon,
											"notification_url_params"=>$notification_url_params,
											"pem_file" => "pushCert_JGRider",
											"debug"=>$debug	
											);
				set_apps_notification($user_id,$message_array);
			}
			$this->load->view('test_notification');
		}
	
}

/* End of file pages.php */