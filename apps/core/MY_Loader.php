<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

class MY_Loader extends MX_Loader {

	function __construct() {
		parent::__construct();
	}

	/**
	*Useful for the cases like xhr load partials
	*/
	 public function partial_view($template_name, $vars = array(), $return = FALSE){
		 if($return):
			$content = $this->load->view($template_name, $vars, $return);
			 return $content;
		else:
			$this->load->view($template_name, $vars);
		endif;
	 }

}