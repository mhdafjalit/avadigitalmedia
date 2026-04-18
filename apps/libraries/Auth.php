<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
class Auth
{
	public $ci;
	public function __construct()
	{
		if (!isset($this->ci))
		{
			$this->ci =& get_instance();
		}
		$this->ci->load->library('safe_encrypt');
		$this->ci->load->helper('cookie');
		$this->auth_tbl = 'wl_customers';
	}
	
	public function is_user_logged_in()
	{
		$is_logged_in = $this->ci->session->userdata('logged_in');
		
		$logged_in_username = $this->ci->session->userdata('username');
		if ($is_logged_in == TRUE)
		{
			$return = false;
			$user_data = array(
			'user_name'=>$logged_in_username,			  
			'status'=>'1'	
			);						 
			$res_user = $this->ci->db->select('customers_id')->get_where($this->auth_tbl,$user_data)->row_array();
			if(!empty($res_user)){
				$return = true;
			}
			return $return;
		}else
		{
			return false;
		}
		
	}
	
	public function is_auth_user()
	{
		if ($this->is_user_logged_in()!= TRUE)
		{
			$this->logout();
			$is_rtype_header = $this->ci->input->get_request_header('XRSP',TRUE);
			if($is_rtype_header=='json'){
				$ret_arr = array('status'=>0,'error_flds'=>array(),'is_logout'=>1);
				$this->ci->output->set_status_header(401);
				echo json_encode($ret_arr);
				die;
			}else{
				redirect_top(site_url('login'), '');
			}
		}
	}
	
	public function update_last_login($login_data)
	{
		if(!$this->ci->session->userdata('is_admin_switch')){
			$data = array(
			'last_login_date'=>$login_data['current_login'],
			'current_login'=>$this->ci->config->item('config.date.time') 
			);
			$this->ci->db->where('customers_id', $this->ci->session->userdata('user_id'));
			$this->ci->db->update($this->auth_tbl, $data);
		}
	}
	
	public function verify_user($params=array())
	{
		$err=1;
		$err_type="error";
		$login_type = !empty($params['login_type']) ? $params['login_type'] : '';
		$username = $params['username'] ?? '';
		$password = $params['password'] ?? '';
		$member_type = $params['member_type'] ?? 0;
		$password = $this->ci->safe_encrypt->encode($password);
		$this->ci->db->select("customers_id,user_name,first_name,last_name,title,sponsor_id,login_type,is_blocked,last_login_date,current_login,block_time,mobile_number,is_verified,is_verified_mobile,member_type",FALSE);
    	$this->ci->db->group_start();
        $this->ci->db->where('user_name', $username);
        $this->ci->db->or_where('sponsor_id', $username);
        $this->ci->db->group_end();
		$this->ci->db->where('password', $password);
		//$this->ci->db->where('member_type', $member_type);
		$this->ci->db->where('status','1');			
		$this->ci->db->where('is_verified','1');		
		$query = $this->ci->db->get($this->auth_tbl);
		if ($query->num_rows() == 1)
		{
			$row  = $query->row_array();
			$skip_verified_check = 1;
			if($row['is_verified']==1 || $skip_verified_check){
				$name = $row['first_name']." ".$row['last_name'];		
				$data = array(
				'user_id'=>$row['customers_id'],
				'name'=>ucwords($name),
				'login_type'=>$row['login_type'],
				'username'=>$row['user_name'],							
				'title'=>$row['title'],
				'first_name'=>$row['first_name'],
				'last_name'=>$row['last_name'],
				'mobile_number'=>$row['mobile_number'],
				'is_blocked'=>$row['is_blocked'],	
				'blocked_time'=>$row['block_time'],
				'is_verified'=>$row['is_verified'],
				'is_mobile_verified'=>$row['is_verified_mobile'],
				'last_login_date'=>$row['last_login_date'],
				'member_type'=>$row['member_type'],
				'logged_in' => TRUE
				);
				
				$login_data = array('current_login'=>$row['current_login']);			
				$this->ci->session->set_userdata($data);
				$this->ci->session->set_userdata('attempted_login_section','Y');
				$this->update_last_login($login_data); 
				$err=0;
				$err_type="";
				$msg = "Login successful";
			}
		}
		else
		{
			$msg = lang('invalid_user_password');
		}
		$ret_data = array('err'=>$err,'err_type'=>$err_type,'msg'=>$msg);
		if(!$err){
			$ret_data['user_data'] = $data;
		}
		if(!empty($redirect_url)){
			$ret_data['redirect_url'] = $redirect_url;
		}
		return $ret_data;
	}
	
	public function logout()
	{
		$userId = $this->ci->session->userdata('user_id');
		$this->ci->session->unset_userdata('session_id');
		
		$data = array('user_id', 'type', 'login_type', 'username', 'first_name', 'last_name', 'name', 'mkey', 'is_blocked', 'blocked_time', 'logged_in','is_verified','mem_nature');
		$this->ci->session->unset_userdata($data);
	}
	
	public function logged_member_type()
	{
		return $this->ci->session->userdata("member_type")!=3 ? "admin" : "members";
	}
	
}