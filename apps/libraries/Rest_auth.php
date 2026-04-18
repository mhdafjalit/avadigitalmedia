<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Rest_auth {

		public $ci;
		public function __construct()
		{
			if (!isset($this->ci))
			{
				$this->ci =& get_instance();
			}
		}

    public function check_token($token){
			trace($this->ci->user);
die;
			/*trace($this->ci->input->method());
			trace($this->ci->router->method);
			echo $token;
			die;*/
			return TRUE;
		}
}

/*End of file */