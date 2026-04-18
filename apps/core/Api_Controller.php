<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api_Controller extends CI_Controller{

	public $userId;
	public $name; 
	
	 public function __construct()
	 {		 
		 parent::__construct();	
		 $this->load->helper(array('android'));
	    
		 $keys       =  $this->input->post('wl_keys');
		 $device_id  =  $this->input->post("device_id");	 	
		 	 
		 $this->db->select("id");	
		 $is_valid =  $this->db->get_where('api_access',array('device_id'=>$device_id,'accesskey'=>$keys))->row_array();
		 
		 
		 if(!is_array($is_valid))
		 {
			$data["success"] = "false";								
			$data["error"]   = "Invalid Secret Key/Unauthorized Access";
			$jason = my_json_encode($data);  
		    print($jason); 
			exit; 
		 } 		
		
	 }	
} 