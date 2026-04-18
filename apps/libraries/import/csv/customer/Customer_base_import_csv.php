<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'libraries/import/csv/Csv_base_import.php');
class Customer_base_import_csv extends Csv_base_import {
	
	public function __construct(){
		if (!isset($this->ci)){
			$this->ci =& get_instance();
		}
	}

}
/*End of file */