<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Otp
{
	
	private $CI;
		
   public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('utils_model');
		$this->expire_offset_str = " +30 minutes";
	 }

	 public function generate_otp($otp_conf = array()){
			$otp_type = $otp_conf['otp_type'];
			$is_temp_user =  !empty($otp_conf['is_temp_user']) ? $otp_conf['is_temp_user'] : 0;
			$temp_value =  !empty($otp_conf['temp_value']) ? $otp_conf['temp_value'] : '';
			$temp_id =  $is_temp_user ? (!empty($otp_conf['temp_id']) ? $otp_conf['temp_id'] : '') : '';
			$user_id =  !empty($otp_conf['user_id']) ? $otp_conf['user_id'] : 0;
			if($is_temp_user && !$temp_id){
				die("Temp Id  missing");
			}elseif(!$is_temp_user && empty($otp_conf['user_id'])){
				die("Entity Id  missing");
			}
			$code_length =  !empty($otp_conf['code_length']) ? (int) $otp_conf['code_length'] : 4;
			$verify_code = random_string('numeric',$code_length);
			//Delete Related Old OTP
			
			if($is_temp_user){
				$del_params = array('temp_id'=>$temp_id,'otp_type'=>$otp_type);
			}else{
				$del_params = array('user_id'=>$user_id,'otp_type'=>$otp_type);
			}
			$this->CI->utils_model->safe_delete('wl_otp_list',$del_params,FALSE);
			$otp_data_array = array(
															'otp'         => $verify_code,
															'otp_type'=>$otp_type,
															'added'=>$this->CI->config->item('config.date.time')
															);
			if($is_temp_user){
				$otp_data_array['temp_id'] = $temp_id;
			}else{
				$otp_data_array['user_id'] = $user_id;
				$otp_data_array['temp_id'] = $temp_value;
			}
			$otp_data_array = $this->CI->security->xss_clean($otp_data_array); 
			$this->CI->utils_model->safe_insert('wl_otp_list',$otp_data_array,FALSE);
			$ret = array('code'=>$verify_code);
			return $ret;
	 }

	 public function verify_otp($otp_conf = array()){
			$err=0;
			$otp_type = $otp_conf['otp_type'];
			$is_temp_user =  !empty($otp_conf['is_temp_user']) ? $otp_conf['is_temp_user'] : 0;
			$temp_id =  $is_temp_user ? (!empty($otp_conf['temp_id']) ? $otp_conf['temp_id'] : '') : '';
			$temp_value =  !empty($otp_conf['temp_value']) ? $otp_conf['temp_value'] : '';
			$user_id =  !empty($otp_conf['user_id']) ? $otp_conf['user_id'] : 0;
			$otp =  !empty($otp_conf['otp']) ? trim($otp_conf['otp']) : '';
			if(!$otp){
				$err=1;
				$err_msg = "OTP  missing";
			}elseif($is_temp_user && !$temp_id){
				$err=1;
				$err_msg = "Temp Id  missing";
			}elseif(!$is_temp_user && empty($otp_conf['user_id'])){
				$err=1;
				$err_msg = "Entity Id  missing";
			}
			if(!$err){
					if($is_temp_user){
						$otp_params = array('temp_id'=>$temp_id,'otp_type'=>$otp_type,'otp'=>$otp);
					}else{
						$otp_params = array('user_id'=>$user_id,'otp_type'=>$otp_type,'otp'=>$otp);
						if($temp_value!=''){
							$otp_params['temp_id'] = $temp_value;
						}
					}
					$res_otp_res = $this->CI->db->select('id,added')->get_where('wl_otp_list',$otp_params)->row_array();
					if(is_array($res_otp_res) && !empty($res_otp_res)){
							$should_validate_timer = true;
							if($should_validate_timer){
								$time_offset = strtotime($this->CI->config->item('config.date.time'));
								$expire_otp_time = strtotime($res_otp_res['added'].$this->expire_offset_str);
								$err = $expire_otp_time<$time_offset;
							}else{
								$err=0;
							}
							if($err){
								$err_code = 'ERR_OTP_EXPIRED';
								$err_msg="OTP Expired";
							}else{
								$res_otp_verified=true;
								//$this->CI->utils_model->safe_delete('wl_otp_list',array('id'=>$res_otp_res['id']),FALSE);
							}
					}else{
						$err=1;
						$err_code = 'ERR_OTP_INVALID';
						$err_msg="Invalid OTP";
					}
			}
			if(!$err){
				$ret = array('err'=>0);
			}else{
				$ret = array('err'=>1,'err_msg'=>$err_msg);
			}
			return $ret;
	 }

	public function delete_otp($otp_conf = array()){
		$err=0;
		$otp_type = $otp_conf['otp_type'];
		$is_temp_user =  !empty($otp_conf['is_temp_user']) ? $otp_conf['is_temp_user'] : 0;
		$temp_id =  $is_temp_user ? (!empty($otp_conf['temp_id']) ? $otp_conf['temp_id'] : '') : '';
		$user_id =  !empty($otp_conf['user_id']) ? $otp_conf['user_id'] : 0;
		if($is_temp_user && !$temp_id){
			$err=1;
			$err_msg = "Temp Id  missing";
		}elseif(!$is_temp_user && empty($otp_conf['user_id'])){
			$err=1;
			$err_msg = "Entity Id  missing";
		}
		if(!$err){
			if($is_temp_user){
				$del_params = array('temp_id'=>$temp_id,'otp_type'=>$otp_type);
			}else{
				$del_params = array('user_id'=>$user_id,'otp_type'=>$otp_type);
			}
			$this->CI->utils_model->safe_delete('wl_otp_list',$del_params,FALSE);
		}
		if(!$err){
			$ret = array('err'=>0);
		}else{
			$ret = array('err'=>1,'err_msg'=>$err_msg);
		}
		return $ret;
	}

	public function check_last_otp_status($otp_conf = array()){
		$err=0;
		$code="";
		$otp_type = $otp_conf['otp_type'];
		$is_temp_user =  !empty($otp_conf['is_temp_user']) ? $otp_conf['is_temp_user'] : 0;
		$temp_id =  $is_temp_user ? (!empty($otp_conf['temp_id']) ? $otp_conf['temp_id'] : '') : '';
		$user_id =  !empty($otp_conf['user_id']) ? $otp_conf['user_id'] : 0;
		$otp =  !empty($otp_conf['otp']) ? trim($otp_conf['otp']) : '';
		if($is_temp_user && !$temp_id){
			$err=1;
			$err_msg = "Temp Id  missing";
		}elseif(!$is_temp_user && empty($otp_conf['user_id'])){
			$err=1;
			$err_msg = "Entity Id  missing";
		}
		if(!$err){
				if($is_temp_user){
					$otp_params = array('temp_id'=>$temp_id,'otp_type'=>$otp_type);
				}else{
					$otp_params = array('user_id'=>$user_id,'otp_type'=>$otp_type);
				}
				$res_otp_res = $this->CI->db->select('id,added,otp')->order_by('added DESC')->get_where('wl_otp_list',$otp_params)->row_array();
				if(is_array($res_otp_res) && !empty($res_otp_res)){
						$code=$res_otp_res['otp'];
						$time_offset = strtotime($this->CI->config->item('config.date.time'));
						$expire_otp_time = strtotime($res_otp_res['added'].$this->expire_offset_str);
						$err = $expire_otp_time<$time_offset;
						if($err){
							$err_code = 'ERR_OTP_EXPIRED';
							$err_msg="OTP Expired";
						}else{
							
						}
				}else{
					$err=1;
					$err_code = 'ERR_OTP_INVALID';
					$err_msg="Invalid OTP";
				}
		}
		if(!$err){
			$ret = array('err'=>0,'code'=>$code);
		}else{
			$ret = array('err'=>1,'err_msg'=>$err_msg,'code'=>$code);
		}
		return $ret;
	}
 


}
// END Form Email mailer  Class
/* End of file Dmailer.php */
/* Location: ./application/libraries/Dmailer.php */