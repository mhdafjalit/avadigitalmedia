<?php
class User extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('date','language','string','cookie','file'));
		$this->load->model(array('user/user_model'));
		$this->load->library(array('safe_encrypt','securimage_library','Auth','Dmailer'));
		$this->form_validation->set_error_delimiters("<div class='required'>","</div>");

		$rf_session = $this->session->userdata('ref');

		if( $this->input->get('ref')!="")
		{
			$curr_land_url =  current_url_query_string();
			if(preg_match("~ref=(.+)~",$curr_land_url,$matches))
			{
				$ref_url = $matches[1];
				$ref_url = urldecode($ref_url);
			}
			else
			{
				$ref_url = $this->input->get('ref');
			}
			$this->session->set_userdata( array('ref'=>$ref_url ) );
		}

	}

	public function index()
	{
		$this->login();
	}

	public function forgotten_password()
	{
		if ( $this->input->post('forgotme')!="")
		{
			$email = $this->input->post('email',TRUE);
			$this->form_validation->set_rules('email', ' Email ID', 'required|valid_email');
			// $this->form_validation->set_rules('verification_code','Verification code','trim|required|valid_captcha_code[fp]');
			
			if ($this->form_validation->run() == TRUE)
			{
				$condtion = array('field'=>"user_name,first_name,last_name,customers_id",'condition'=>"user_name ='$email' AND status ='1' ");
				$res_user = $this->user_model->find('wl_customers',$condtion);
				if( is_array($res_user) && !empty($res_user))
				{
					$unique_ref_code="";
					$is_unique_ref_code=0;
					$ix=1;
					$max_try = 20;
					while(!$unique_ref_code){
						$unique_ref_code=random_string('alnum',10);
						$qry="SELECT COUNT(*) as gtotal FROM wl_reset_pwd_link WHERE code='".$this->db->escape_str($unique_ref_code)."'";
						$res = $this->db->query($qry)->row_array();
						$count = (int) $res['gtotal'];
						if($count==0){
							$is_unique_ref_code=1;
							break;
						}elseif($ix>=$max_try){
							break;
						}
						$ix++;
					}

					if(!$is_unique_ref_code){
						$this->session->set_userdata(array('msg_type'=>'error'));
						$this->session->set_flashdata('error',"Unable to generate unique code");
						redirect(site_url('forgot-password'), '');
						exit;
					}
					$expire=date("Y-m-d H:i:s",strtotime("+30 minute"));
					/* Delete Related Old Link */
					$this->user_model->safe_delete('wl_reset_pwd_link',array('user_id'=>$res_user['customers_id']),FALSE);
					$reset_data_array = array(
										'user_id'  => $res_user['customers_id'],
										'code'         => $unique_ref_code,
										'expire'=>$expire,
										'added'=>$this->config->item('config.date.time')
										);
					$this->user_model->safe_insert('wl_reset_pwd_link',$reset_data_array,FALSE);

					$first_name  = $res_user['first_name'];
					$last_name   = $res_user['last_name'];
					$username    = $res_user['user_name'];
					/* Send  mail to user */

					$content    =  get_content('wl_auto_respond_mails','2');
					$subject    =  $content->email_subject;
					$subject			=	str_replace('{site_name}',$this->config->item('site_name'),$subject);
					$body       =  $content->email_content;

					$reset_link = '<a href="'.base_url().'user/reset_pwd/'.$unique_ref_code.'">Click here to reset </a>';

					$validity_text = "Reset link will be only valid for 30 minutes";

					$name = ucwords($first_name)." ".ucwords($last_name);
					$body			=	str_replace('{mem_name}',$name,$body);
					$body			=	str_replace('{username}',$username,$body);
					$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
					$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
					$body			=	str_replace('{url}',base_url(),$body);
					$body			=	str_replace('{reset_link}',$reset_link,$body);
					$body			=	str_replace('{validity_text}',$validity_text,$body);


					$mail_conf =  array(
					'subject'=>$subject,
					'to_email'=>$username,
					'from_email'=>$this->admin_info->admin_email,
					'from_name'=> $this->config->item('site_name'),
					'body_part'=>$body
					);

					$this->dmailer->mail_notify($mail_conf);
					$reset_pwd_msg = "A reset link has been sent to your entered email. $validity_text";
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success',$reset_pwd_msg);
					redirect(site_url('forgot-password'), '');
				}else
				{
					$this->session->set_userdata(array('msg_type'=>'error'));
					$this->session->set_flashdata('error',$this->config->item('email_not_exist'));
					redirect(site_url('forgot-password'), '');
				}
			}
		}
		$this->header_menu_section ='forgot_password';
		$data['heading_title'] = "Forgot Password?";
		$this->load->view('users_forgot_password',$data);
	}

	public function reset_pwd()
	{
		$err = 1;
	    $code = $this->uri->segment(3);
		 $page_heading = $this->is_create_pwd ?? "Reset Password";
		 if($code!=''){
			 $res_user=$this->db->select("user_id,expire,id")->get_where('wl_reset_pwd_link',array('code'=>$code))->row_array();
			 if(is_array($res_user) && !empty($res_user)){
				 $expire_time = strtotime($res_user['expire']);
				 $cur_time = strtotime($this->config->item('config.date.time'));
				 $skip_expiry=$this->skip_expiry ?? 0;
				 if(!$skip_expiry && $cur_time>$expire_time){
						$err = 1;
						$err_msg = "Reset link has been expired. Please resend using forgot password";
				 }else{
					$err=0;
					if($this->input->post('sbt_btn')!=''){
						$this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[20]|valid_password');
						$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|max_length[20]|matches[password]');
						if($this->form_validation->run() == TRUE){
							$posted_password = $this->input->post('password');
							$password  		=  $this->safe_encrypt->encode($posted_password);
							/*reset as per scenario*/
							$res_register = $this->db->select("password,first_name,last_name,user_name")->get_where('wl_customers',array('customers_id'=>$res_user['user_id']))->row_array();
							if(is_array($res_register) && !empty($res_register)){
								
								$this->user_model->safe_update('wl_customers',array('password'=>$password),array('customers_id'=>$res_user['user_id']),FALSE);
							}
							//Delete Related Old Link
							$this->user_model->safe_delete('wl_reset_pwd_link',array('id'=>$res_user['id']),FALSE);
							$this->session->set_userdata(array('msg_type'=>'success'));
							$this->session->set_flashdata('success',"Password has been ". (!empty($this->is_create_pwd) ? 'created' : 'reset')." successfully.");
							redirect(site_url('login'),'');		
						}
					}
				 }
			 }else{
				  $err = 1;
					$err_msg = "Invalid request";
			 }
		 }else{
			 $err = 1;
			$err_msg = "Invalid request";
		 }
		 if($err){
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',$err_msg);
			redirect(site_url(''),'');		
		 }else{
			$data['heading'] = $page_heading;
			$this->load->view('reset_pwd',$data);
		 }
	}

	public function login()
	{
		if( $this->auth->is_user_logged_in() )
		{
			redirect('members','');
		}
		$is_xhr = $this->input->is_ajax_request();
		$preserved_ref_url = $this->preservedRefUrl(true);
		if($is_xhr)
		{
			$is_otp_login = false;
			$custom_error_flds = array();
			$member_type = $this->input->post('member_type',TRUE);
			if((($this->input->post('send_otp')=='Y') || $this->input->post('btn_resend')!=''))
			{
				$is_otp_login =true;
				$this->form_validation->set_rules('mobile_number', 'Mobile Number','trim|required|min_length[10]|max_length[12]|callback_mobile_validate['.$mem_nature.']');
			}elseif($this->input->post('action')=='verify'){
				$is_verify_otp_login =true;
				$temp_value = $this->input->post('mobile_number',TRUE);
				$this->form_validation->set_rules('mobile_number', 'Mobile Number','trim|required|min_length[10]|max_length[12]|callback_mobile_validate['.$mem_nature.']');
				$this->form_validation->set_rules('otp_code', 'OTP', "trim|required|callback_validate_otp[0~login_mobile~".$temp_value."]");
			}
			else
			{
				$this->form_validation->set_rules('user_name', 'Email ID','trim|required');
				$this->form_validation->set_rules('password', 'Password', 'trim|required');
			}

			if ($this->form_validation->run() == TRUE)
			{
				if(!empty($is_verify_otp_login)){
					$res = $this->handle_verify_otp_login($member_type);
					if(!$res['err']  ){
						$ref_redirect = $preserved_ref_url!=''  ? $preserved_ref_url : site_url('members');
						$ret_data = array('status'=>1,'redirect_url'=>$ref_redirect);
					}else{
						$ret_data = array('status'=>0,'msg'=>$res['msg']);
					}
				}elseif($is_otp_login){
					$res = $this->handle_login_with_otp();
					$ret_data = array('status'=>!$res['err'],'msg'=>$res['msg']);
					if(!$res['err'] && !$this->is_sms_active){
						$ret_data['otp'] = $res['code'];
					}
				}else{
					$res = $this->handle_login_with_username($member_type);
					if( !$res['err']  ){
						$ref_redirect = $preserved_ref_url!='' ? $preserved_ref_url : site_url('members');
						$ret_data = array('status'=>1,'redirect_url'=>$ref_redirect);
					}else{
						$ret_data = array('status'=>0,'msg'=>$res['msg']);
					}
				}
				if(!$ret_data['status'] && !empty($ret_data['msg'])){
					$ret_data['msg'] = "<div class=\"required\">".$ret_data['msg']."</div>";
				}
				echo json_encode($ret_data);
				exit;
			}
			else
			{
				$error_array = req_compose_errors($custom_error_flds);
				$ret_data = array('status'=>'0','error_flds'=>$error_array);
				echo json_encode($ret_data);
				exit;
			}
		}
		$saved_username = get_cookie('Xcnv');
		if($saved_username!=''){
			$saved_username =  $this->safe_encrypt->decode($saved_username);
		}
		$saved_pwd = get_cookie('Omn2z');
		if($saved_pwd!=''){
			$saved_pwd =  $this->safe_encrypt->decode($saved_pwd);
		}
		$data['posted_user_name'] = $saved_username;
		$data['posted_password'] = $saved_pwd;
		$data['remember'] = $saved_username!='' ? 'Y' : '';
		$data['heading_title'] = "Login";
		$this->header_menu_section ='login';
		$data['preserved_ref_url'] = $preserved_ref_url;
		$this->load->view('users_login',$data);
	}

	private function handle_login_with_username($member_type){
		$is_xhr = $this->input->is_ajax_request();
		$username  =  $this->input->post('user_name');
		$password  =  $this->input->post('password');	
		// print_r($this->input->post());die;
		$rember    =  ($this->input->post('remember')!="") ? TRUE : FALSE;

		if( $this->input->post('remember')=="Y" )
		{	
			$encoded_username  =  $this->safe_encrypt->encode($username);
			$encoded_password  =  $this->safe_encrypt->encode($password);
			set_cookie('Xcnv',$encoded_username, time()+60*60*24*30,'','/','',false,true );
			set_cookie('Omn2z',$encoded_password, time()+60*60*24*30,'','/','',false,true );
		}
		else
		{	
			delete_cookie('Xcnv');
			delete_cookie('Omn2z');
		}
		$params_verify_user =  array(
					'username'=>$username,
					'password'=>$password,
					//'member_type'=>$member_type,
					'login_type'=>'website'
				);
		$res_verify = $this->auth->verify_user($params_verify_user);
		return $res_verify;
	}

	private function handle_login_with_otp(){
		$this->load->library('otp');
		$registerId = 0;
		$mobile_number = $this->input->post('mobile_number');
		if($this->input->post('btn_resend')!=''){
			$msg = "OTP has been resent";
		}else{
			$msg = "OTP Generated";
		}
		$param_otp_mobile = array('user_id'=>$registerId,'otp_type'=>'login_mobile','temp_value'=>$mobile_number);
		$otp_res_mobile = $this->otp->generate_otp($param_otp_mobile);
		if(empty($otp_res_mobile)){
			$ret = array('err'=>1,'msg'=>"Unable to generate OTP");
		}else{
			
			$otp_mobile = $otp_res_mobile['code'];
			$sms_param['to'] = $mobile_number; 
			$sms_param['message'] = 'Your verification otp '.$otp_mobile.' for '.$this->config->item('site_name').'. Do not share with anyone.'; 
			//$this->sms_integration_uae->send_message($sms_param);
			/* End sms send to user */
			$ret = array('err'=>0,'msg'=>$msg,'code'=>$otp_mobile);
		}
		return $ret;	
	}

	private function handle_verify_otp_login($mem_nature){
		$this->load->library('otp');
		$registerId = 0;
		$mobile_number = $this->input->post('mobile_number');
		$param_otp = array('is_temp_user'=>1,'otp_type'=>'login_mobile','temp_id'=>$mobile_number);
		$del_otp_res = $this->otp->delete_otp($param_otp);
		$res_user = $this->db->select('customers_id')->get_where('wl_customers',array('mobile_number' => $mobile_number,'mem_nature'=>$mem_nature,'is_verified!='=>'1'))->row_array();
		if(!empty($res_user)){
			$where = "customers_id=".$res_user['customers_id']." "; 
			$this->user_model->safe_update('wl_customers',array('is_verified'=>'1'),$where,FALSE);
		}
		$ret_rs = $this->auth->verify_user(array('username'=>$mobile_number,'mem_nature'=>$mem_nature));
		if( $this->auth->is_user_logged_in() ){
			$ret_data = array('err'=>'0','msg'=>"Login Successful");
		}else{
			$ret_data = array('err'=>'1','msg'=>"Unable to login");
		}
		return $ret_data;

	}

	public function guest_login()
	{
		if( $this->auth->is_user_logged_in() )
		{
			redirect('cart/checkout','');
		}
		//$this->session->unset_userdata(array('ref'));
		//echo $this->session->userdata('ref');
		if ( $this->input->post('action') )
		{
			$this->form_validation->set_rules('user_name', 'Email ID','required|valid_email');
			$this->form_validation->set_rules('user_login', 'User Login', 'required');
			if($this->input->post('user_login')=="member"){
				$this->form_validation->set_rules('password', 'Password', 'required');
			}
			
			if ($this->form_validation->run() == TRUE)
			{
				$username  =  $this->input->post('user_name');

				if($this->input->post('user_login')=="guest"){					
					$status='3';
					$user=$this->user_model->create_guest_user();					
					$password=$user[2];							
					$this->session->set_userdata("guest",$user[0]);				
					redirect('cart/checkout','');
				}else{					
					$password=$this->input->post('password');	
					$status='1';
					$this->auth->verify_user($username,$password,$status);
				}
										
				$rember    =  ($this->input->post('remember')!="") ? TRUE : FALSE;
				
				if($this->input->post('remember')=="Y"){
					set_cookie('userName',$this->input->post('user_name'), time()+60*60*24*30 );
					set_cookie('pwd',$password, time()+60*60*24*30 );
				}else
				{
					delete_cookie('userName');
					delete_cookie('pwd');
				}
							
				if( $this->auth->is_user_logged_in() )
				{
					$ref = $this->session->userdata('ref');
					$this->session->unset_userdata(array('ref'));
					if( $ref !="")
					{
						redirect($ref,'');
					}else{
						redirect('cart/checkout','');
					}
				}else{
					$this->session->set_userdata(array('msg_type'=>'error'));
					$this->session->set_flashdata('error',$this->config->item('login_failed'));
					redirect('user/guest_login', '');
				}
			}else{
				$data['heading_title'] = "Login";
				$this->load->view('guest_login',$data);
			}
		}else{
			$data['heading_title'] = "Login";
			$this->load->view('guest_login',$data);
		}
	}
	
	public function logout()
	{
		$data2 = array('guest','shipping_id','coupon_id','discount_amount','coupon_usage','coupon_for','usage_count','coupon_code_sess','use_wallet_sess','shiping_address_id','ref');
		$this->session->unset_userdata($data2);
		$this->cart->destroy();
		$this->auth->logout();
		$this->session->set_userdata(array('msg_type'=>'success'));
		$this->session->set_flashdata('success',$this->config->item('member_logout'));
		redirect(site_url('login'), '');
	}

	public function thanks()
	{
		$registerId  = $this->uri->segment(3);
		$data['heading_title'] = "Verification";
		$data['user_name'] = get_db_field_value('wl_customers','user_name', array("md5(customers_id)"=>$registerId));
		$this->load->view('users_thanks',$data);
	}
	
	
	public function checkreferral()
	{
	    $referral_code = $this->input->post('referral_code');
	    $query=$this->db->query(" select refer_id from wl_referral_customers where 1 and referral_code='".$referral_code."' and refer_user='".$this->input->post('user_name')."'");
	    
	    $count=$query->num_rows();
	    if($referral_code)
	    {
	        if ($count==0 )
	        {
	            $this->form_validation->set_message('checkreferral', "Referral code does not exist");
	            return FALSE;
	        }
	        else
	        {
	            return TRUE;
	        }
	    }
	    else
	    {
	        return TRUE;
	    }
	}

	public function register(){
		if (!$this->auth->is_user_logged_in() )
		{
			if($this->input->post('btn_sbt')!=''){
				$this->process_register();
			}
			$this->header_menu_section = "register";
			$data['heading_title'] = "Register";
			$this->load->view('users_register',$data);
		}else{
			redirect('members', 'refresh');
		}
	}

	private function process_register()
	{
		$img_allow_size =  $this->config->item('allow.file.size');
		if($this->input->post('btn_sbt')!=''){
			$this->form_validation->set_rules('first_name', 'Name', 'trim|required|alpha|max_length[80]');
			$this->form_validation->set_rules('last_name', 'Last name', 'trim|alpha|max_length[30]');
			$this->form_validation->set_rules('mobile_number', 'Mobile', 'trim|required|numeric|min_length[10]|max_length[12]|callback_mobile_check');
			$this->form_validation->set_rules('user_name', 'Email ID','trim|required|valid_email|max_length[80]|callback_email_check');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[20]|valid_password');
			$this->form_validation->set_rules('confirm_password', 'Confirm password', 'required|max_length[20]|matches[password]');
			$this->form_validation->set_rules('bank_name', 'Bank Name','trim|alpha|required|max_length[50]');
			$this->form_validation->set_rules('ac_holder_name', 'Account Holder Name','trim|alpha|required|max_length[50]');
			$this->form_validation->set_rules('account_no', 'Account No.','trim|numeric|required|min_length[10]|max_length[20]');
			$this->form_validation->set_rules('ifsc_code', 'IFSC Code','trim|alpha_numeric|required|exact_length[11]|callback_validate_ifsc');
			$this->form_validation->set_rules('aadhar_doc','Aadhar Card',"file_required|file_allowed_type[pdf_image]|file_size_max[$img_allow_size]");
			$this->form_validation->set_rules('pancard_doc','PAN Card',"file_required|file_allowed_type[pdf_image]|file_size_max[$img_allow_size]");
			$this->form_validation->set_rules('bank_passbook','Bank Passbook',"file_required|file_allowed_type[pdf_image]|file_size_max[$img_allow_size]");
			//$this->form_validation->set_rules('verification_code','Verification code','trim|required|valid_captcha_code[register]');
			if ($this->form_validation->run() == TRUE)
			{
				$aadhar_doc = "";
				if( !empty($_FILES['aadhar_doc']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('aadhar_doc','members');
					if( is_array($uploaded_data)  && !empty($uploaded_data) ){
						$aadhar_doc = $uploaded_data['upload_data']['file_name'];
					}
				}
				$pancard_doc = "";
				if( !empty($_FILES['pancard_doc']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('pancard_doc','members');
					if( is_array($uploaded_data)  && !empty($uploaded_data) ){
						$pancard_doc = $uploaded_data['upload_data']['file_name'];
					}
				}
				$bank_passbook = "";
				if( !empty($_FILES['bank_passbook']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('bank_passbook','members');
					if( is_array($uploaded_data)  && !empty($uploaded_data) ){
						$bank_passbook = $uploaded_data['upload_data']['file_name'];
					}
				}
				$mem_nature = 1;
				$email = $this->input->post('user_name',TRUE);
				$actkey = md5($email."-".random_string('numeric',4));
				$password = $this->input->post('password',TRUE);
				$encoded_password  =  $this->safe_encrypt->encode($password);
				$sponsorId = generate_sponsorId();
				$posted_data = array(					
					'actkey'    			=> $actkey,
					'member_type'			=> '3', 
					'mem_nature'			=> $mem_nature, 
					'sponsor_id'			=> $sponsorId,	
					'user_name'				=> $this->input->post('user_name',TRUE), 	
					'password'				=> $encoded_password,
					'first_name'			=> $this->input->post('first_name',TRUE), 					
					'mobile_number'			=> $this->input->post('mobile_number',TRUE),
					'ac_holder_name'		=> $this->input->post('ac_holder_name',TRUE),
					'bank_name'				=> $this->input->post('bank_name',TRUE),
					'account_no'			=> $this->input->post('account_no',TRUE),
					'ifsc_code'				=> $this->input->post('ifsc_code',TRUE),
					'aadhar_doc'			=> $aadhar_doc,
					'pancard_doc'			=> $pancard_doc,
					'bank_passbook'			=> $bank_passbook,
					'status'				=> '1',
					'is_verified'			=> '1',
					'ip_address'  			=> $this->input->ip_address(),
					'account_created_date'	=> $this->config->item('config.date.time')
				);  
				// trace($posted_data);die;
				$posted_data = $this->security->xss_clean($posted_data); 
				$registerId = $this->user_model->safe_insert('wl_customers',$posted_data); 
				if($registerId > 0)
				{
					/* Send OTP to mobile
					$this->load->library('otp');
					$param_otp_mobile = array('user_id'=>$registerId,'otp_type'=>'register_mobile','temp_value'=>$this->input->post('mobile_number',TRUE));
					$otp_res_mobile = $this->otp->generate_otp($param_otp_mobile);
					*/
					$first_name  = $this->input->post('first_name',TRUE);
					$last_name   = '';
					$username    = $this->input->post('user_name',TRUE);
					$password    = $this->input->post('password',TRUE);
					$params_verify_user =  array(
										'username'=>$username,
										'password'=>$password,
										'mem_nature'=> $mem_nature,
										'login_type'=>'website'
									);
					$this->auth->verify_user($params_verify_user);
					/* Send  mail to user */
					$content    =  get_content('wl_auto_respond_mails','1');
					$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
					$body       =  $content->email_content;
					$verify_url = "<a href=".base_url()."user/verify/".$actkey.">Click here </a>";
					$name 		= ucwords($first_name)." ".ucwords($last_name);
					$body		=	str_replace('{mem_name}',$name,$body);
					$body		=	str_replace('{username}',$username,$body);
					$body		=	str_replace('{password}',$password,$body);
					$body		=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
					$body		=	str_replace('{site_name}',$this->config->item('site_name'),$body);
					$body		=	str_replace('{url}',base_url(),$body);
					$body		=	str_replace('{verification_link}',$verify_url,$body);
					$mail_conf =  array(
						'subject'=>$subject,
						'to_email'=>$this->input->post('user_name'),
						'from_email'=>$this->admin_info->admin_email,
						'from_name'=> $this->config->item('site_name'),
						'body_part'=>$body
					);

					$this->dmailer->mail_notify($mail_conf);
					/* End send  mail to user */
					$content    =  get_content('wl_auto_respond_mails','6');			
					$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
					$body       =  $content->email_content;

					$name = ucwords($first_name)." ".ucwords($last_name);

					$body			=	str_replace('{name}',$name,$body);
					$body			=	str_replace('{username}',$username,$body);
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
					// redirect('user/thanks/'.md5($registerId));
					members_direction($mem_nature);
				}
			}
		}
	}

	public function email_check()
	{
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
		$mobile_number = $this->input->post('mobile_number');
		if ($this->user_model->is_email_exits(array('mobile_number' => $mobile_number)))
		{
			$this->form_validation->set_message('mobile_check', "Mobile number already exist.");
			return FALSE;
		}else{
			return TRUE;
		}
	}

	public function mobile_validate($val,$mem_type=0)
	{
		$mobile_number = $this->input->post('mobile_number');
		if($mobile_number!=''){
			$res_user = $this->db->select('status,is_verified')->get_where('wl_customers',array('mobile_number' => $mobile_number,'mem_nature'=>$mem_type,'status!='=>'2'))->row_array();
			if(empty($res_user)){
				$this->form_validation->set_message('mobile_validate', 'This mobile number is not registered with us.');
				return FALSE;
			}else{
				if ($res_user['status']!='1') {
					$this->form_validation->set_message('mobile_validate', 'Your account is in in-active state Please conatct to administration.');
					return FALSE;
				}
				if ($mem_type>1) {
					if ($res_user['is_verified']!='1') {
						$this->form_validation->set_message('mobile_validate', 'Your account is under review. Please conatct to administration.');
						return FALSE;
					}
				}
			}
		}
		return TRUE;
	}

	public function valid_captcha_code($verification_code)
	{
		if ($this->securimage_library->check($verification_code) == true)
		{
			return TRUE;
		}else
		{
			$this->form_validation->set_message('valid_captcha_code', 'The Word verification code you have entered is invalid.');
			return FALSE;
		}
	}
	
	public function verify(){
		$err = 1;
		$actkey = $this->uri->segment(3);
		if($actkey!=''){
			$mres = get_db_single_row('wl_customers','is_verified,customers_id,app_id,temp_email',array("actkey"=>$this->db->escape_str($actkey),'status !'=>'2'));
			if(is_array($mres) && !empty($mres)){
				if($mres['temp_email']!=''){
					$app_id = $mres['app_id'];
					$posted_data = array('is_verified_mail_once'=>1,'is_verified'=>'1','user_name'=>$mres['temp_email'],'temp_email'=>'');
					$posted_data = $this->security->xss_clean($posted_data);
					$where = "customers_id=".$mres['customers_id']." "; 
					$this->user_model->safe_update('wl_customers',$posted_data,$where,FALSE);
					/*Notification*/
					$this->load->library('Custom_notification');
					$param_notification=array(
										'user_id'=>$mres['customers_id'],
										'verified_dt'=>$this->config->item('config.date.time'),
										'email'=>$mres['temp_email']
									);
					$this->custom_notification->user_email_verified($param_notification);
					/*Notification Ends*/
					$err=0;
					$msg_type="SBNYM";
				}else{
					$err=0;
					$msg_type="ASBCNM";
				}
			}else{
				$msg_type="IRQ";
			}
		}else{
			$msg_type="IRQ";
		}
		redirect("user/verify_response/$msg_type/".$actkey);
	}

	public function verify_mobile()
	{
		$logged_in_user_id = (int) $this->session->userdata('user_id');
		$preserved_ref_url = $this->preservedRefUrl(true);
		$ref_qs_redirect = $preserved_ref_url!=''  ? '?ref='.$preserved_ref_url : '';
		if($logged_in_user_id==0){
			$user_id = $this->uri->rsegment(3);
			$this->cust_res = get_db_single_row("wl_customers","customers_id, mobile_number, first_name,last_name,password,actkey,is_verified_mobile,temp_mobile"," AND md5(customers_id) ='".$user_id."' ");
		}else{
			$this->cust_res = $this->mres;
		}
		
		if(is_array($this->cust_res) && !empty($this->cust_res))
		{
			$this->load->library('otp');
			$is_xhr = $this->input->is_ajax_request();
			
			if($this->cust_res['is_verified_mobile']==0){
				$this->otp_type = 'register_mobile';
				$use_db_fld = "mobile_number";
			}else{
				$this->otp_type = 'update_mobile';
				$use_db_fld = "mobile_number";
			}
			if($this->input->post('update_mobile')=='Y')
			{
				$this->_handleMobileUpdate();
			}elseif($this->input->post('btn_resend')=='Y'){
				$this->_handleResendOTP();
			}elseif ( $this->input->post('action') == 'verify' ){
				$this->_handleVerifyOTP();
			}
			if($this->cust_res['is_verified_mobile']==1 && empty($this->cust_res['temp_mobile'])){
				if($is_xhr){
					$error_array = array('update_fld'=>'You are verified. Please refresh the page');
					$ret_data = array('status'=>'0','error_flds'=>$error_array);
					echo json_encode($ret_data);
					exit;
				}else{
					// redirect('members');
				}
			}
			$data['page_heading'] = "Verify";
			$data['verify_type'] = $this->otp_type;
			$data['cust_res'] = $this->cust_res;
			$data['preserved_ref_url'] = $preserved_ref_url;
			if(!$this->is_sms_active){
				$param_otp_mobile = array('user_id'=>$this->cust_res['customers_id'],'otp_type'=>$this->otp_type);
				$last_otp_status = $this->otp->check_last_otp_status($param_otp_mobile);
				$otp_mobile = $last_otp_status['code'];
				$data['otp_mobile'] = $otp_mobile;
			}
			$data['update_fld'] = $this->cust_res[$use_db_fld];
			$data['fld_type'] = 'mobile';
			with_no_cache();
			$this->load->view('user/verify_otp',$data);
		}
		else
		{
			redirect('user/login','');
		}
	}

	private function _handleResendOTP(){
		$process_via = '';
		switch($this->otp_type){
			case 'register_mobile':
				$temp_value = $this->cust_res['mobile_number'];
				$process_via = 'sms';
			break;
			case 'update_mobile':
				$temp_value = $this->cust_res['temp_mobile'];
				$process_via = 'sms';
			break;
			case 'register_email':
				$temp_value = $this->cust_res['user_name'];
				$process_via = 'email';
			break;
			case 'update_email':
				$temp_value = $this->cust_res['temp_email'];
				$process_via = 'email';
			break;
			default:
			 $ret_data = array('status'=>'0','msg'=>"<div class=\"required\">Error sending code</div>");
			echo json_encode($ret_data);
			exit;

		}

		$param_otp = array('user_id'=>$this->cust_res['customers_id'],'otp_type'=>$this->otp_type,'temp_value'=>$temp_value);
		$otp_res = $this->otp->generate_otp($param_otp);
		$otp_code = $otp_res['code'];
		if($otp_code!=''){
			if($process_via=='sms'){
				//Send sms here
			}else{
				//Shoot OTP to Email 
			}
			$ret_data = array('status'=>'1','otp'=>$otp_code,'msg'=>"<div style=\"color:#177b33;font-size:14px;\">OTP has been resent</div>");
			echo json_encode($ret_data);
			exit;
		}else{
			$ret_data = array('status'=>'0','msg'=>"<div class=\"required\">Error sending code</div>");
			echo json_encode($ret_data);
			exit;
		}
	}

	private function _handleVerifyOTP(){
		$is_xhr = $this->input->is_ajax_request();
		$verified_field = '';
		$swap_field="";
		$tmp_field="";
		switch($this->otp_type){
			case 'register_mobile':
				$temp_value = $this->cust_res['mobile_number'];
				$verified_field = 'is_verified_mobile';
			break;
			case 'update_mobile':
				$temp_value = $this->cust_res['temp_mobile'];
				$verified_field = 'is_verified_mobile';
				$swap_field = 'mobile_number';
				$tmp_field = 'temp_mobile';
			break;
			case 'register_email':
				$temp_value = $this->cust_res['user_name'];
				$verified_field = 'is_verified';
			break;
			case 'update_email':
				$temp_value = $this->cust_res['temp_email'];
				$verified_field = 'is_verified';
				$swap_field = 'user_name';
				$tmp_field = 'temp_email';
			break;
			default:
			 $ret_data = array('status'=>'0','msg'=>"<div class=\"required\">Error sending code</div>");
			echo json_encode($ret_data);
			exit;

		}
		$this->form_validation->set_rules('otp_code', 'OTP', "trim|required|callback_validate_otp[".$this->cust_res['customers_id']."~".$this->otp_type."~".$temp_value."]");
		if ($this->form_validation->run() == TRUE)
		{
			$posted_user_data = array(
				$verified_field   => '1'
				);
			if(!empty($swap_field) && !empty($tmp_field)){
				$posted_user_data[$swap_field] = $temp_value;
				$posted_user_data[$tmp_field]='';
			}
			$posted_user_data = $this->security->xss_clean($posted_user_data);
			$where       = "customers_id = '".$this->cust_res['customers_id']."'";
			$this->user_model->safe_update('wl_customers',$posted_user_data,$where,FALSE);
			$param_otp = array('user_id'=>$this->cust_res['customers_id'],'otp_type'=>$this->otp_type);
			$del_otp_res = $this->otp->delete_otp($param_otp);

			/*Process Response*/
			$logged_in_user_id = (int) $this->session->userdata('user_id');
			$msg = $verified_field=='is_verified_mobile' ? 'Mobile no. has been verified successfully' : 'Email has been verified successfully';
			$msg_code = $verified_field=='is_verified_mobile' ? 'SBNYM' : 'ASBCNM';
			if($logged_in_user_id==0){
				$password = $this->safe_encrypt->decode($this->cust_res['password']);
				$ret_rs = $this->auth->verify_user(array('username'=>$temp_value,'password'=>$password));
				if( $this->auth->is_user_logged_in() ){
					$ref = site_url('user/user_thanks/'.$msg_code.'/'.$this->cust_res['actkey']);
					$ret_data = array('status'=>'1','return_url'=>$ref,'msg'=>$msg);
				}else{
					$error_array=array('otp_code'=>$this->config->item('login_failed'));
					$ret_data = array('status'=>'0','error_flds'=>$error_array);
				}
			}else{
				$this->session->set_userdata($swap_field,$temp_value);
				$ref = site_url('members/edit_account/');
				$ret_data = array('status'=>'1','return_url'=>$ref,'msg'=>$msg);
			}
			echo json_encode($ret_data);
			exit;
		}else{
			if($is_xhr){
				$error_array=array();
				$err_frm_flds = $this->form_validation->error_array();
				if(is_array($err_frm_flds)){
					foreach($err_frm_flds as $key=>$val)
					{
						$error_array[$key] = $val;
					}
				}
				$ret_data = array('status'=>'0','error_flds'=>$error_array);
				echo json_encode($ret_data);
				exit;
			}
		}

	}

	public function verify_response(){
		$user_exists =0;
		$actkey = $this->uri->segment(4);
		$response_type = $this->uri->segment(3);
		if($actkey!=''){
			$mres       = get_db_single_row('wl_customers','is_verified,customers_id',array("actkey"=>$this->db->escape_str($actkey),'is_verified'=>'1','status!'=>'2'));
			if(is_array($mres) && !empty($mres)){
				$user_exists = 1;
				if(in_array($response_type,array('IRQ'))){
					$response_type = "SBNYM";
				}
			}else{
				$response_type = "IRQ";
			}
		}else{
			if(in_array($response_type,array('SBNYM','ASBCNM'))){
				$response_type = "IRQ";
			}
		}
		switch($response_type){
			case 'SBNYM':
				$msg = lang('acc_verify_succ_msg');
			break;
			case 'ASBCNM':
				$msg = lang('acc_verify_already_succ_msg');
			break;
			case 'IRQ':
				$msg = lang('verify_inv_req_msg');
			break;
			default:
				if($user_exists){
					$msg = lang('acc_verify_succ_msg');
				}else{
					$msg = lang('verify_inv_req_msg');
				}
		}
		$data['heading_title'] = "Thanks";
		$data['msg'] = $msg;
		$this->load->view('users_thanks',$data);
	}
	
	public function facebook_callback()
	{
		set_include_path(FCROOT.'socialauth/src/'.PATH_SEPARATOR.get_include_path());
		require_once FCROOT.'socialauth/src/SocialAuth.php';
		
		if (!empty($_GET['type']))
		{
			$cookieArr = array();
			switch ($_GET['type'])
			{
				case 'facebook':
				
				$facebookObj = SocialAuth::init('facebook');
				$facebookInfo = $facebookObj->getUser();
				if ($facebookInfo)
				{
					try {
							$url = '';
							$fields = SocialAuth::getConfig('facebook', 'fields');
							if (!empty($fields)) 
							{
								$url = '?fields=' . $fields;
							}
							
							$facebookUserInfo = $facebookObj->api('/me' . $url);
							
							$dataArr = array(
		                      'type' => 'facebook',
		                      'name' => $facebookUserInfo['name'],
		                      'email' => $facebookUserInfo['email']
		                  );
		                  
		           			//trace($dataArr);exit;
		           
				            $name				=$dataArr['name'];
				            $name_arr		=@explode(" ",$name);
				            $first_name	=$name_arr[0];
				            $last_name	=$name_arr[1];
						    $email			=$dataArr['email'];
						    $password_rand		=random_string('alnum', 8);
						    $password 	= $this->safe_encrypt->encode($password_rand);
						    
						    $where ="status !='2' and user_name ='".$email."'";
						    $this->db->from('wl_customers');
							$this->db->where($where);	
							$query = $this->db->get();
							if ($query->num_rows() == 1)
							{
							
							$mdtl=$query->row();
							$name=ucwords($mdtl->first_name.' '.$mdtl->last_name);
							$data = array(
								'user_id' 			=>$mdtl->customers_id,
								'name'					 =>$name,
								'username' 			=>$mdtl->user_name,
								'first_name'		=>$mdtl->first_name,
								'last_name'			=>$mdtl->last_name,							
								'is_blocked'		=>$mdtl->is_blocked,	
								'blocked_time'	=>$mdtl->block_time,						
								'logged_in' => TRUE
													);
							$this->session->set_userdata($data);
							
						}
						else
						{
							$updId ="";
							$data=array(
							'user_name'							=>$email,
							'password'							=>$password,	
							'first_name'						=>$first_name,
							'last_name'							=>$last_name,
							'actkey'            		=>md5($email),
							'account_created_date'	=>$this->config->item('config.date.time'),
							'current_login'    			=>$this->config->item('config.date.time'),
							'status'								=>'1',
							'login_type'						=>'facebook',
							'is_verified'						=>'1',
							'ip_address'  					=>$this->input->ip_address()
															);
									
							$insId =  $this->user_model->safe_insert('wl_customers',$data,FALSE);
					
							$data1 = array(
								'user_id'=>$insId,
								'name'=>ucwords($name),
								'login_type'=>'facebook',
								'username'=>$email,
								'first_name'=>$first_name,
								'last_name'=>$last_name,
								'logged_in' => TRUE
							);
														
							$this->session->set_userdata($data1);
								
								
							if( $insId > 0 )
							{
								$billing_array = array
								(
								'customer_id'  =>$insId,
								'reciv_date'  => $this->config->item('config.date.time'),
								'address_type' =>'Bill',
								'default_status'=>'Y'
								);
								
								$bill_Id = get_db_field_value("wl_customers_address_book",'address_id',array("customer_id"=>$insId,"default_status"=>"Y","address_type"=>"Bill"));		
								
								if($bill_Id ==""){
								
									$bill_Id =  $this->user_model->safe_insert('wl_customers_address_book',$billing_array,FALSE);
							  }else{			   
								   $this->safe_update('wl_customers_address_book',$billing_array,"address_id = '". $bill_Id."'",FALSE);
								}
							
								if( $bill_Id > 0)
								{
									if( $is_same_bill_ship =='Y')
									{
										$shipping_array = array
										(
										'customer_id'  =>$insId,
										'reciv_date'  => $this->config->item('config.date.time'),
										'address_type' =>'Ship',
										'default_status'=>'Y'
										);
									}else
									{
										$shipping_array =  array
										(
										'customer_id'  =>$insId,
										'reciv_date'  => $this->config->item('config.date.time'),
										'address_type' =>'Ship',
										'default_status'=>'Y'
										);
									}
									
									if($updId ==""){
										$this->user_model->safe_insert('wl_customers_address_book',$shipping_array,FALSE);
								  }else{	
									  $this->user_model->safe_update('wl_customers_address_book',$shipping_array,"customer_id = '".$insId."' AND default_status='Y' AND address_type ='Ship'",FALSE);
								  }
								}
							}
							
							/* Send  mail to user */
				
							$content    =  get_content('wl_auto_respond_mails','1');
							$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
							$body       =  $content->email_content;
							$verify_url = "<a href=".base_url()."users/verify/".md5($registerId).">Click here </a>";
							
							$name = ucwords($first_name)." ".ucwords($last_name);
		
							$body			=	str_replace('{mem_name}',$name,$body);
							$body			=	str_replace('{username}',$email,$body);
							$body			=	str_replace('{password}',$password_rand,$body);
							$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
							$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
							$body			=	str_replace('{url}',base_url(),$body);
							$body			=	str_replace('{link}',$verify_url,$body);
								
							$mail_conf =  array(
							'subject'=>$subject,
							'to_email'=>$email,
							'from_email'=>$this->admin_info->admin_email,
							'from_name'=> $this->config->item('site_name'),
							'body_part'=>$body
							);
		
							$this->dmailer->mail_notify($mail_conf);
							
						}
							
						//Redirect main page for user data ceck from db
						SocialAuth::redirectParentWindow('facebook', $dataArr, $_COOKIE['ref']);
					}
					catch (FacebookApiException $e)
					{
						
						error_log($e);
						$facebookInfo = null;
					}
				}
				break;
			
				case 'google':
					$googleObj = SocialAuth::init('google');
	            if ($googleObj->validate()) {
	                $identity = $googleObj->identity;
	                $attributes = $googleObj->getAttributes();
	                $email = $attributes['contact/email'];
	                $first_name = $attributes['namePerson/first'];
	                $last_name = $attributes['namePerson/last'];
	            }
		               
	            $dataArr = array(
	                'type' => 'google',
	                'name' => $first_name . ' ' . $last_name,
	                'first_name'=>$first_name,
	                'last_name'=>$last_name,
	                'email' => $email
	            );
	        
	            $name						=$dataArr['name'];
	            $first_name			=$dataArr['first_name'];
	            $last_name			=$dataArr['last_name'];
	            $email					=$dataArr['email'];
	            $password_rand				=random_string('alnum', 8);
	            $password 			= $this->safe_encrypt->encode($password_rand);
	            
	            $where="status !='2' and user_name ='".$email."'";
	            
	            $this->db->from('wl_customers');
	            $this->db->where($where);
	            $query = $this->db->get();
	            
	            if ($query->num_rows() == 1)
	            {
	            $mdtl=$query->row();
	            
	            $name=ucwords($mdtl->first_name.' '.$mdtl->last_name);
	            $data = array(
            							'user_id' 			=>$mdtl->customers_id,
            							'name'					=>$name,
													'username' 			=>$mdtl->user_name,
													'first_name'		=>$mdtl->first_name,
													'last_name'			=>$mdtl->last_name,							
													'is_blocked'		=>$mdtl->is_blocked,	
													'blocked_time'	=>$mdtl->block_time,						
													'logged_in' => TRUE
            							);
            	$this->session->set_userdata($data);
            
           }
           else
           {
	           $updId ="";
	           $data=array(
					'user_name'							=>$email,
					'password'							=>$password,	
					'first_name'						=>$first_name,
					'last_name'							=>$last_name,
					'actkey'            		=>md5($email),
					'account_created_date'	=>$this->config->item('config.date.time'),
					'current_login'    			=>$this->config->item('config.date.time'),
					'status'								=>'1',
					'login_type'						=>'google',
					'is_verified'						=>'1',
					'ip_address'  					=>$this->input->ip_address()
					);
				    						
				    $insId =  $this->user_model->safe_insert('wl_customers',$data,FALSE);
				    
				    $data1 = array(
							'user_id'=>$insId,
							'name'=>ucwords($name),
							'login_type'=>'google',
							'username'=>$email,
							'first_name'=>$first_name,
							'last_name'=>$last_name,
							'logged_in' => TRUE
						);
												
						$this->session->set_userdata($data1);
						
						if( $insId > 0 )
						{
							$billing_array = array
							(
							'customer_id'  =>$insId,
							'reciv_date'  => $this->config->item('config.date.time'),
							'address_type' =>'Bill',
							'default_status'=>'Y'
							);
							
							$bill_Id = get_db_field_value("wl_customers_address_book",'address_id',array("customer_id"=>$insId,"default_status"=>"Y","address_type"=>"Bill"));		
							
							if($bill_Id ==""){
							
								$bill_Id =  $this->user_model->safe_insert('wl_customers_address_book',$billing_array,FALSE);
						  }else{			   
							   $this->safe_update('wl_customers_address_book',$billing_array,"address_id = '". $bill_Id."'",FALSE);
							}
						
							if( $bill_Id > 0)
							{
								if( $is_same_bill_ship =='Y')
								{
									$shipping_array = array
									(
									'customer_id'  =>$insId,
									'reciv_date'  => $this->config->item('config.date.time'),
									'address_type' =>'Ship',
									'default_status'=>'Y'
									);
								}else
								{
									$shipping_array =  array
									(
									'customer_id'  =>$insId,
									'reciv_date'  => $this->config->item('config.date.time'),
									'address_type' =>'Ship',
									'default_status'=>'Y'
									);
								}
								
								if($updId ==""){
									$this->user_model->safe_insert('wl_customers_address_book',$shipping_array,FALSE);
							  }else{	
								  $this->safe_update('wl_customers_address_book',$shipping_array,"customer_id = '".$insId."' AND default_status='Y' AND address_type ='Ship'",FALSE);
							  }
							}
						}
						
						/* Send  mail to user */
			
						$content    =  get_content('wl_auto_respond_mails','1');
						$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
						$body       =  $content->email_content;
						$verify_url = "<a href=".base_url()."users/verify/".md5($registerId).">Click here </a>";
						
						$name = ucwords($first_name)." ".ucwords($last_name);
	
						$body			=	str_replace('{mem_name}',$name,$body);
						$body			=	str_replace('{username}',$email,$body);
						$body			=	str_replace('{password}',$password_rand,$body);
						$body			=	str_replace('{admin_email}',$this->admin_info->admin_email,$body);
						$body			=	str_replace('{site_name}',$this->config->item('site_name'),$body);
						$body			=	str_replace('{url}',base_url(),$body);
						$body			=	str_replace('{link}',$verify_url,$body);
							
						$mail_conf =  array(
						'subject'=>$subject,
						'to_email'=>$email,
						'from_email'=>$this->admin_info->admin_email,
						'from_name'=> $this->config->item('site_name'),
						'body_part'=>$body
						);
	
						$this->dmailer->mail_notify($mail_conf);
					}
					
					SocialAuth::redirectParentWindow('google', $dataArr, $_COOKIE['ref']);
					break;
					
					default:
						header("Location:" . SocialAuth::getConfig('main', 'base_path'));
			}
		}
	}

	public function backtopanel(){
		$this->session->unset_userdata('is_admin_switch');
		$this->auth->logout();
		$redirecturl = ($this->session->userdata('refurl')!='')?$this->session->userdata('refurl'):'sitepanel/members';
		redirect($redirecturl, '');
	}
}
/* End of file users.php */
/* Location: ./application/modules/users/controller/users.php */