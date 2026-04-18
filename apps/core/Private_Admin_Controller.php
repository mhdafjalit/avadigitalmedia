<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Private_Admin_Controller extends Private_Controller
{
	public function __construct()
	{
		ob_start();
		parent::__construct();
		if($this->mres['mem_nature']!=0){
			members_direction($this->mres['mem_nature']);
		}
		if(isset($this->checkTourAccess) && !$this->mres['has_tour_pkg_access']){
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Please change your type to access the tour section.");
			redirect_top(site_url('admin/edit_account') , '');
		}
	}
}