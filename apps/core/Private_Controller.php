<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Private_Controller extends MY_Controller
{

	public $userId;	
	public $userType;

	public function __construct()
	{
		ob_start();
		parent::__construct();
		$this->load->library(array('Auth'));
		$this->auth->is_auth_user();
		$this->userId = (int) $this->session->userdata('user_id');
		$this->load->model(array('admin/admin_model'));
		$mres = $this->mres;
		$this->member_parent_id = $mres['parent_id'];
		/*
		if(is_array($mres) && !empty($mres)){
				$this->use_non_step_url=1;//URL for all steps will be same
				$this->use_default_step_url= 'members/profile/complete_your_profile';
				$this->total_profile_steps=4;
				$this->check_verify_otp_screen();
				$this->check_profile_completion_steps();
		}*/
		$this->load->library(array('safe_encrypt','Dmailer','cart'));
	}

	private function check_profile_completion_steps(){
		$profile_steps_completed = $this->mres['profile_steps_completed'];
		$total_steps = $this->total_profile_steps;
		if($profile_steps_completed<=$total_steps){
			if($this->mres['is_profile_completed']){
				$profile_status_url = "students/profile/profile_status";
				$cur_url = $this->uri->uri_string;
				/*Profile is not in approved state so restrict user to browse myaccount sections & show only profile status page*/
				if($this->mres['profile_status']!=1){
					if($cur_url!=$profile_status_url){
						$red_url = site_url($profile_status_url);
						$is_xhr = $this->input->is_ajax_request();
						if($is_xhr){
							$ret_data = array('status'=>0,'red_url'=>$red_url);
							echo json_encode($ret_data);
							die;
						}else{
							redirect($red_url);
						}
					}
				}else{
					$this->step_completion_heading_title="Edit Your Details";
					/*Currently profile details is not editable once approved So restricting profile steps url*/
					$steps_url = array('students/profile'=>1,'students/profile/back'=>1,'students/profile/next'=>1);
					$steps_url[$this->use_default_step_url] = 1;
					for($ix=1;$ix<=$total_steps;$ix++){
						$loop_step_url = $this->getProfileStepURL($ix);
						$steps_url[$loop_step_url] = 1;
					}
					if(isset($steps_url[$cur_url])){
						$is_xhr = $this->input->is_ajax_request();
						$red_url = 'students/profile/profile_details';
						$red_url = site_url($red_url);
						if($is_xhr){
							$ret_data = array('status'=>0,'red_url'=>$red_url);
							echo json_encode($ret_data);
							die;
						}else{
							redirect($red_url);
						}
					}
				}
			}else{
					$this->step_completion_heading_title="Register Your Details";
					$current_profile_step = $this->session->userdata('current_profile_step');
					if($current_profile_step==''){
						$current_profile_step = $profile_steps_completed+1;
						$this->session->set_userdata('current_profile_step',$current_profile_step);
					}
					$steps_url = array('students/profile'=>1,'students/profile/back'=>1,'students/profile/next'=>1);
					if($this->use_non_step_url){
						$steps_url[$this->use_default_step_url] = 1;
					}else{
						for($ix=1;$ix<=$total_steps;$ix++){
							$loop_step_url = $this->getProfileStepURL($ix);
							$steps_url[$loop_step_url] = 1;
						}
					}
					$cur_url = $this->uri->uri_string;
					if(!isset($steps_url[$cur_url])){
						$is_xhr = $this->input->is_ajax_request();
						$red_url = $this->getProfileStepURL();
						$red_url = site_url($red_url);
						if($is_xhr){
							$ret_data = array('status'=>0,'red_url'=>$red_url);
							echo json_encode($ret_data);
							die;
						}else{
							redirect($red_url);
						}
					}
			}
		}
	}

	protected function checkStepAccess(){
		$is_xhr = $this->input->is_ajax_request();
		$profile_steps_completed = $this->mres['profile_steps_completed'];
		if($profile_steps_completed<$this->total_profile_steps && $profile_steps_completed<$this->prev_profile_step){
			$red_url = $this->getProfileStepURL();
			$red_url = site_url($red_url);
			if($is_xhr){
				$ret_data = array('status'=>0,'red_url'=>$red_url);
				echo json_encode($ret_data);
				die;
			}else{
				redirect($red_url);
			}
		}
	}

	/**
		*Returns method name if $step_id is method else url
	*/
	protected function getProfileStepURL($step_id=''){
		if($this->use_non_step_url && ((!is_numeric($step_id) && $step_id!='method') || $step_id=='')){
			$url = $this->use_default_step_url;
			return $url;
		}else{
				$url = 'students/profile/';
				if($step_id=='method'){
					$current_profile_step =$this->session->userdata('current_profile_step');
				}else{
					$current_profile_step = !empty($step_id) ? $step_id : $this->session->userdata('current_profile_step');
				}
				switch($current_profile_step){
					case 1:
						$method= 'step1';
					break;
					case 2:
						$method= 'step2';
					break;
					case 3:
						$method= 'step3';
					break;
					case 4:
						$method= 'step4';
					break;
					default:
						$method= 'step1';
				}
				if($step_id=='method'){
					return $method;
				}else{
					$url .= $method;
					return $url;
				}
		}
	}
}