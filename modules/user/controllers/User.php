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

	public function login()
	{
		// If user has login_verified flag, they are properly logged in
		if($this->session->userdata('user_id') && $this->session->userdata('login_verified') === TRUE) {
			redirect('members');
		}
		
		// If there's any session data but no verified flag, destroy everything
		if($this->session->userdata('user_id')) {
			$this->destroy_all_session();
		}
		
		$is_xhr = $this->input->is_ajax_request();
		$preserved_ref_url = $this->preservedRefUrl(true);
		
		if($is_xhr) {
			$member_type = $this->input->post('member_type', TRUE);
			
			if($this->input->post('action') == 'Y') {
				$this->form_validation->set_rules('user_name', 'Email ID', 'trim|required|valid_email');
				$this->form_validation->set_rules('password', 'Password', 'trim|required');
				
				if($this->form_validation->run() == TRUE) {
					$res = $this->handle_login_with_username($member_type);
					if(!$res['err']) {
						$ret_data = array(
							'status' => '1',
							'temp_user_id' => $res['temp_user_id'],
							'email' => $res['email']
						);
					} else {
						$ret_data = array('status' => '0', 'msg' => $res['msg']);
					}
				} else {
					$error_array = array();
					if(form_error('user_name')) $error_array['user_name'] = form_error('user_name');
					if(form_error('password')) $error_array['password'] = form_error('password');
					$ret_data = array('status' => '0', 'error_flds' => $error_array);
				}
				echo json_encode($ret_data);
				exit;
			}
		}
		
		$saved_username = get_cookie('Xcnv');
		if($saved_username != '') {
			$saved_username = $this->safe_encrypt->decode($saved_username);
		}
		$saved_pwd = get_cookie('Omn2z');
		if($saved_pwd != '') {
			$saved_pwd = $this->safe_encrypt->decode($saved_pwd);
		}
		
		$data['posted_user_name'] = $saved_username;
		$data['posted_password'] = $saved_pwd;
		$data['remember'] = $saved_username != '' ? 'Y' : '';
		$data['heading_title'] = "Login";
		$this->header_menu_section = 'login';
		$data['preserved_ref_url'] = $preserved_ref_url;
		$this->load->view('users_login', $data);
	}

	private function handle_login_with_username($member_type)
	{
		$username = $this->input->post('user_name');
		$password = $this->input->post('password');
		$remember = $this->input->post('remember');
		
		$params_verify_user = array(
			'username' => $username,
			'password' => $password,
			'login_type' => 'website'
		);
		
		$res_verify = $this->auth->verify_user($params_verify_user);
		
		if(!$res_verify['err']) {
			$user_data = $this->db->select('customers_id, user_name, first_name, last_name, member_type, status')
				->from('wl_customers')
				->where('user_name', $username)
				->where('status', '1')
				->get()
				->row_array();
			
			if($user_data) {
				$otp = sprintf("%06d", mt_rand(1, 999999));
				$temp_user_id = md5(uniqid() . $user_data['customers_id'] . time() . rand(1000, 9999));
				
				// Clear any existing OTP data first
				$this->clear_otp_session();
				
				// Store OTP data in session - NOT logged in yet
				$this->session->set_userdata([
					'otp_pending_user_id' => $temp_user_id,
					'otp_pending_data' => $user_data,
					'otp_pending_code' => $otp,
					'otp_pending_expiry' => time() + 300,
					'otp_pending_password' => $password,
					'otp_pending_member_type' => $member_type,
					'otp_pending_remember' => $remember,
					'otp_failed_attempts' => 0
				]);
				
				// Send OTP email
				$this->send_otp_email($user_data['user_name'], $otp);
				
				$masked_email = $this->mask_email($user_data['user_name']);
				
				return array(
					'err' => 0, 
					'temp_user_id' => $temp_user_id,
					'email' => $masked_email
				);
			}
		}
		
		return array('err' => 1, 'msg' => 'Invalid username or password');
	}

	public function verify_email_otp()
	{
		$is_xhr = $this->input->is_ajax_request();
		if(!$is_xhr) {
			redirect('login');
		}
		
		header('Content-Type: application/json');
		
		if($this->input->post('action') == 'verify') {
			$otp_code = $this->input->post('otp_code');
			$temp_user_id = $this->input->post('temp_user_id');
			$member_type = $this->input->post('member_type');
			$remember = $this->input->post('remember');
			
			// Get OTP data from session
			$pending_data = $this->session->userdata('otp_pending_data');
			$pending_otp = $this->session->userdata('otp_pending_code');
			$pending_expiry = $this->session->userdata('otp_pending_expiry');
			$stored_temp_id = $this->session->userdata('otp_pending_user_id');
			$pending_password = $this->session->userdata('otp_pending_password');
			$failed_attempts = $this->session->userdata('otp_failed_attempts') ?: 0;
			
			// Validate session
			if((string)$temp_user_id !== (string)$stored_temp_id || empty($pending_data)) {
				echo json_encode([
					'status' => '0',
					'msg' => 'Invalid session. Please login again.'
				]);
				return;
			}
			
			// Check OTP expiry
			if(time() > $pending_expiry) {
				$this->clear_otp_session();
				echo json_encode([
					'status' => '0',
					'msg' => 'OTP has expired. Please login again.'
				]);
				return;
			}
			
			// Verify OTP
			if($otp_code != $pending_otp) {
				$failed_attempts++;
				$this->session->set_userdata('otp_failed_attempts', $failed_attempts);
				
				if($failed_attempts >= 3) {
					$this->clear_otp_session();
					echo json_encode([
						'status' => '0',
						'msg' => 'Too many failed attempts. Please login again.'
					]);
					return;
				}
				
				echo json_encode([
					'status' => '0',
					'msg' => 'Invalid OTP. Please try again. (' . (3 - $failed_attempts) . ' attempts remaining)'
				]);
				return;
			}
			
			// OTP VERIFIED SUCCESSFULLY - Get data before clearing
			$user_data = $pending_data;
			$remember_val = $remember ?: $this->session->userdata('otp_pending_remember');
			$temp_password = $pending_password;
			
			// Clear OTP pending data first
			$this->clear_otp_session();
			
			// Set user login session with verified flag
			$session_data = [
				'user_id' => $user_data['customers_id'],
				'user_name' => $user_data['user_name'],
				'first_name' => $user_data['first_name'],
				'last_name' => $user_data['last_name'],
				'member_type' => $member_type ? $member_type : (isset($user_data['member_type']) ? $user_data['member_type'] : '3'),
				'last_activity' => time(),
				'logged_in' => TRUE,
				'login_verified' => TRUE
			];
			
			$this->session->set_userdata($session_data);
			
			// Set remember me cookies if requested
			if($remember_val == 'Y') {
				$encoded_username = $this->safe_encrypt->encode($user_data['user_name']);
				if($temp_password) {
					$encoded_password = $this->safe_encrypt->encode($temp_password);
					set_cookie('Omn2z', $encoded_password, time()+60*60*24*30, '/', '', false, true);
				}
				set_cookie('Xcnv', $encoded_username, time()+60*60*24*30, '/', '', false, true);
			}
			
			// Update last login info
			$this->db->where('customers_id', $user_data['customers_id']);
			$this->db->update('wl_customers', [
				'current_login' => $this->config->item('config.date.time'),
				'last_ip' => $this->input->ip_address()
			]);
			
			$preserved_ref_url = $this->preservedRefUrl(true);
			$ref_redirect = !empty($preserved_ref_url) ? $preserved_ref_url : site_url('members');
			
			echo json_encode([
				'status' => '1',
				'msg' => 'Login successful!',
				'redirect_url' => $ref_redirect
			]);
			return;
		} else {
			$this->clear_otp_session();
			echo json_encode([
				'status' => '0',
				'msg' => 'Invalid request. Please login again.'
			]);
			return;
		}
	}

	public function resend_email_otp()
	{
		$is_xhr = $this->input->is_ajax_request();
		if(!$is_xhr) {
			redirect('login');
		}
		
		$temp_user_id = $this->input->post('temp_user_id');
		
		$pending_data = $this->session->userdata('otp_pending_data');
		$stored_temp_id = $this->session->userdata('otp_pending_user_id');
		
		if((string)$temp_user_id !== (string)$stored_temp_id || empty($pending_data)) {
			$this->clear_otp_session();
			echo json_encode([
				'status' => '0',
				'msg' => 'Invalid session. Please login again.'
			]);
			return;
		}
		
		// Check resend cooldown (30 seconds)
		$last_resend = $this->session->userdata('last_otp_resend');
		if($last_resend && (time() - $last_resend) < 30) {
			$wait_time = 30 - (time() - $last_resend);
			echo json_encode([
				'status' => '0',
				'msg' => 'Please wait ' . $wait_time . ' seconds before requesting another OTP.'
			]);
			return;
		}
		
		$otp = sprintf("%06d", mt_rand(1, 999999));
		
		$this->session->set_userdata([
			'otp_pending_code' => $otp,
			'otp_pending_expiry' => time() + 300,
			'last_otp_resend' => time(),
			'otp_failed_attempts' => 0
		]);
		
		$this->send_otp_email($pending_data['user_name'], $otp);
		
		echo json_encode([
			'status' => '1',
			'msg' => 'New OTP has been sent to your email.'
		]);
		return;
	}

	private function send_otp_email($email, $otp)
	{
		$site_name = $this->config->item('site_name');
		$from_email = $this->admin_info->admin_email;
		
		$subject = $site_name . ' - Your Login Verification Code';
		
		$body = '
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8">
			<title>Login OTP Verification</title>
			<style>
				body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
				.container { max-width: 550px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
				.header { background: #4a90e2; padding: 20px; text-align: center; }
				.header h1 { margin: 0; color: #ffffff; }
				.content { padding: 30px; }
				.otp-code { font-size: 36px; font-weight: bold; color: #4a90e2; background: #f0f0f0; padding: 15px; text-align: center; letter-spacing: 5px; border-radius: 5px; margin: 20px 0; }
				.footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #666; }
				.warning { color: #e74c3c; font-size: 12px; margin-top: 15px; }
			</style>
		</head>
		<body>
			<div class="container">
				<div class="header">
					<h1>' . $site_name . '</h1>
				</div>
				<div class="content">
					<h3>Dear User,</h3>
					<p>Your OTP for login is:</p>
					<div class="otp-code"><strong>' . $otp . '</strong></div>
					<p>This OTP is valid for <strong>5 minutes</strong>.</p>
					<div class="warning">⚠️ Never share this OTP with anyone.</div>
				</div>
				<div class="footer">
					<p>&copy; ' . date('Y') . ' ' . $site_name . '. All rights reserved.</p>
				</div>
			</div>
		</body>
		</html>';
		
		$mail_conf = array(
			'subject' => $subject,
			'to_email' => $email,
			'from_email' => $from_email,
			'from_name' => $site_name,
			'body_part' => $body,
			'debug' => false
		);
		
		return $this->dmailer->mail_notify_ci($mail_conf);
	}

	private function mask_email($email)
	{
		$parts = explode('@', $email);
		$name = $parts[0];
		$len = strlen($name);
		if($len <= 2) {
			$masked_name = $name[0] . str_repeat('*', $len - 1);
		} else {
			$masked_name = substr($name, 0, 2) . str_repeat('*', $len - 2);
		}
		return $masked_name . '@' . $parts[1];
	}

	public function check_session_status()
	{
		$is_xhr = $this->input->is_ajax_request();
		if(!$is_xhr) {
			echo json_encode(['status' => 'error']);
			return;
		}
		
		if($this->session->userdata('user_id') && $this->session->userdata('login_verified') === TRUE) {
			$last_activity = $this->session->userdata('last_activity');
			$current_time = time();
			$inactive_time = 1800;
			
			if(($current_time - $last_activity) > $inactive_time) {
				$this->destroy_all_session();
				echo json_encode(['status' => 'expired']);
				return;
			}
			
			$this->session->set_userdata('last_activity', $current_time);
			echo json_encode(['status' => 'active']);
		} else {
			echo json_encode(['status' => 'expired']);
		}
	}

	public function logout()
	{
		$this->destroy_all_session();
		$this->cart->destroy();
		if(isset($this->auth)) {
			$this->auth->logout();
		}
		$this->session->set_userdata(array('msg_type'=>'success'));
		$this->session->set_flashdata('success', $this->config->item('member_logout'));
		redirect(site_url('login'), '');
	}

	public function clear_otp_session_ajax()
	{
		if(!$this->input->is_ajax_request()) {
			redirect('login');
		}
		
		$this->clear_otp_session();
		
		echo json_encode(['status' => '1', 'msg' => 'OTP session cleared']);
		return;
	}

	private function clear_otp_session()
	{
		$this->session->unset_userdata([
			'otp_pending_user_id',
			'otp_pending_data',
			'otp_pending_code',
			'otp_pending_expiry',
			'otp_pending_password',
			'otp_pending_member_type',
			'otp_pending_remember',
			'otp_failed_attempts',
			'last_otp_resend'
		]);
	}

	private function destroy_all_session()
	{
		$this->clear_otp_session();
		$this->session->unset_userdata([
			'user_id',
			'user_name',
			'first_name',
			'last_name',
			'member_type',
			'last_activity',
			'logged_in',
			'login_verified'
		]);
		$this->session->sess_destroy();
		delete_cookie('Xcnv');
		delete_cookie('Omn2z');
	}

	public function test_otp_email()
	{
		$email = 'mhdafjalit@gmail.com';
		$otp = sprintf("%06d", mt_rand(1, 999999));
		
		echo "Testing OTP email to: " . $email . "<br>";
		echo "OTP: " . $otp . "<br><br>";
		
		$result = $this->send_otp_email($email, $otp);
		
		if($result) {
			echo "OTP Email sent successfully! Check your inbox/spam folder.";
		} else {
			echo "Failed to send OTP email. Check error logs.";
		}
	}


	

	// Keep all your existing methods below (forgotten_password, reset_pwd, etc.)
	public function forgotten_password()
	{
		if ( $this->input->post('forgotme')!="")
		{
			$email = $this->input->post('email',TRUE);
			$this->form_validation->set_rules('email', ' Email ID', 'required|valid_email');
			
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

					$content    =  get_content('wl_auto_respond_mails','2');
					$subject    =  $content->email_subject;
					$subject = str_replace('{site_name}',$this->config->item('site_name'),$subject);
					$body       =  $content->email_content;

					$reset_link = '<a href="'.base_url().'user/reset_pwd/'.$unique_ref_code.'">Click here to reset </a>';
					$validity_text = "Reset link will be only valid for 30 minutes";

					$name = ucwords($first_name)." ".ucwords($last_name);
					$body = str_replace('{mem_name}',$name,$body);
					$body = str_replace('{username}',$username,$body);
					$body = str_replace('{admin_email}',$this->admin_info->admin_email,$body);
					$body = str_replace('{site_name}',$this->config->item('site_name'),$body);
					$body = str_replace('{url}',base_url(),$body);
					$body = str_replace('{reset_link}',$reset_link,$body);
					$body = str_replace('{validity_text}',$validity_text,$body);

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
							$password = $this->safe_encrypt->encode($posted_password);
							$res_register = $this->db->select("password,first_name,last_name,user_name")->get_where('wl_customers',array('customers_id'=>$res_user['user_id']))->row_array();
							if(is_array($res_register) && !empty($res_register)){
								$this->user_model->safe_update('wl_customers',array('password'=>$password),array('customers_id'=>$res_user['user_id']),FALSE);
							}
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

	// Add other existing methods here...
	public function guest_login()
	{
		if( $this->auth->is_user_logged_in() )
		{
			redirect('cart/checkout','');
		}
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
				$posted_data = $this->security->xss_clean($posted_data); 
				$registerId = $this->user_model->safe_insert('wl_customers',$posted_data); 
				if($registerId > 0)
				{
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
					
					$content    =  get_content('wl_auto_respond_mails','1');
					$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
					$body       =  $content->email_content;
					$verify_url = "<a href=".base_url()."user/verify/".$actkey.">Click here </a>";
					$name 		= ucwords($first_name)." ".ucwords($last_name);
					$body = str_replace('{mem_name}',$name,$body);
					$body = str_replace('{username}',$username,$body);
					$body = str_replace('{password}',$password,$body);
					$body = str_replace('{admin_email}',$this->admin_info->admin_email,$body);
					$body = str_replace('{site_name}',$this->config->item('site_name'),$body);
					$body = str_replace('{url}',base_url(),$body);
					$body = str_replace('{verification_link}',$verify_url,$body);
					$mail_conf =  array(
						'subject'=>$subject,
						'to_email'=>$this->input->post('user_name'),
						'from_email'=>$this->admin_info->admin_email,
						'from_name'=> $this->config->item('site_name'),
						'body_part'=>$body
					);
					$this->dmailer->mail_notify($mail_conf);
					
					$content    =  get_content('wl_auto_respond_mails','6');			
					$subject    =  str_replace('{site_name}',$this->config->item('site_name'),$content->email_subject);
					$body       =  $content->email_content;
					$name = ucwords($first_name)." ".ucwords($last_name);
					$body = str_replace('{name}',$name,$body);
					$body = str_replace('{username}',$username,$body);
					$body = str_replace('{password}',$password,$body);
					$body = str_replace('{admin_email}',$this->admin_info->admin_email,$body);
					$body = str_replace('{site_name}',$this->config->item('site_name'),$body);
					$body = str_replace('{url}',base_url(),$body);
					$mail_conf =  array(
						'subject'=>$subject,
						'to_email'=>$this->admin_info->admin_email,
						'from_email'=>$this->input->post('user_name'),
						'from_name'=> $this->config->item('site_name'),
						'body_part'=>$body
					);
					$this->dmailer->mail_notify($mail_conf);
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
					$this->load->library('Custom_notification');
					$param_notification=array(
										'user_id'=>$mres['customers_id'],
										'verified_dt'=>$this->config->item('config.date.time'),
										'email'=>$mres['temp_email']
									);
					$this->custom_notification->user_email_verified($param_notification);
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

	public function backtopanel(){
		$this->session->unset_userdata('is_admin_switch');
		$this->auth->logout();
		$redirecturl = ($this->session->userdata('refurl')!='')?$this->session->userdata('refurl'):'sitepanel/members';
		redirect($redirecturl, '');
	}
}
?>