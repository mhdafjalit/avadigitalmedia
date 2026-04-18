<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model
{

	/**
	* Get account by id
	*
	* @access public
	* @param string $account_id
	* @return object account object
	*/
	public function create_user($status='0', $is_verified='0')
	{
		$password = $this->safe_encrypt->encode($this->input->post('password',TRUE));
		
		$register_array = array
		(
		'user_name'        => $this->input->post('user_name',TRUE),
		'password'         => $password,
		'first_name'       => $this->input->post('first_name',TRUE),
		'last_name'       => $this->input->post('last_name',TRUE),
		'mobile_number'       => $this->input->post('mobilenumber',TRUE),
		'actkey'           =>md5($this->input->post('user_name',TRUE)),
		'account_created_date'=>$this->config->item('config.date.time'),
		'current_login'    =>$this->config->item('config.date.time'),
		'status'=>$status,
		'is_verified'=>$is_verified,
		'ip_address'  =>$this->input->ip_address()
		);
		
		$register_array = $this->security->xss_clean($register_array);
		$this->db->select("customers_id")->from("wl_customers")->where("user_name",$this->input->post('user_name',TRUE))->where("status","3");
		
		$mqry=$this->db->get();
		
		$updId ="";
		
		if($mqry->num_rows()>0){
			$mr=$mqry->row();
			$insId = $mr->customers_id; 			
			$updId =$insId;
			$this->safe_update('wl_customers',$register_array,"customers_id = '".$insId."'",FALSE);
			
		}else{
			return $insId =  $this->safe_insert('wl_customers',$register_array,FALSE);
	 }
	}
	
	
	public function create_user_mobile($status='0', $is_verified='0')
	{
		$password = $this->safe_encrypt->encode('User@123');
		
		$register_array = array
		(
		'mobile_number'    => $this->input->post('mobilenumber',TRUE),
		'password'         => $password,
		'first_name'       => 'Customer',
		'actkey'           => md5($this->input->post('mobilenumber',TRUE)),
		'account_created_date'=>$this->config->item('config.date.time'),
		'current_login'    =>$this->config->item('config.date.time'),
		'status'=>$status,
		'is_verified'=>$is_verified,
		'ip_address'  =>$this->input->ip_address()
		);
		
		$register_array = $this->security->xss_clean($register_array);
		
		return $insId =  $this->safe_insert('wl_customers',$register_array,FALSE);
	 
	}
	
	public function is_email_exits($data)
	{
		$this->db->select('customers_id');
		$this->db->from('wl_customers');
		$this->db->where($data);
		$this->db->where('status !=', '2')->where('status !=', '3');

		$query = $this->db->get();
		if ($query->num_rows() == 1)
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	public function is_subscribed($data)
	{
		$this->db->select('subscriber_id');
		$this->db->from('wl_newsletters');
		$this->db->where($data);
		$this->db->where('status !=', '2');

		$query = $this->db->get();
		if ($query->num_rows() == 1)
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	public function logout()
	{
		$data = array('user_id', 'email', 'name', 'user_photo', 'logged_in' => FALSE);
		$this->session->sess_destroy();
		$this->session->unset_userdata($data);
	}
	
	public function activate_account($cid)
	{		
		$is_verified=get_db_field_value('wl_customers','is_verified', array('md5(customers_id)'=>$cid));
		
		if($is_verified==0){		
	   	 	$this->db->query("update wl_customers set is_verified='1' where md5(customers_id)='".$cid."'");	
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Your account has been activated successfully.");
		}else{
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Your account has been already activated.");
		}
		redirect('user/login');		
	}

}
/* End of file users_model.php */
/* Location: ./application/modules/users/models/users_model.php */