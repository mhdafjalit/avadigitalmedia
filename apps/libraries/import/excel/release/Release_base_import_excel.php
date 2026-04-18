<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/import/excel/Excel_base_import.php');

class Release_base_import_excel extends Excel_base_import {



	public function __construct(){

		if (!isset($this->ci)){

			$this->ci =& get_instance();

		}

	}



}

/*End of file */