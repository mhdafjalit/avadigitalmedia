<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sms_integration{
	public $ci;
	public function __construct()
	{
		if (!isset($this->ci)){
			$this->ci =& get_instance();
		}
		$this->default_sender_id = "ROYSCN";
	}
	private function get_token(){
		$token = 'mE5J1hBQCWMHSnRiq67wU9xpF3kY4I0vZGOtgsXKaDTLcbljfeKSQx64qgbmFeuRinyJIvTXhj0Cp1Lk';
		$ret_arr = array('token'=>$token);
		return $ret_arr;
	}

	public function send_message($params=array()){
			try{
					$token_res = $this->get_token();
					$token = $token_res['token'];
					$to_numbers = !isset($params['to']) ? "" : $params['to'];
					$to_numbers_arr = explode(",",$to_numbers);
					$params_to = array();
					$arr_converted_tonum = array();
					$to_phone_track_arr = array();
					if(!empty($to_numbers_arr)){
						foreach($to_numbers_arr as $mval){
							$mval = trim($mval);
							if($mval!=''){
								$mval_len = strlen($mval);
								if($mval_len>10){
										$mval =  preg_replace(array('/^\+91/','/^0/'), '',$mval);
								}
								$arr_converted_tonum[] = $mval;
								if(!isset($to_phone_track_arr[$mval])){
									$to_phone_track_arr[$mval]=1;
									$params_to[] = $mval;
								}
							}
						}
					}
					$arr_converted_tonum = array_unique($arr_converted_tonum);
					$val_converted_tonum = implode(",",$arr_converted_tonum);
					$message = !empty($params['message']) ? $params['message'] : 127609;
					$route = !empty($params['route']) ? $params['route'] : "dlt";
					$variables_values = !empty($params['variables_values']) ? $params['variables_values'] : "645645";
					$param_req = array(
														'route'=>$route,
														'sender_id'=>$this->default_sender_id,
														'message'=>$message,
														'numbers'=>implode(",",$params_to),
														'variables_values'=>$variables_values,
														'flash'=>0
													);
					//trace($arr_converted_tonum);
					//trace($param_req);
					//die;
					$json_data = json_encode($param_req);
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://www.fast2sms.com/dev/bulkV2',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_SSL_VERIFYHOST=>0,
					CURLOPT_SSL_VERIFYPEER=>0,
					CURLOPT_POSTFIELDS =>$json_data,
					CURLOPT_HTTPHEADER => array(
						 "Content-Type: application/json",
						 "authorization: $token"
					  )
					));
					$response = curl_exec($curl);
					curl_close($curl);
					$response_obj = json_decode($response);
					if(!empty($response_obj->request_id)){
						$request_id = $response_obj->request_id;
					}else{
						$request_id = "";
					}
					//$log_msg_qry = $this->ci->db->insert_string('wl_sms_log', array('request_id'=>$request_id,'send_to'=>$val_converted_tonum,'response_data'=>$response,'date_added'=>$this->ci->config->item('config.date.time')));
					//$this->ci->db->query($log_msg_qry);
					if(!empty($params['debug'])){
						trace($response); trace($response_obj); die;
					}
					return $response_obj;
			}catch(Exception $e){
				throw $e;
			}
	}
	public function get_outbound_msg_data($params=array()){
		try{
					$token_res = $this->get_token();
					$token = $token_res['token'];
					$request_id = $params['request_id'];
					$json_data = json_encode($param_req);
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://api.in.freshchat.com/v2/outbound-messages?request_id='.$request_id,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_SSL_VERIFYHOST=>0,
					CURLOPT_SSL_VERIFYPEER=>0,
					CURLOPT_POSTFIELDS =>$json_data,
					CURLOPT_HTTPHEADER => array(
						 "Content-Type: application/json",
						 "Authorization: Bearer $token"
					  )
					));
					$response = curl_exec($curl);
					curl_close($curl);
					$response_obj = json_decode($response);
					if(!empty($params['debug'])){
						trace($response); trace($response_obj); die;
					}
					return $response_obj;
			}catch(Exception $e){
				throw $e;
			}
	}
}
/*End of file*/