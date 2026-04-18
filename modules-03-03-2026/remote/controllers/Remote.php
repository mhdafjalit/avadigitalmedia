<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Remote extends MY_Controller{

	public function __construct()
	{
		parent::__construct();

		$this->common_status_arr  = array(0=>'Inactive',1=>'Active',2=>'Deleted');
	}

	public function load_states()
	{
		$country_id = (int)$this->input->post('country_id');

		$selected_id = (int)$this->input->post('current_selected');

		$status = $this->input->post('status');

		$params_where = array('country_id'=>$country_id,'status !='=>'2');

		if($status!=''){
			$params_where['status']=$status;
		}

		$res_array = $this->db->select('id,title,status')->order_by('title')->get_where('wl_states',$params_where)->result_array();

		$data['res'] =  $res_array;
		$data['selected_id']    = $selected_id;
		$data['option_val_field'] = 'id';
		$data['option_text_field'] = 'title';
		$data['option_status_field'] = 'status';
		//$data['show_status'] = 1;
		//$data['status_active_value'] = 1;
		$data['status_arr'] = $this->common_status_arr;
		$this->load->view('remote/load_attributes',$data);
	}

	public function load_cities()
	{
		$state_id = (int)$this->input->post('state_id');

		$selected_id = (int)$this->input->post('current_selected');

		$status = $this->input->post('status');

		$params_where = array('state_id'=>$state_id,'status !='=>'2');

		if($status!=''){
			$params_where['status']=$status;
		}

		$res_array = $this->db->select('id,title,status')->order_by('title')->get_where('wl_cities',$params_where)->result_array();

		$data['res'] =  $res_array;
		$data['selected_id']    = $selected_id;
		$data['option_val_field'] = 'id';
		$data['option_text_field'] = 'title';
		$data['option_status_field'] = 'status';
		//$data['show_status'] = 1;
		//$data['status_active_value'] = 1;
		$data['status_arr'] = $this->common_status_arr;
		$this->load->view('remote/load_attributes',$data);
	}

	public function load_monthly_releases_graph(){
		if($this->input->post('action')=='Y'){
			$this->load->model(array('admin/admin_model'));
			$member_id 	   	= $this->input->post('member_id',TRUE);
			$release_month 	= $this->input->post('month',TRUE);
			$releases_data 	= ['member_id'=>$member_id,'month'=>$release_month];
			$monthly_releases = $this->admin_model->get_monthly_releases_data($releases_data);
			echo json_encode($monthly_releases);
			die;
		}
	}

	public function load_company_senders()
	{
		$use_group_parent_id = !empty($this->mres['use_group_parent_id']) ? $this->mres['use_group_parent_id'] : -1;

		$company_id = (int)$this->input->post('company_id');

		$selected_id = (int)$this->input->post('current_selected');

		$status = $this->input->post('status');

		$user_role=2;

		$params_where = array('b.company_id'=>$company_id,'user_role'=>$user_role,'group_parent_id'=>$use_group_parent_id,'status !='=>'2');

		if($status!='-1'){
			if($status!=''){
				$params_where['status']=$status;
			}else{
				$params_where['status']='1';
			}
		}

		$res_array = $this->db->select('customers_id,first_name,employee_id')->join('wl_user_companies as b','a.customers_id=b.user_id')->order_by('first_name')->get_where('wl_customers as a',$params_where)->result_array();
		//echo $this->db->last_query();
		if(!empty($res_array)){
			foreach($res_array as $key=>$val){
					$loop_custom_text = $val['first_name']." - ".$val['employee_id'];
					$res_array[$key]['custom_text'] = $loop_custom_text;
			}
			usort($res_array,function($a,$b){
				return strcmp($a["custom_text"], $b["custom_text"]);	
			});
		}
		$data['res'] =  $res_array;
		$data['selected_id']    = $selected_id;
		$data['option_val_field'] = 'customers_id';
		$data['option_text_field'] = 'custom_text';
		$data['option_status_field'] = 'status';
		//$data['show_status'] = 1;
		//$data['status_active_value'] = 1;
		$data['status_arr'] = $this->common_status_arr;
		$this->load->view('remote/load_attributes',$data);
	}
}
