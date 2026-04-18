<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/*** The global CI helpers ******/ 
if ( ! function_exists('CI'))
{
	function CI()
	{
	   if (!function_exists('get_instance')) return FALSE;
		
		$CI = &get_instance();		
		return $CI;
	}
}

if ( ! function_exists('get_device_info'))
{
	function get_device_info($userId)    
	{
		if($userId !='' && $userId > 0 )
		{
			$ci = CI();
			/*$ci->db->select('api_token_id,app_id,app_type,token,user_id as user_id');
			$ci->db->order_by('api_token_id DESC');
			$result =  $ci->db->get_where('wl_apl_tokens',array('user_id'=>$userId))->row_array();*/

			$result =  $ci->db->select('app_id,app_type')->get_where('wl_customers',array('customers_id'=>$userId ))->row_array();
			
			//$result = $thid->db->query("SELECT ");			
			return $result;	
		}		
	}
}


if ( ! function_exists('set_apps_notification'))
{
	function set_apps_notification($userId,$message)    
	{
		$CI = &get_instance();		
		if($userId!='' && $userId > 0  && $message!='')
		{
			//$CI->load->model('notification/notification_model');
			
			/*$post_notification = array(
									'user_id'			=> $userId,
									'notification_title'=> $message['message_title'],
									'created_at'		=> $CI->config->item('config.date.time'),
									'status'			=> '1'
									);
			$CI->notification_model->safe_insert('wl_notification',$post_notification,FALSE);*/
			
			$device_res =  get_device_info($userId);
			
			//trace($device_res);
					 
			if( is_array($device_res) && !empty($device_res))
			{
     			$device_id   = $device_res['app_id']?$device_res['app_id']:''; 
				$device_type = strtolower($device_res['app_type']);

				$registration_ids = $device_id; 
				$pem_file         = ($message['pem_file']);
				$app_message      = ($message['message']);
				$message_title    = ($message['message_title']);
				$tbl    		   = (@$message['tbl']);
				$user_id	       = ($userId);

				$notification_url_params    = !empty($message['notification_url_params']) ?  $message['notification_url_params']  :  '';

				$notification_message_image    = !empty($message['message_image']) ?  $message['message_image']  :  '';

				$debug    = !empty($message['debug']) ?  $message['debug']  :  0;
					 
				$msg_payload = array(
								'debug'=>$debug,
								'mtitle' => $message_title,
								'mdesc' => $app_message,
								'pem_file' => $pem_file,
								'user_id' => $user_id,
								'notification_url_params'=>$notification_url_params,
								'notification_message_image'=>$notification_message_image,
								'tbl' => $tbl);

			

				if( $device_id!='' && $device_type=='android' ){

					return send_notification_android($registration_ids,$msg_payload);	

				}else if($device_id!='' && $device_type=='ios'){

					return send_notification_iphone($registration_ids,$msg_payload);

				}				
			}
		}
	}
}


if ( ! function_exists('send_notification_android')){	

function send_notification_android($devicetoken, $data)
{		 
	$serverKey = 'AAAAP8EqUvc:APA91bF_-4KZtaDVAoM6AA7XTt7nQUj0Y2W3lo-2UK9FFfv69Uve8J9U2PwYF2T46YqyMZMy08XHKt9FNNRDB_YD5zl8CUsVOJJSy7gJbNktV1AeRcgD7U2KzjNFtlGhh7tE8HsWc72U';
	    
		 
       $msg = array(
		'body'=>$data['mdesc'],
		'title'=>$data['mtitle'],
		'icon'=>!empty($data['icon']) ? $data['icon'] : '',
		'sound'=>'',
		'color'=>'#151515',
		"click_action"=>"OPEN_ACTIVITY_1",
		);
		
	  $payload = array();
	  $payload['team'] = 'India';
	  $payload['score'] = '5.6';
		
		
		$title = $data['mtitle'];
		$body = $data['mdesc'];
		
		
		$notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => '1','click_action'=>'OPEN_ACTIVITY_1');

		if(!empty($data['notification_message_image'])){
			$notification['image'] = $data['notification_message_image'];
		}

		

		$payload_data = array(
									'priority'=>'high',
									'payload'=>$payload,
									'message'=>$data['mdesc'],
									'timestamp'=>date('Y-m-d G:i:s')
							);

		if(!empty($data['notification_url_params'])){
			$payload_data['page'] = !empty($data['notification_url_params']['page']) ? $data['notification_url_params']['page'] : "";
			$payload_data['id1'] = !empty($data['notification_url_params']['id1']) ? $data['notification_url_params']['id1'] : "";
			$payload_data['id2'] = !empty($data['notification_url_params']['id2']) ? $data['notification_url_params']['id2'] : "";
		}
		
		//$devicetoken = "fEOKATmBmE4dn7jKnXmP-N:APA91bFSsLdcf_PTU4_T4A1aailYVaxzOikkiJcyng_IcQk6H7NG6UzRdzJXW1bBeGb2n0-i4sMbfBbfZ5-23VrQfhIIKLqTeQ7p8ml4NRzJqa7xaMsydIucd6paX1y06UfPDiwJTt5N";
		
		$arrayToSend = array('to' => $devicetoken, 'notification' => $notification,'data'=>$payload_data);
		$json = json_encode($arrayToSend);
		
		if(!empty($data['debug'])){
			trace($arrayToSend);	
			die;
		}
		
		/*$fields = array('data'=>array('data'=>array('title'=>$data['mtitle'],
									  'is_background'=>false,
									  'message'=>$data['mdesc'],
									  'image'=>'',
									  'payload'=>$payload,
									  'timestamp'=>date('Y-m-d G:i:s')
						)),'to' => $devicetoken);*/
						
						
		//trace($fields);exit;
		$url = 'https://fcm.googleapis.com/fcm/send';
		
        /*$headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );*/
        // Open connection
		
        $headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: key='. $serverKey;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = @curl_exec($ch);					
		//trace($response);
		

		if ($response === FALSE) {
			$ret_arr = array('err'=>TRUE,'msg'=>'FCM Send Error: ' . curl_error($ch));
			return $ret_arr;
		}
		curl_close($ch);
		
		//trace($response);
		$ret_arr = array('err'=>FALSE,'msg'=>'');
		return $ret_arr;
	}
	
	
}



if ( ! function_exists('send_notification_iphone'))
{
	function send_notification_iphone($devicetoken, $data)
	{
		if($devicetoken!=''){
		//$ch = curl_init("https://fcm.googleapis.com/fcm/send");
		$url = "https://fcm.googleapis.com/fcm/send";
		$serverKey = 'AAAA60lb-RY:APA91bENO_R93UgzLtbdjdFsfdATE1IPkh7zr_KiMHCqoTprEs6STO7xMYa2Uv6-Yd2k3RhZNlSMQBcc0OemyDRBuwpD3djWvw7JqvFG5JJMFIUO5Uh3noheV0_nz-ceywLO_Dzdkeql';	

    //The device token.
    $token = $devicetoken;

    //Title of the Notification.
    $title = $data['mtitle'];

    //Body of the Notification.
    $body = $data['mdesc'];

    //Creating the notification array.
    //$notification = array('title' =>$title , 'text' => $body);

    //This array contains, the token and the notification. The 'to' attribute stores the token.
   // $arrayToSend = array('to' => $token, 'notification' => $notification);
	
	$notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => '1');

	$arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
					
    //Generating JSON encoded string form the above array.
    $json = json_encode($arrayToSend);
	
	//trace($json);

    //Setup headers:
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='.$serverKey; //server key here

    //Setup curl, add headers and post parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     

    //Send the request
    $response = curl_exec($ch);
	trace($response);
	//echo $response; die;

       if ($response === FALSE)
		{
			$error = curl_error($ch);
			echo $error;
			die('error occured');
		}else
		{
		}
		
    //Close request
    curl_close($ch);
   // return $response;
   
   //exit;
		}
		
	}
}

	/*
	function name: send_ios_notification
	@param: deviceToken
	@param: message
	*/
  // Sends Push notification for iOS users
	/*function send_notification_iphone($devicetoken, $data) {
		
		$ci = CI();
		$passphrase = '';
		//$pem_name = (array_key_exists('pem_file',$data)) ? $data['pem_file']: 'pushCert_JGDriver';
		//$tCert = UPLOAD_DIR."/push_notification/".$pem_name.".pem";
		$token = $devicetoken;
		//$tCert = UPLOAD_DIR."/push_notification/CertificatesDevelopment.pem";
		$tCert = UPLOAD_DIR."/push_notification/pushcertDev.pem";
		
		$apnsHost = 'gateway.sandbox.push.apple.com';
		$apnsCert = $tCert;
		$apnsPort = 2195;
		$streamContext = stream_context_create();
		stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
		$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
		$payload['aps'] = array('alert' =>  array('title' => $data['mtitle'],'body' => $data['mdesc']), 'badge' => 1, 'sound' => 'default');
		$output = json_encode($payload);
		$token = pack('H*', str_replace(' ', '', $token));
		$apnsMessage = chr(0) . chr(0) . chr(32) . $token . chr(0) . chr(strlen($output)) . $output;
		$result = fwrite($apns, $apnsMessage);
		@socket_close($apns);
		if ($result)
		{
			echo 'MESSAGE SENT TO ASPN';
			return 'Message not delivered' . PHP_EOL;
		}
		else
		{
			return 'Message successfully delivered' . PHP_EOL;
		}
		
		fclose($apns);		
	}*/
	
	
	

function hex_2_bin($hexdata) {
   $bindata="";
   for ($i=0;$i<strlen($hexdata);$i+=2) {
      $bindata.=chr(hexdec(substr($hexdata,$i,2)));
   }

   return $bindata;
}

//Send notification to user your driver reached
/*
$notify_userId 	= $res['user_id'];
$message_title	= "Driver reached";
$message		= "Your driver has reached at your location and waiting for you to board the cab.";
$message_array  = array(
						"message_title"=>$message_title,
						"message"=>$message,//$admin_info->notification,
						"pem_file" => "pushCert_JGRider",
						"tbl" =>"user"
						);

set_apps_notification($notify_userId,$message_array);*/
//Send notification to user your driver reached END	