<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Whatsapp_integration{
	public $ci;
	public function __construct()
	{
		if (!isset($this->ci)){
			$this->ci =& get_instance();
		}
		$this->from_number = "+919361418662";
	}
	private function get_token(){
		$token = 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJsY1pWMF82VTNza2RKU19GOV9qdGNMNXotNlZPc05EaGNuZ1lLNHFHaVJvIn0.eyJqdGkiOiI5NDc4OWExOS1hZWNmLTRkZDMtYWQ5MS02Nzg0ZWJmODJiYjIiLCJleHAiOjE5MzUwNDcyMjMsIm5iZiI6MCwiaWF0IjoxNjE5Njg3MjIzLCJpc3MiOiJodHRwOi8vaW50ZXJuYWwtZmMtYXBzMS0wMC1hbGIta2V5Y2xvYWstMjAzODM0MDkxMS5hcC1zb3V0aC0xLmVsYi5hbWF6b25hd3MuY29tL2F1dGgvcmVhbG1zL3Byb2R1Y3Rpb24iLCJhdWQiOiIwMGU4N2Q3YS0wZmI5LTRjNTYtOWY2NC0xM2Q1ZjhiNjJhYTEiLCJzdWIiOiJkMTJlZWEwNi0zODc3LTQ1YzMtOTk4YS1hNGMyYWRkYzYyNGMiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiIwMGU4N2Q3YS0wZmI5LTRjNTYtOWY2NC0xM2Q1ZjhiNjJhYTEiLCJhdXRoX3RpbWUiOjAsInNlc3Npb25fc3RhdGUiOiI3NmVmZmZlNi1mNjA4LTRlNmQtYTQxNi1mYWIxMzgxYjIxZTUiLCJhY3IiOiIxIiwiYWxsb3dlZC1vcmlnaW5zIjpbXSwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbIm9mZmxpbmVfYWNjZXNzIiwidW1hX2F1dGhvcml6YXRpb24iXX0sInJlc291cmNlX2FjY2VzcyI6eyJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6Im1lc3NhZ2U6Z2V0IGFnZW50OnVwZGF0ZSBkYXNoYm9hcmQ6cmVhZCBhZ2VudDpyZWFkIG1lc3NhZ2U6Y3JlYXRlIGFnZW50OmRlbGV0ZSBtZXNzYWdpbmctY2hhbm5lbHM6bWVzc2FnZTpzZW5kIHJlcG9ydHM6cmVhZCBjb252ZXJzYXRpb246dXBkYXRlIGltYWdlOnVwbG9hZCByZXBvcnRzOmV4dHJhY3QgdXNlcjp1cGRhdGUgYWdlbnQ6Y3JlYXRlIHJlcG9ydHM6ZXh0cmFjdDpyZWFkIHVzZXI6cmVhZCBmaWx0ZXJpbmJveDpyZWFkIG91dGJvdW5kbWVzc2FnZTpnZXQgbWVzc2FnaW5nLWNoYW5uZWxzOnRlbXBsYXRlOmdldCByb2xlOnJlYWQgcmVwb3J0czpmZXRjaCBtZXNzYWdpbmctY2hhbm5lbHM6bWVzc2FnZTpnZXQgbWVzc2FnaW5nLWNoYW5uZWxzOnRlbXBsYXRlOmNyZWF0ZSBmaWx0ZXJpbmJveDpjb3VudDpyZWFkIGNvbnZlcnNhdGlvbjpyZWFkIHVzZXI6ZGVsZXRlIGNvbnZlcnNhdGlvbjpjcmVhdGUgb3V0Ym91bmRtZXNzYWdlOnNlbmQgYmlsbGluZzp1cGRhdGUgdXNlcjpjcmVhdGUiLCJjbGllbnRIb3N0IjoiMTAuNjguMTQuMjI0IiwiY2xpZW50SWQiOiIwMGU4N2Q3YS0wZmI5LTRjNTYtOWY2NC0xM2Q1ZjhiNjJhYTEiLCJjbGllbnRBZGRyZXNzIjoiMTAuNjguMTQuMjI0In0.KzkLBV7-ZhJsWc6me1dn8CvnsRCe0pG6xPhBKhPYyY3LjEZ_2B8IEnk6apduS6GGL8Ybl4z5ZcgcsLxDMdVhuiejwKSw5NKMVIzwTmELSzT7VcGixXA81u-FqRWK3p8aeb8z5XJ7BxTjGwqdRoSQuzDAwKiW8AN4CaAw-LVNYDwL4OlW2v6HvQBMi2u7eigkI-wA8tAaqUT3o6Uj2od7hFgL8YSzlGaETmJv0p5fKgs6YAETd7_vJsfDU9y9dmWbaFkpnxRg2SMCnbv7cBvVBAHpndIXzXbX2fGc0OeD8jaMnWX9MAZStRurBbdVUqT7fAYisTNctvplpwuKudJgLg';
		$ret_arr = array('token'=>$token);
		return $ret_arr;
	}

	public function send_message($params=array()){
			try{
					$token_res = $this->get_token();
					$token = $token_res['token'];
					$template_name = $params['message_template']['template_name'];
					$default_props_message_tpl_data = array(
																							'storage'=>'none',
																							'namespace'=>'44e5c0d6_0e5e_443f_babf_3eb4e99af5e5',
																							'language'=>array(
																														'code'=> "en",
																														'policy'=>'deterministic'
																													)
																						);
					$message_template_data = array_merge($params['message_template'],$default_props_message_tpl_data);
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
								if($mval_len==10){
									$mval = "+91".$mval;
								}elseif($mval_len>10 && preg_match("~^(0|\+?91)(\d+)~",$mval,$matches)){
										$mval = "+91".$matches[2];
								}
								$arr_converted_tonum[] = $mval;
								if(!isset($to_phone_track_arr[$mval])){
									$to_phone_track_arr[$mval]=1;
									$params_to[] = array('phone_number'=>$mval);
								}
							}
						}
					}
					$arr_converted_tonum = array_unique($arr_converted_tonum);
					$val_converted_tonum = implode(",",$arr_converted_tonum);
					$param_req = array(
														'from'=>array(
																		'phone_number'=>$this->from_number
																	),
														'provider'=>'whatsapp',
														'to'=>$params_to,
														'data'=>array(
																				'message_template'=>$message_template_data
																			)
													);
					//trace($arr_converted_tonum);
					//trace($param_req);
					//die;
					$json_data = json_encode($param_req);
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://api.in.freshchat.com/v2/outbound-messages/whatsapp',
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
						 "Authorization: Bearer $token"
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
					$log_msg_qry = $this->ci->db->insert_string('wl_whatsapp_log', array('request_id'=>$request_id,'send_to'=>$val_converted_tonum,'template_name'=>$template_name,'response_data'=>$response,'date_added'=>$this->ci->config->item('config.date.time')));
					$this->ci->db->query($log_msg_qry);
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