<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Custom_notification{
	public $ci;
	public function __construct(){
		if (!isset($this->ci)){
			$this->ci =& get_instance();
		}
		$this->ci->load->helper('push_notification');
	}
	//When user successfully login
	public function user_login($data=array()){
		$msg_title = "Login";
		$msg_desc = "You Logged in at ".date("M d Y h:i:A",strtotime($data['login_dt']));
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	//When user successfully register
	public function user_register($data=array()){
		$msg_title = "Registration";
		$msg_desc = "Thank you to register at ".$this->ci->config->item('site_name');
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	//When user successfully updated mobile
	public function user_mobile_success_update($data=array()){
		$msg_title = "Mobile Updation";
		$msg_desc = "Your mobile number ".$data['new_no']." has been successfully updated at ".date("M d Y h:i:A",strtotime($data['update_dt']));
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	//When user changes mobile
	public function user_mobile_update_request($data=array()){
		$msg_title = "Mobile No. Change Request";
		$msg_desc = "You have changed your mobile number to ".$data['new_no'].". Please verify it.";
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	//When user changes username
	public function user_email_update_request($data=array()){
		$msg_title = "Email Change Request";
		$msg_desc = "You have changed your email to ".$data['new_email'].". Please verify it.";
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	//When user successfully verified email
	public function user_email_verified($data=array()){
		$msg_title = "Email verified!";
		$msg_desc = "Your email ".$data['email']."  has been successfully verified.";
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	//When user update profile picture
	public function user_profile_update($data=array()){
		if($data['activity']=='remove'){
			$msg_title = "Profile Picture Deletion";
			$msg_desc = "You have removed your profile picture";
		}else{
			$msg_title = "Profile Picture Updation";
			$msg_desc = "You have updated your profile picture";
		}
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	//When user post message from message section
	public function user_post_message_msg_section($data=array()){
		$msg_title = "Message Post";
		$msg_desc = "You have posted a message";
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	//When user create post
	public function user_create_post($data=array()){
		$config_post_types_arr = $this->ci->config->item('config_post_types_arr');
		$config_post_content_type_arr = $this->ci->config->item('config_post_content_type_arr');
		$post_type_hint = (!empty($config_post_types_arr[$data['post_type']]) ? $config_post_types_arr[$data['post_type']] : '');
		$msg_title = $post_type_hint." Post Created";
		$msg_desc = "You have created a ".$post_type_hint." post ".$data['post_title'];
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	//When user create post
	public function user_update_post($data=array()){
		$config_post_types_arr = $this->ci->config->item('config_post_types_arr');
		$config_post_content_type_arr = $this->ci->config->item('config_post_content_type_arr');
		$post_type_hint = (!empty($config_post_types_arr[$data['post_type']]) ? $config_post_types_arr[$data['post_type']] : '');
		$msg_title = $post_type_hint." Post Updated";
		$msg_desc = "You have updated a ".$post_type_hint." post ".$data['post_title'];
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	//When user delete post
	public function user_delete_post($data=array()){
		$config_post_types_arr = $this->ci->config->item('config_post_types_arr');
		$config_post_content_type_arr = $this->ci->config->item('config_post_content_type_arr');
		$post_type_hint = (!empty($config_post_types_arr[$data['post_type']]) ? $config_post_types_arr[$data['post_type']] : '');
		$msg_title = $post_type_hint." Post Deleted";
		$msg_desc = "You have deleted a ".$post_type_hint." post ".$data['post_title'];
		$user_id = $data['user_id'];
		$params_notification = array(
															'nf_type'=>1,
															'message_title'=>$msg_title,
															'message_desc'=>$msg_desc,
															'user_id'=>$user_id,
															'notification_type'=>'log'
														);
		//$this->send_notification($params_notification);
	}

	


	//Common Interface to send notification of various activity
	//Please dont use it for admin scheduled notification. As it is going through cron & Entry is already exists for each member to receive the notification
	public function send_notification($params=array()){
		$message_title = !empty($params['message_title']) ? $params['message_title'] : "Notification";
		$message_desc = $params['message_desc'];
		//Possible value - log,both(push+log)
		//You can change as per your requirement
		$notification_type = empty($params['notification_type']) ? 'both' : $params['notification_type'];
		$nf_type = empty($params['nf_type']) ? 1 : $params['nf_type'];
		$member_type = empty($params['member_type']) ? 3 : $params['member_type'];
		$user_ids = (is_array($params['user_id']) && !empty($params['user_id'])) ? $params['user_id'] : array($params['user_id']);
		if(!empty($user_ids)){
			//Log the Activity
			$url_hint = empty($params['url_hint']) ? '' : $params['url_hint'];
			$url_params = (empty($params['url_params']) || !is_array($params['url_params'])) ? array() : $params['url_params'];
			$message_icon=!empty($params['message_icon']) ? $params['message_icon'] : '';
			$message_icon_path=!empty($params['icon_path']) ? $params['icon_path'] : '';
			if($message_icon!='' && $message_icon_path!='' && file_exists(UPLOAD_DIR."/".$message_icon_path."/".$message_icon)){
				$push_message_icon=base_url()."uploaded_files/".$message_icon_path."/".$message_icon;
			}else{
				$push_message_icon="";
			}
			$message_image=!empty($params['message_image']) ? $params['message_image'] : '';
			$message_image_path_path=!empty($params['image_path']) ? $params['image_path'] : '';
			if($message_image!='' && $message_image_path_path!='' && file_exists(UPLOAD_DIR."/".$message_image_path_path."/".$message_image)){
				$push_message_image=base_url()."uploaded_files/".$message_image_path_path."/".$message_image;
			}else{
				$push_message_image="";
			}
			$nf_master_id=empty($params['nf_master_id']) ? 0 : $params['nf_master_id'];
			if(!$nf_master_id){
				
				$data_insert_nf = array(
														'nf_type'=>$nf_type,
														'member_type'=>$member_type,
														'notification_title'=>$message_title,
														'notification_image'=>$message_icon,
														'description'=>$message_desc,
														'created_at'=>$this->ci->config->item('config.date.time'),
														'status'=>1,
														'url_hint'=>$url_hint,
														'url_params'=>serialize($url_params)
													);
				$nf_qstr = $this->ci->db->insert_string('wl_notification',$data_insert_nf);
				$this->ci->db->query($nf_qstr);
				$nf_master_id = $this->ci->db->insert_id();
			}
			if($nf_master_id>0){
				foreach($user_ids as $val){
					$data_insert_nf_mem = array(
													'notification_id'=>$nf_master_id,
													'customer_id'=>$val,
													'created_at'=>$this->ci->config->item('config.date.time')
												);
					$nf_mem_qstr = $this->ci->db->insert_string('wl_notification_customer',$data_insert_nf_mem);
					$this->ci->db->query($nf_mem_qstr);
				}
			}
			if($notification_type=='both' || $notification_type=='push'){
				$params_nf_url = array('url_hint'=>$url_hint,'url_params'=>$url_params);
				$notification_url_params = $this->format_notification_params($params_nf_url);
				foreach($user_ids as $val){
					$message_array  = array(
										"message_title"=>$message_title,
										"message"=>$message_desc,
										"icon"=>$push_message_icon,
										"message_image"=>$push_message_image,
										"notification_url_params"=>$notification_url_params,
										"pem_file" => "pushCert_JGRider");
					set_apps_notification($val,$message_array);
				}
			}
		}
	}

	/*Can be customized as per your need*/
	public function format_notification_params($params=array()){
		$ret_params=array();
		$url_hint = empty($params['url_hint']) ? '' : $params['url_hint'];
		$url_params = (!empty($params['url_params']) && is_array($params['url_params'])) ? $params['url_params'] : (!empty($params['url_params']) ? unserialize($params['url_params']) : array());
		/*Function for handle special case*/
		$fn_refactor_params = function($url_params){
			$ret_params=array('hint'=>'default','id1'=>0);
			switch($url_params['prod_type']){
				case 1:
					$ret_params=array('hint'=>'course_detail','id1'=>$url_params['detail_id']);
				break;
				case 2:
					$ret_params=array('hint'=>'mtp_detail','id1'=>$url_params['detail_id']);
				break;
				case 3:
					$ret_params=array('hint'=>'video_course_detail','id1'=>$url_params['detail_id']);
				break;
				case 4:
					$ret_params=array('hint'=>'notes_detail','id1'=>$url_params['detail_id']);
				break;
				case 5:
					$ret_params=array('hint'=>'subscription','id1'=>$url_params['root_category_id']);
				break;
			}
			return $ret_params;
		};
		switch($url_hint){
			case  'cat_sub_expiry':
			case  'course_sub_expiry':
			case  'vcourse_sub_expiry':
			case  'notes_sub_expiry':
			case  'ts_sub_expiry':
				$ret_params = array('page'=>$url_hint,'id1'=>(!empty($url_params['subscription_id']) ? $url_params['subscription_id'] : ''),'id2'=>'');
			break;
			case  'live_class_dtls':
				$ret_params = array('page'=>$url_hint,'id1'=>(!empty($url_params['live_class_id']) ? $url_params['live_class_id'] : ''),'id2'=>'');
			break;
			case  'live_mt_dtls':
				$ret_params = array('page'=>$url_hint,'id1'=>(!empty($url_params['live_mt_id']) ? $url_params['live_mt_id'] : ''),'id2'=>'');
			break;
			case  'live_result_dtls':
				$ret_params = array('page'=>$url_hint,'id1'=>(!empty($url_params['master_test_id']) ? $url_params['master_test_id'] : ''),'id2'=>(!empty($url_params['user_mt_attempt_id']) ? $url_params['user_mt_attempt_id'] : ''));
			break;
			case  'nf_by_cat':
				if(!empty($url_params['prod_type'])){
					$refactored_params = $fn_refactor_params($url_params);
					$ret_params = array('page'=>$refactored_params['hint'],'id1'=>$refactored_params['id1'],'id2'=>'');
				}else{
					$ret_params = array('page'=>$url_hint,'id1'=>(!empty($url_params['root_category_id']) ? $url_params['root_category_id'] : ''),'id2'=>'');
				}
			break;
			case  'nf_by_search':
				$ret_params = array('page'=>$url_hint,'id1'=>'','id2'=>'');
			break;
			case  'nf_course_by_expired':
				if(!empty($url_params['prod_type'])){
					$refactored_params = $fn_refactor_params($url_params);
					$ret_params = array('page'=>$refactored_params['hint'],'id1'=>$refactored_params['id1'],'id2'=>'');
				}else{
					$ret_params = array('page'=>$url_hint,'id1'=>'','id2'=>'');
				}
			break;
			case  'nf_ts_by_expired':
				if(!empty($url_params['prod_type'])){
					$refactored_params = $fn_refactor_params($url_params);
					$ret_params = array('page'=>$refactored_params['hint'],'id1'=>$refactored_params['id1'],'id2'=>'');
				}else{
					$ret_params = array('page'=>$url_hint,'id1'=>'','id2'=>'');
				}
			break;
			case  'nf_vc_by_expired':
				if(!empty($url_params['prod_type'])){
					$refactored_params = $fn_refactor_params($url_params);
					$ret_params = array('page'=>$refactored_params['hint'],'id1'=>$refactored_params['id1'],'id2'=>'');
				}else{
					$ret_params = array('page'=>$url_hint,'id1'=>'','id2'=>'');
				}
			break;
			case  'nf_notes_by_expired':
				if(!empty($url_params['prod_type'])){
					$refactored_params = $fn_refactor_params($url_params);
					$ret_params = array('page'=>$refactored_params['hint'],'id1'=>$refactored_params['id1'],'id2'=>'');
				}else{
					$ret_params = array('page'=>$url_hint,'id1'=>'','id2'=>'');
				}
			break;
			case  'nf_sub_by_expired':
				if(!empty($url_params['prod_type'])){
					$refactored_params = $fn_refactor_params($url_params);
					$ret_params = array('page'=>$refactored_params['hint'],'id1'=>$refactored_params['id1'],'id2'=>'');
				}else{
					$ret_params = array('page'=>$url_hint,'id1'=>'','id2'=>'');
				}
			break;
			case  'nf_by_non_purchased':
				if(!empty($url_params['prod_type'])){
					$refactored_params = $fn_refactor_params($url_params);
					$ret_params = array('page'=>$refactored_params['hint'],'id1'=>$refactored_params['id1'],'id2'=>'');
				}else{
					$ret_params = array('page'=>$url_hint,'id1'=>'','id2'=>'');
				}
			break;
			case  'wallet':
				$ret_params = array('page'=>$url_hint,'id1'=>'','id2'=>'');
			break;
			case  'order_purchased':
				$ret_params = array('page'=>$url_hint,'id1'=>(!empty($url_params['order_id']) ? $url_params['order_id'] : ''),'id2'=>(!empty($url_params['order_token_id']) ? $url_params['order_token_id'] : ''));
			break;
			default:
				$ret_params = array('page'=>$url_hint,'id1'=>'','id2'=>'');
		}
		return $ret_params;
	}

	/*Can be customized as per your need*/
	public function create_notification_url($params=array()){
		$ret_url=base_url().'webview';
		$url_hint = empty($params['url_hint']) ? '' : $params['url_hint'];
		$url_params = $this->format_notification_params($params);
		//$url_params = (!empty($params['url_params']) && is_array($params['url_params'])) ? $params['url_params'] : (!empty($params['url_params']) ? unserialize($params['url_params']) : array());
		switch($url_hint){
			default:
				$ret_url.=(!empty($url_params) ? '?'.http_build_query($url_params) : '');
		}
		return $ret_url;
	}

	public function unsubscribe_customer_on_purchase($params=array()){
		$order_type = isset($params['order_type']) ?  (int) $params['order_type'] : -1;
		$customer_id = isset($params['customer_id']) ?  (int) $params['customer_id'] : 0;
		switch($order_type){
				case 1:
					$ref_type_id=5;
				break;
				case 2:
					$ref_type_id=1;
				break;
				case 3:
					$ref_type_id=3;
				break;
				case 4:
					$ref_type_id=4;
				break;
				case 5:
					$ref_type_id=2;
				break;
				default:
					$ref_type_id=0;
		}

		$qry_notification_del = "DELETE b.* FROM wl_ref_notification_members as b JOIN wl_notification as a ON a.notification_id=b.ref_nf_id WHERE a.nf_type=2 AND  a.mem_by_type IN(3,5) AND b.user_id='".$customer_id."'";
		if($ref_type_id>0){
			$qry_notification_del .= " AND IF(a.mem_by_type=3,a.ref_type_id=$ref_type_id,1)";
		}
		$this->ci->db->query($qry_notification_del);
		$qry_group_mem_del = "DELETE b.* FROM wl_ref_group_members as b JOIN wl_member_groups as a ON a.group_id=b.ref_group_id WHERE  a.mem_by_type IN(3,5) AND b.user_id='".$customer_id."'";
		if($ref_type_id>0){
			$qry_group_mem_del .= " AND IF(a.mem_by_type=3,a.ref_type_id=$ref_type_id,1)";
		}
		$this->ci->db->query($qry_group_mem_del);
	}

}
/*End of file*/