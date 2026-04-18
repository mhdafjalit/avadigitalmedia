<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_reward_coin_settings')){
	function get_reward_coin_settings(){
		$CI =& get_instance();
		if(empty($CI->configuration_settings_gx['coin_settings'])){
			$configuration_res = $CI->db->get_where('wl_configuration',array('type'=>'coin_settings'))->row_array();
			$CI->configuration_settings_gx['coin_settings']=!empty($configuration_res) ? unserialize($configuration_res['value']) : array();
		}
		return $CI->configuration_settings_gx['coin_settings'];
	}
}

if(! function_exists('get_available_wallet_credit')){
	function get_available_wallet_credit($user_id)
	{
		$CI =& get_instance();
		$cur_date = $CI->config->item('config.date');
		$points_credit=$CI->db->query("SELECT SUM(left_points) AS Credit FROM wl_wallet WHERE user_id='".$user_id."' AND transaction_type='Cr' AND status='1' AND DATE(expiry_date)>='".$cur_date."' ")->row_array();
		 
		$points_debit=0;//$CI->db->query("SELECT SUM(points) AS Debit FROM wl_wallet WHERE user_id='".$user_id."' AND transaction_type='Dr' AND status='1' ")->row_array();
		 
		$total_available_points=($points_credit['Credit']-$points_debit['Debit']);
		$total_available_points=$total_available_points<0 ? 0 : $total_available_points;
		return $total_available_points;
	}
}

if(! function_exists('get_total_earn_wallet_credit')){
function get_total_earn_wallet_credit($user_id)
{
	$CI =& get_instance();

	$points_credit=$CI->db->query("SELECT SUM(points) AS Credit FROM wl_wallet WHERE user_id='".$user_id."' AND transaction_type='Cr' AND status='1' ")->row_array();

	$total_available_points=($points_credit['Credit']) ? $points_credit['Credit'] : 0;
	return $total_available_points;
}
}

if(! function_exists('get_total_availed_wallet_credit')){
function get_total_availed_wallet_credit($user_id)
{
	$CI =& get_instance();

	$points_credit=$CI->db->query("SELECT SUM(points) AS Debit FROM wl_wallet WHERE user_id='".$user_id."' AND transaction_type='Dr' AND status='1' ")->row_array();

	$total_avail_points=($points_credit['Debit']) ? $points_credit['Debit'] : 0;
	return $total_avail_points;
}
}

if(! function_exists('convert_points_to_wallet_amount')){
	function convert_points_to_wallet_amount($points=0)
	{
		$CI =& get_instance();
		$total_amt=0;
		if($points){
			$settings=get_reward_coin_settings();
			if(!empty($settings['reward_per_unit']['key_value'])){
				$one_unit_eq = $settings['reward_per_unit']['key_value'];
				$total_amt=$points*$one_unit_eq;
			}
		}
		return $total_amt;
	}
}

if(! function_exists('convert_amount_to_wallet_points')){
	function convert_amount_to_wallet_points($amt=0)
	{
		$CI =& get_instance();
		$total_points=0;
		if($amt){
			$settings=get_reward_coin_settings();
			if(!empty($settings['reward_per_unit']['key_value'])){
				$one_unit_eq = $settings['reward_per_unit']['key_value'];
				$total_points=ceil($amt/$one_unit_eq);
			}
		}
		return $total_points;
	}
}

if(! function_exists('get_min_redeem_points')){
	function get_min_redeem_points()
	{
		$CI =& get_instance();
		$min_points=0;
		$settings=get_reward_coin_settings();
		if(!empty($settings['min_redeem_value']['key_value'])){
			$min_points = (int) $settings['min_redeem_value']['key_value'];
		}
		return $min_points;
	}
}

if(! function_exists('get_one_coin_value')){
	function get_one_coin_value()
	{
		$CI =& get_instance();
		$one_unit_eq=0;
		$settings=get_reward_coin_settings();
		if(!empty($settings['reward_per_unit']['key_value'])){
			$one_unit_eq = $settings['reward_per_unit']['key_value'];
		}
		return $one_unit_eq;
	}
}

if ( ! function_exists('get_referral_discount')){
	function get_referral_discount(){
		$CI =& get_instance();
		if(empty($CI->configuration_settings_gx['referral_discount'])){
			$configuration_res = $CI->db->get_where('wl_configuration',array('type'=>'referral_discount'))->row_array();
			$CI->configuration_settings_gx['referral_discount']=!empty($configuration_res) ? $configuration_res['value'] : 0;
		}
		return $CI->configuration_settings_gx['referral_discount'];
	}
}

/*
*****Event Based Wallet Coins Entry*****
*/
if ( ! function_exists('check_n_update_credit')){
	function check_n_update_credit($params=array()){
		$CI =& get_instance();
		$CI->load->model('utils_model');
		$log_type=$params['log_type'];
		$settings=get_reward_coin_settings();
		$cur_date=$CI->config->item('config.date');
		$config_wallet_event_types=$CI->config->item('wallet_event_types');
		$coin_validity_res = $CI->db->get_where('wl_configuration',array('type'=>'coin_validity'))->row_array();
		$validity_value = (int) $coin_validity_res['value'];//days
		$expire_date = date("Y-m-d h:i:s",strtotime("+".$validity_value." day"));
		$insert_wallet=0;
		$notify_credited_points=0;
		switch($log_type){
			case 'attempt_poll':
				$activity_type=5;
				$user_id=(int) $params['user_id'];
				$ref_id=(int) $params['ref_id'];
				$setting_entry_value=(int) $settings['attempt_no_of_polls']['key_value'];
				$credited_points=(int) $settings['attempt_no_of_polls']['coins_gained'];
				$log_data=array(
												'activity_type'=>$activity_type,
												'ref_id'=>$ref_id,
												'user_id'=>$user_id,
												'log_date'=>$CI->config->item('config.date.time')
											);
				$insert_log_id = $CI->utils_model->safe_insert('wl_wallet_basic_activity_log',$log_data,FALSE);
				$total_new_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND points_credited=0");
				if($total_new_entry>=$setting_entry_value){
					$insert_wallet=1;
					$repeat_points_credited = floor($total_new_entry/$setting_entry_value);
				}
				if($insert_wallet){
					//taking care of all previous entries that are not credited but condition meet
					while($repeat_points_credited>0){
						$res_entries = $CI->db->select('log_id')->limit($setting_entry_value)->get_where('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND points_credited=0")->result_array();
						if(is_array($res_entries) && !empty($res_entries)){
								$use_event_type=5;
								$wallet_data=array(
															'event_type'=>$use_event_type,
															'transaction_type'=>'Cr',
															'matter_type'=>$config_wallet_event_types[$use_event_type]['title'],
															'user_id'=>$user_id,
															'points'=>$credited_points,
															'left_points'=>$credited_points,
															'user_id'=>$user_id,
															'receive_date'=>$CI->config->item('config.date.time'),
															'expiry_date'=>$expire_date
														);
								$wallet_id = $CI->utils_model->safe_insert('wl_wallet',$wallet_data,FALSE);
								if($wallet_id>0){
									$notify_credited_points = $notify_credited_points+$credited_points;
									foreach($res_entries as $rsval){
										$log_data=array(
															'points_credited'=>1,
															'credited_date'=>$CI->config->item('config.date.time')
														);
										$where_log_update=array(
																						'log_id'=>$rsval['log_id']
																					 );
										$CI->utils_model->safe_update('wl_wallet_basic_activity_log',$log_data,$where_log_update,FALSE);
									}
								}
						}
						$repeat_points_credited--;
					}
				}
			break;
			case 'each_day_login':
				$activity_type=1;
				$user_id=(int) $params['user_id'];
				$setting_entry_value=(int) $settings['every_day_app_login']['key_value'];
				$credited_points=(int) $settings['every_day_app_login']['coins_gained'];
				$total_today_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND DATE(log_date)='$cur_date'");
				if($total_today_entry<=$setting_entry_value){
					$log_data=array(
												'activity_type'=>$activity_type,
												'ref_id'=>0,
												'user_id'=>$user_id,
												'log_date'=>$CI->config->item('config.date.time')
											);
					$insert_log_id = $CI->utils_model->safe_insert('wl_wallet_basic_activity_log',$log_data,FALSE);
					if($insert_log_id>0){
						$total_today_entry++;
					}
					if($total_today_entry==$setting_entry_value){
						$insert_wallet=1;
					}
				}else{
					//Points not credited but condition meets (It is one time activity per day basis)
					$total_credited_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND DATE(log_date)='$cur_date' AND points_credited=1");
					$insert_wallet=$total_credited_entry==0 ? 1 : 0;
				}
				//echo $insert_wallet;die;
				if($insert_wallet){
					$use_event_type=3;
					$wallet_data=array(
												'event_type'=>$use_event_type,
												'transaction_type'=>'Cr',
												'matter_type'=>$config_wallet_event_types[$use_event_type]['title'],
												'user_id'=>$user_id,
												'points'=>$credited_points,
												'left_points'=>$credited_points,
												'user_id'=>$user_id,
												'receive_date'=>$CI->config->item('config.date.time'),
												'expiry_date'=>$expire_date
											);
					$wallet_id = $CI->utils_model->safe_insert('wl_wallet',$wallet_data,FALSE);
					if($wallet_id>0){
						$notify_credited_points = $notify_credited_points+$credited_points;
						$log_data=array(
											'points_credited'=>1,
											'credited_date'=>$CI->config->item('config.date.time')
										);
						$where_log_update=array(
																			'activity_type'=>$activity_type,
																			'ref_id'=>0,
																			'user_id'=>$user_id,
																			'DATE(log_date)'=>$cur_date
																		);
						$CI->utils_model->safe_update('wl_wallet_basic_activity_log',$log_data,$where_log_update,FALSE);
					}
				}
			break;
			case 'refer_register':
				$activity_type=2;
				$user_id=(int) $params['user_id'];
				$ref_id=(int) $params['ref_id'];
				$setting_entry_value=(int) $settings['referred_friend_register']['key_value'];
				$credited_points=(int) $settings['referred_friend_register']['coins_gained'];
				$res_exists = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND ref_id=$ref_id AND points_credited=0");
				if(!$res_exists){
					$log_data=array(
													'activity_type'=>$activity_type,
													'ref_id'=>$ref_id,
													'user_id'=>$user_id,
													'log_date'=>$CI->config->item('config.date.time')
												);
					$insert_log_id = $CI->utils_model->safe_insert('wl_wallet_basic_activity_log',$log_data,FALSE);
					$total_new_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND points_credited=0");
					if($total_new_entry>=$setting_entry_value){
						$insert_wallet=1;
						$repeat_points_credited = floor($total_new_entry/$setting_entry_value);
					}
					if($insert_wallet){
						//taking care of all previous entries that are not credited but condition meet
						while($repeat_points_credited>0){
							$res_entries = $CI->db->select('log_id')->limit($setting_entry_value)->get_where('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND points_credited=0")->result_array();
							if(is_array($res_entries) && !empty($res_entries)){
									$use_event_type=7;
									$wallet_data=array(
																'event_type'=>$use_event_type,
																'transaction_type'=>'Cr',
																'matter_type'=>$config_wallet_event_types[$use_event_type]['title'],
																'user_id'=>$user_id,
																'points'=>$credited_points,
																'left_points'=>$credited_points,
																'user_id'=>$user_id,
																'receive_date'=>$CI->config->item('config.date.time'),
																'expiry_date'=>$expire_date
															);
									$wallet_id = $CI->utils_model->safe_insert('wl_wallet',$wallet_data,FALSE);
									if($wallet_id>0){
										$notify_credited_points = $notify_credited_points+$credited_points;
										foreach($res_entries as $rsval){
											$log_data=array(
																'points_credited'=>1,
																'credited_date'=>$CI->config->item('config.date.time')
															);
											$where_log_update=array(
																							'log_id'=>$rsval['log_id']
																						 );
											$CI->utils_model->safe_update('wl_wallet_basic_activity_log',$log_data,$where_log_update,FALSE);
										}
									}
							}
							$repeat_points_credited--;
						}
					}
				}
			break;
			case 'attempt_mock_test':
			case 'attempt_live_test':
				$activity_type=$log_type=='attempt_mock_test' ? 4 : 7;
				$user_id=(int) $params['user_id'];
				$ref_id=(int) $params['ref_id'];
				$setting_entry_value=(int) $settings['attempting_mock_test']['key_value'];
				$credited_points=(int) $settings['attempting_mock_test']['coins_gained'];
				$res_exists = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND ref_id=$ref_id AND points_credited=0");
				if(!$res_exists){
					$log_data=array(
													'activity_type'=>$activity_type,
													'ref_id'=>$ref_id,
													'user_id'=>$user_id,
													'log_date'=>$CI->config->item('config.date.time')
												);
					$insert_log_id = $CI->utils_model->safe_insert('wl_wallet_basic_activity_log',$log_data,FALSE);
					$total_new_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND points_credited=0");
					if($total_new_entry>=$setting_entry_value){
						$insert_wallet=1;
						$repeat_points_credited = floor($total_new_entry/$setting_entry_value);
					}
					if($insert_wallet){
						//taking care of all previous entries that are not credited but condition meet
						while($repeat_points_credited>0){
							$res_entries = $CI->db->select('log_id')->limit($setting_entry_value)->get_where('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND points_credited=0")->result_array();
							if(is_array($res_entries) && !empty($res_entries)){
									$use_event_type=$log_type=='attempt_mock_test' ? 9 : 10;
									$wallet_data=array(
																'event_type'=>$use_event_type,
																'transaction_type'=>'Cr',
																'matter_type'=>$config_wallet_event_types[$use_event_type]['title'],
																'points'=>$credited_points,
																'left_points'=>$credited_points,
																'user_id'=>$user_id,
																'receive_date'=>$CI->config->item('config.date.time'),
																'expiry_date'=>$expire_date
															);
									$wallet_id = $CI->utils_model->safe_insert('wl_wallet',$wallet_data,FALSE);
									if($wallet_id>0){
										$notify_credited_points = $notify_credited_points+$credited_points;
										foreach($res_entries as $rsval){
											$log_data=array(
																'points_credited'=>1,
																'credited_date'=>$CI->config->item('config.date.time')
															);
											$where_log_update=array(
																							'log_id'=>$rsval['log_id']
																						 );
											$CI->utils_model->safe_update('wl_wallet_basic_activity_log',$log_data,$where_log_update,FALSE);
										}
									}
							}
							$repeat_points_credited--;
						}
					}
				}
			break;
			case 'first_register':
				$activity_type=3;
				$user_id=(int) $params['user_id'];
				$credited_points=(int) $settings['first_time_register']['coins_gained'];
				$total_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id");
				if(!$total_entry){
					$log_data=array(
												'activity_type'=>$activity_type,
												'ref_id'=>0,
												'user_id'=>$user_id,
												'log_date'=>$CI->config->item('config.date.time')
											);
					$insert_log_id = $CI->utils_model->safe_insert('wl_wallet_basic_activity_log',$log_data,FALSE);
					if($insert_log_id>0){
						$insert_wallet=1;
					}
				}else{
					//Points not credited but condition meets (It is at most one time activity)
					$total_credited_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND points_credited=1");
					$insert_wallet=$total_credited_entry==0 ? 1 : 0;
				}
				if($insert_wallet){
					$use_event_type=4;
					$wallet_data=array(
												'event_type'=>$use_event_type,
												'transaction_type'=>'Cr',
												'matter_type'=>$config_wallet_event_types[$use_event_type]['title'],
												'user_id'=>$user_id,
												'points'=>$credited_points,
												'left_points'=>$credited_points,
												'user_id'=>$user_id,
												'receive_date'=>$CI->config->item('config.date.time'),
												'expiry_date'=>$expire_date
											);
					$wallet_id = $CI->utils_model->safe_insert('wl_wallet',$wallet_data,FALSE);
					if($wallet_id>0){
						$notify_credited_points = $notify_credited_points+$credited_points;
						$log_data=array(
											'points_credited'=>1,
											'credited_date'=>$CI->config->item('config.date.time')
										);
						$where_log_update=array(
																			'activity_type'=>$activity_type,
																			'ref_id'=>0,
																			'user_id'=>$user_id
																		);
						$CI->utils_model->safe_update('wl_wallet_basic_activity_log',$log_data,$where_log_update,FALSE);
					}
				}
			break;
			case 'approved_post':
				$activity_type=6;
				$user_id=(int) $params['user_id'];
				$ref_id=(int) $params['ref_id'];
				$setting_entry_value=(int) $settings['on_approved_post']['key_value'];
				$credited_points=(int) $settings['on_approved_post']['coins_gained'];
				$res_exists = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND ref_id=$ref_id AND points_credited=0");
				if(!$res_exists){
					$log_data=array(
													'activity_type'=>$activity_type,
													'ref_id'=>$ref_id,
													'user_id'=>$user_id,
													'log_date'=>$CI->config->item('config.date.time')
												);
					$insert_log_id = $CI->utils_model->safe_insert('wl_wallet_basic_activity_log',$log_data,FALSE);
					$total_new_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND points_credited=0");
					if($total_new_entry>=$setting_entry_value){
						$insert_wallet=1;
						$repeat_points_credited = floor($total_new_entry/$setting_entry_value);
					}
					if($insert_wallet){
						//taking care of all previous entries that are not credited but condition meet
						while($repeat_points_credited>0){
							$res_entries = $CI->db->select('log_id')->limit($setting_entry_value)->get_where('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND points_credited=0")->result_array();
							if(is_array($res_entries) && !empty($res_entries)){
									$use_event_type=8;
									$wallet_data=array(
																'event_type'=>$use_event_type,
																'transaction_type'=>'Cr',
																'matter_type'=>$config_wallet_event_types[$use_event_type]['title'],
																'points'=>$credited_points,
																'left_points'=>$credited_points,
																'user_id'=>$user_id,
																'receive_date'=>$CI->config->item('config.date.time'),
																'expiry_date'=>$expire_date
															);
									$wallet_id = $CI->utils_model->safe_insert('wl_wallet',$wallet_data,FALSE);
									if($wallet_id>0){
										$notify_credited_points = $notify_credited_points+$credited_points;
										foreach($res_entries as $rsval){
											$log_data=array(
																'points_credited'=>1,
																'credited_date'=>$CI->config->item('config.date.time')
															);
											$where_log_update=array(
																							'log_id'=>$rsval['log_id']
																						 );
											$CI->utils_model->safe_update('wl_wallet_basic_activity_log',$log_data,$where_log_update,FALSE);
										}
									}
							}
							$repeat_points_credited--;
						}
					}
				}
			break;
			case 'refer_first_purchase':
				$activity_type=3;
				$user_id=(int) $params['user_id'];
				$ref_id=(int) $params['user_id'];
				$credited_points=(int) $settings['friend_buy_first_time']['coins_gained'];
				$total_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id");
				if(!$total_entry){
					$log_data=array(
												'activity_type'=>$activity_type,
												'ref_id'=>$ref_id,
												'user_id'=>$user_id,
												'log_date'=>$CI->config->item('config.date.time')
											);
					$insert_log_id = $CI->utils_model->safe_insert('wl_wallet_basic_activity_log',$log_data,FALSE);
					if($insert_log_id>0){
						$insert_wallet=1;
					}
				}else{
					//Points not credited but condition meets (It is at most one time activity)
					$total_credited_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND points_credited=1");
					$insert_wallet=$total_credited_entry==0 ? 1 : 0;
				}
				if($insert_wallet){
					$use_event_type=6;
					$wallet_data=array(
												'event_type'=>$use_event_type,
												'transaction_type'=>'Cr',
												'matter_type'=>$config_wallet_event_types[$use_event_type]['title'],
												'user_id'=>$user_id,
												'points'=>$credited_points,
												'left_points'=>$credited_points,
												'user_id'=>$user_id,
												'receive_date'=>$CI->config->item('config.date.time'),
												'expiry_date'=>$expire_date
											);
					$wallet_id = $CI->utils_model->safe_insert('wl_wallet',$wallet_data,FALSE);
					if($wallet_id>0){
						$notify_credited_points = $notify_credited_points+$credited_points;
						$log_data=array(
											'points_credited'=>1,
											'credited_date'=>$CI->config->item('config.date.time')
										);
						$where_log_update=array(
																			'activity_type'=>$activity_type,
																			'ref_id'=>$ref_id,
																			'user_id'=>$user_id
																		);
						$CI->utils_model->safe_update('wl_wallet_basic_activity_log',$log_data,$where_log_update,FALSE);
					}
				}
			break;
			case 'order_cancel_reversal':
			/*Please check wallet amount is used or not before using this case.It must be pre-checked before calling this function*/
				$activity_type=8;
				$user_id=(int) $params['user_id'];
				$ref_id=(int) $params['ref_id'];
				$credited_points=(int) $params['credits_used'];
				$total_credited_entry = (int) $CI->utils_model->findCountV1('wl_wallet_basic_activity_log',"activity_type=$activity_type AND user_id=$user_id AND ref_id=$ref_id");
				$insert_wallet=$total_credited_entry==0 ? 1 : 0;
				if($insert_wallet){
					$use_event_type=11;
					$wallet_data=array(
												'event_type'=>$use_event_type,
												'transaction_type'=>'Cr',
												'matter_type'=>$config_wallet_event_types[$use_event_type]['title'],
												'matter_id'=>$ref_id,
												'user_id'=>$user_id,		
												'points'=>$credited_points,
												'left_points'=>$credited_points,
												'receive_date'=>$CI->config->item('config.date.time'),
												'expiry_date'=>$expire_date
											);
					$wallet_id = $CI->utils_model->safe_insert('wl_wallet',$wallet_data,FALSE);
					if($wallet_id>0){
						$notify_credited_points = $notify_credited_points+$credited_points;
						$log_data=array(
											'points_credited'=>1,
											'credited_date'=>$CI->config->item('config.date.time')
										);
						$where_log_update=array(
																			'activity_type'=>$activity_type,
																			'ref_id'=>$ref_id,
																			'user_id'=>$user_id
																		);
						$CI->utils_model->safe_update('wl_wallet_basic_activity_log',$log_data,$where_log_update,FALSE);

						$order_data=array(
											'is_order_reverted'=>1,
											'order_status'=>'Cancelled'
										);
						$where_order_update=array(
																			'order_id'=>$ref_id
																		);
						$CI->utils_model->safe_update('wl_order',$order_data,$where_order_update,FALSE);
					}
				}
			break;
		}
		if($insert_wallet && $notify_credited_points>0){
			//Send Notification to RT server if any
			if(empty($CI->custom_notification)){
				$CI->load->library('Custom_notification');
			}
			$msg_title = $config_wallet_event_types[$use_event_type]['title'];
			$msg_desc = "You got ".$notify_credited_points." point".($notify_credited_points>0 ? 's' : '')." expiring on ".date("M d,Y",strtotime($expire_date)).".";
			$url_hint = 'wallet';
			$url_params=array();
			$params_notification = array(
																	'nf_type'=>1,
																	'message_title'=>$msg_title,
																	'message_desc'=>$msg_desc,
																	'user_id'=>$user_id,
																	'notification_type'=>'both',
																	'url_hint'=>$url_hint,
																	'url_params'=>$url_params	
																);
			$CI->custom_notification->send_notification($params_notification);
		}
	}
}
if(! function_exists('get_max_redeem_points')){
	function get_max_redeem_points()
	{
		$CI =& get_instance();
		$max_points=0;
		$settings=get_reward_coin_settings();
		if(!empty($settings['max_redeem_value']['key_value'])){
			$max_points = (int) $settings['max_redeem_value']['key_value'];
		}
		return $max_points;
	}
}