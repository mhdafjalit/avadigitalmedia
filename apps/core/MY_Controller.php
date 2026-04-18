<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* The MX_Controller class is autoloaded as required */
class MY_Controller extends CI_Controller
{
	public $spamwords = array();
	public $has_spamword;
	public $admin_info;
	public $meta_info;
	public $site_setting;

	public function __construct(){
		ob_start();
		parent::__construct();
        
        $this->load->helper('seo/seo');
		$this->load->config('seo/config');
		$this->config->set_item('language','english');
		
		if($this->uri->segment(1)=='android_apis' || $_SERVER['REQUEST_METHOD']==='OPTIONS')
		{ 
		 header('Access-Control-Allow-Origin: *');
		 header("Access-Control-Allow-Methods: PUT, GET, POST, OPTIONS");
		 header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
		} 
		
		$this->show_active_rec_only	= TRUE;
		$is_xhr = $this->input->is_ajax_request();
		if($is_xhr && $this->config->item('csrf_protection')===TRUE && $this->input->method()=='post'){
			header('X-TKN-KL:'.$this->security->get_csrf_token_name());
			header('X-TKV-KL:'.$this->security->get_csrf_hash());
		}
		
		$this->db->select('*');
		$this->db->from('tbl_admin');
		$this->db->where('admin_id','1');
		$query = $this->db->get();
		if( $query->num_rows() > 0 )
		{
			$this->admin_info = $query->row();
		}

		$this->shareable_properties = array('top_banner_file'=>1);
		$this->mem_top_menu_section="";

		if($this->uri->segment(1)!='sitepanel')
		{
			if($this->session->userdata('unq_user_ref')==''){
				$unique_ref = uniqid(random_string('alnum',8));
				$this->session->set_userdata('unq_user_ref',$unique_ref);
			}
			$this->meta_info  = getMeta();
			$this->userId = (int) $this->session->userdata('user_id');
			if($this->userId > 0)
			{
				$this->load->model(array('admin/admin_model'));
				$this->mres = $mres  = $this->admin_model->get_member_row( $this->userId );
				if(is_array($mres) && !empty($mres)){
					//$this->check_verify_otp_screen();
				}else{
					$this->load->library('Auth');
					$this->auth->logout();
				}
			}
		}
		
		$this->inject_header_css_files = array('magiczoomplus'=>array(
			'path'=>resource_url().'zoom/magiczoomplus.css',
				'insert'=>0
			),
				'star_rating'=>array('path'=>theme_url().'css/star-rating.min.css',
					'insert'=>0
			)	
		);

		$this->inject_footer_js_files = array('star_rating'=>array(
			'path'=>resource_url().'Scripts/star-rating.min.js',
				'insert'=>0
		));
		
		$this->member_type = $this->session->userdata('member_type');
        
        // Check session activity - only for non-login controllers
        $this->check_session_activity();
 	}

   protected function check_session_activity() {
        $current_controller = $this->router->fetch_class();
        $skip_controllers = array('user', 'login', 'auth');
        
        if(in_array($current_controller, $skip_controllers)) {
            return;
        }
        
        if($this->session->userdata('user_id')) {
            $last_activity = $this->session->userdata('last_activity');
            $current_time = time();
            $inactive_time = 1800;
            
            if(($current_time - $last_activity) > $inactive_time) {
                $this->session->sess_destroy();
                delete_cookie('Xcnv');
                delete_cookie('Omn2z');
                
                if(!$this->input->is_ajax_request()) {
                    $this->session->set_flashdata('error', 'Your session has expired due to inactivity. Please login again.');
                    redirect('login');
                }
                return;
            }
            
            $this->session->set_userdata('last_activity', $current_time);
        }
    }

	public function fetch_spamwords()
	{
		if(is_array($this->spamwords) && empty($this->spamwords) )
		{
			$this->db->select('words');
			$this->db->where('status','1');
			$query=$this->db->get('tbl_spam_words');
			if($query->num_rows() > 0)
			{
				$this->spamwords=$query->result();
			}
		}
		return  $this->spamwords;
	}

	public function filter_spamwords($in_string)
	{
		$spam_words="";
		$res=$this->fetch_spamwords();
		$i=0;
		foreach($res as $val)
		{
			if( preg_match("/\b".$val->words."\b/i",$in_string) )
			{
				$spam_words.=$val->words.",";
			}
		}
		$spam_words=rtrim($spam_words,',');
		return  $spam_words;
	}

	public function has_spamwords($in_string)
	{
		$array = array_map('reset', $this->fetch_spamwords());
		$this->has_spamword=check_spam_words($array,$in_string);
		return  $this->has_spamword;
	}

	public function check_spamwords($str)
	{
		if($this->has_spamwords($str))
		{
			$this->form_validation->set_message("check_spamwords","The %s field contains some offensive words. Please remove them first. The Found Offensive Word(s): <b> ".$this->filter_spamwords($str)."</b>");
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	public function get_configuration_values()
	{
		$this->configuration_res = array();
		$configuration_res = $this->db->get_where('wl_configuration')->result_array();
		foreach($configuration_res as $val)
		{
			$this->configuration_res[$val['type']] = $val['value'];
		}
	}

	public function get_global_gst_cent(){
		if(isset($this->configuration_res) && isset($this->configuration_res['gst_cent'])){
			return floatval($this->configuration_res['gst_cent']);
		}
		return 0;
	}

	public function validate_otp($val,$otp_params){
	  $otp_params_arr = explode("~",$otp_params);
	  $otp_type = $otp_params_arr[1];
	  $user_id = $otp_params_arr[0];
	  $mobile_no = $otp_params_arr[2];
		if($val!=''){
			$this->load->library('otp');
			$param_otp = array('user_id'=>$user_id,'otp'=>$val,'otp_type'=>$otp_type,'temp_value'=>$mobile_no);
			$otp_res = $this->otp->verify_otp($param_otp);
			if($otp_res['err']){
				$this->form_validation->set_message('validate_otp', $otp_res['err_msg']);
				return FALSE;
			}
		}
		return TRUE;
	 
	}

	public function check_verify_otp_screen(){
		if(empty($this->mres)){
			redirect(site_url('login'),'');
		}else{
			if(!$this->mres['is_verified_mobile']){
				$is_sms_gateway_implemented = 0;
				$is_xhr = $this->input->is_ajax_request();
				if($this->input->post('btn_resend')=='Y'){
					$this->load->library('otp');
					if($is_xhr ){
						$param_otp_mobile = array('user_id'=>$this->mres['customers_id'],'otp_type'=>'register_mobile','temp_value'=>$this->mres['mobile_number']);
						$otp_res_mobile = $this->otp->generate_otp($param_otp_mobile);
						$otp_mobile = $otp_res_mobile['code'];
						if($otp_mobile!=''){
							//Send sms here
							$ret_data = array('status'=>'1','otp'=>$otp_mobile,'msg'=>"<div style=\"color:#177b33;font-size:14px;\">OTP has been resent</div>");
							echo json_encode($ret_data);
							exit;
						}else{
							$ret_data = array('status'=>'0','msg'=>"<div class=\"required\">Error sending code</div>");
							echo json_encode($ret_data);
							exit;
						}
					}
				}elseif($this->input->post('btn_sbt')!=''){
					$this->form_validation->set_rules('otp_code', 'OTP', "trim|required|callback_validate_otp[".$this->mres['customers_id']."~register_mobile~".$this->mres['mobile_number']."]");
					if ($this->form_validation->run() == TRUE){
						$posted_user_data = array(
						'is_verified_mobile'   => '1'
						);
						$posted_user_data = $this->security->xss_clean($posted_user_data);
						$where       = "customers_id = '".$this->mres['customers_id']."'";
						$this->members_model->safe_update('wl_customers',$posted_user_data,$where,FALSE);

						$param_otp_mobile = array('user_id'=>$this->mres['customers_id'],'otp_type'=>'register_mobile');
						$del_otp_res = $this->otp->delete_otp($param_otp_mobile);
						$this->session->set_userdata(array('msg_type'=>'success'));
						$this->session->set_flashdata('success',"Your account has been verified");
						if($is_xhr){
							$ret_data = array('status'=>'1');
							echo json_encode($ret_data);
							exit;
						}
						redirect('students', '');
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
				}else{
						//Check for last otp to resend automatically for fresh login
						$attempted_login_section = $this->session->userdata('attempted_login_section');
						if(!empty($attempted_login_section)){
							$this->load->library('otp');
							$param_otp_mobile = array('user_id'=>$this->mres['customers_id'],'otp_type'=>'register_mobile');
							$last_otp_status = $this->otp->check_last_otp_status($param_otp_mobile);
							if($last_otp_status['err']){
								$param_otp_mobile['temp_value']  = $this->mres['mobile_number'];
								$otp_res_mobile = $this->otp->generate_otp($param_otp_mobile);
								$otp_mobile = $otp_res_mobile['code'];
								//Send sms here
							}else{
								if(!$is_sms_gateway_implemented){
									$otp_mobile = $last_otp_status['code'];
								}
							}
							$this->session->unset_userdata(array('attempted_login_section'));
						}else{
							if(!$is_sms_gateway_implemented){
								$this->load->library('otp');
								$param_otp_mobile = array('user_id'=>$this->mres['customers_id'],'otp_type'=>'register_mobile');
								$last_otp_status = $this->otp->check_last_otp_status($param_otp_mobile);
								$otp_mobile = $last_otp_status['code'];
								//No need to Send sms here.This is just when no gateway implemented
							}
						}
				}
				$data['heading_title'] = "Verify Mobile";
				$data['mres'] = $this->mres;
				$data['otp_mobile']=!empty($otp_mobile) ? $otp_mobile : '';
				echo $output = $this->load->view('user/verify_otp',$data,TRUE);
				exit;
			}
		}
	}

	public function check_price($disc_price,$price_fld='')
	{
		$disc_price = floatval($disc_price);
		$price      = floatval($price_fld!='' ? $this->input->post($price_fld) : $this->input->post('product_price'));
		if($disc_price>0 && $disc_price>=$price)
		{
			$this->form_validation->set_message('check_price', 'Discount price must be less than actual price.');
			return FALSE;
		}else
		{
			return TRUE;
		}
	}

	public function check_price_zero($price)
	{
		$price      = floatval($price);
		if($price <= 0)
		{
			$this->form_validation->set_message('check_price_zero', 'Price must be a postive numeric number.');
			return FALSE;
		}else
		{
			return TRUE;
		}
	}

	public function validate_ifsc($ifsc_code) {
		if($ifsc_code!=''){
		    if (!preg_match('/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/', $ifsc_code)) {
		        $this->form_validation->set_message('validate_ifsc', 'The IFSC code must be in the format: XXXX0YYYYYY, where X is alphabets and Y is alphanumeric.');
		        return FALSE;
		    }
		    return TRUE;
		}
	}
	
	public function validate_pancard($pan_no) {
		if($pan_no!=''){
		    if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan_no)) {
		        $this->form_validation->set_message('validate_pancard', 'The {field} must be a valid PAN Card number');
		        return FALSE;
		    }
		    return TRUE;
		}
	}

	public function validate_gst($gst) {
	    if ($gst != '') {
	        if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[0-9]{1}$/', $gst)) {
	            $this->form_validation->set_message('validate_gst', 'The GST Number must be in the format: XXXXX0000X0X0, where X is an uppercase letter and digit.');
	            return FALSE;
	        }
	        return TRUE;
	    }
	}

	protected function preservedRefUrl($return=false){
		if($return){
			return preservedRefUrl($return);
		}else{
			preservedRefUrl($return);
		}
	}
}