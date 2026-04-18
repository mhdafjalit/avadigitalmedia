<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Subadmin_hooks {

	private $ci;
	public $trackAdmin;	
	
	public function __construct() {
		$this->ci =& get_instance();
	}
	
	//--------------------------------------------------------------------
	
	
	/**
	  check privilege to login user
	 */
	public function check_privileges(){
		if (!class_exists('CI_Session')){
			$this->ci->load->library('session');
		}
		$this->ci->load->model('sitepanel/subadmin_model');

		//trace($this->ci->input->post());exit;

		/* Works only if admin login && working environment is sitepanel */
		//$path_admin = FCPATH.'modules/'.$module.'/
		
		if(( $this->ci->session->userdata('admin_user')!='' ) && in_array('sitepanel',$this->ci->uri->segments)){
			$admin_type	=	$this->ci->session->userdata('admin_type');
			$admin_id		=	$this->ci->session->userdata('admin_id');
			$admin_name		=	$this->ci->session->userdata('admin_name');
			if($admin_type==2){
				$controller = $this->ci->router->fetch_class();
				$method = $this->ci->router->fetch_method();
				$priv_url = $controller;
				$spl_case = null;
				$skip = FALSE;
				//Will execute message only(no redirection) as it is open in popup
				$is_pop_flag = FALSE;
				switch($controller){
					case 'dashboard':
						$skip = TRUE;
					break;
					case 'sitepanel':
						switch($method){
							case 'logout':
								$skip = TRUE;
							break;
						}
					break;
					case 'subjects':
						switch($method){							case 'view_faculty_list':							case 'add_faculty':							case 'edit_faculty':								$priv_url .= "/view_faculty_list";							break;							case 'view_folder_list':							case 'add_folder':							case 'edit_folder':							break;							case 'add':							case 'edit':							break;
							default:
								if($method!='index'){
									$priv_url .= "/".$method;
								}
						}
					break;					case 'configurations':						switch($method){							default:								if($method!='index'){									$priv_url .= "/".$method;								}						}					break;					case 'manage_posts':						switch($method){							case 'comments':								$priv_url .= "/comments";							break;							case 'view_details':								$is_pop_flag = TRUE;							break;							case 'add':							case 'edit':							break;							default:								if($method!='index'){									$priv_url .= "/".$method;								}						}					break;					case 'question_bank':						switch($method){							case 'view_details':								$is_pop_flag = TRUE;							break;							case 'add':							case 'edit':							break;							default:								if($method!='index'){									$priv_url .= "/".$method;								}						}					break;					case 'video_bank':						switch($method){							case 'reviews':								$priv_url .= "/reviews";							break;							case 'add':							case 'edit':							break;							default:								if($method!='index'){									$priv_url .= "/".$method;								}						}					break;					case 'live_classes':						switch($method){							case 'view_chat_list':								$is_pop_flag = TRUE;							break;							default:								if($method!='index'){									$priv_url .= "/".$method;								}						}					break;
					case 'remote':
							$skip = TRUE;
					break;
				}
				if($skip===FALSE){
					//echo $priv_url;
					$qry_section = "SELECT a.id FROM tbl_admin_allowed_sections as a JOIN tbl_admin_sections as b ON a.sec_id=b.id WHERE a.subadmin_id='".$admin_id."' AND b.section_controller='".$priv_url."'";
					if($spl_case!=''){
						switch($spl_case){
							case 'aaa':
								$qry_section = "SELECT allowed_section_id FROM tbl_admin_allowed_sections as a JOIN tbl_admin_sections as b ON a.sec_id=b.id WHERE a.subadmin_id='".$admin_id."' AND (b.section_controller='map_varients' OR b.section_controller='map_varient_listing')";
							break;
						}
					}
					$res_section = $this->ci->db->query($qry_section)->row_array();
					if(!is_array($res_section) || empty($res_section)){
						if($is_pop_flag===TRUE){
							echo "You do not have access to the page requested. Please contact to admin";
							exit;
						}else{
							$this->ci->session->set_userdata(array('msg_type'=>'error'));
							$this->ci->session->set_flashdata('error',"You do not have access to the page requested. Please contact to admin" );
							redirect('sitepanel/dashboard', '');
						}
					}
				}
			}
		}
	}
}

// End Subadmin_hooks class