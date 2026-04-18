<?php
class Captcha extends CI_Controller
{
	// Constructor
  public function __construct() {
		
	  parent::__construct();   

		// Load needed models, libraries, helpers and language files
		$this->load->library('securimage_library');
	}

	// Public methods
	public function normal()
	{
		$segment_namespace = $this->uri->segment(3);
		$segment_namespace = preg_replace("~\.htm$~","",$segment_namespace);
		$params = array(
						  'namespace' => (trim($segment_namespace)=='' ? 'default' : $segment_namespace)
						);
		$this->securimage_library->initialize($params);		
		$this->securimage_library->show();
	}
	
	public function mobile()
	{
		$this->securimage_library->initialize(TRUE);
		$this->securimage_library->show();
	}
	
	
}
	
/* End of file captcha.php */
/* Location: ./application/modules/blog/controllers/captcha.php */