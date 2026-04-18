<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'libraries/import/utility/customer/Customer_import.php');
require_once(APPPATH.'libraries/import/csv/customer/Customer_base_import_csv.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
#[AllowDynamicProperties]
class Customer_import_csv extends Customer_base_import_csv {
	use Customer_import;
	private $process_version;
	private $available_stable_versions = array();
	private $customers_custom_fields_res;
	public function __construct($params=array()){
		if (!isset($this->ci)){
			$this->ci =& get_instance();
		}
		$this->ci->load->model('utils_model');
		$this->process_version = '1.0.0';
		$this->company_id = $params['company_id'] ?? 0;
		$this->debug_mode = $params['debug_mode'] ?? FALSE;
		$this->data_initial_row_start = 2;
		$this->data_row_start = $this->data_initial_row_start;
		$this->max_custom_fields = 5;
	}

	public function create_sample_for_import($params=array()){
		ini_set('memory_limit','-1');
		$filename=$params['filename'] ?? 'sample_customer_'.date("Y-m-d H:i:s");
		$with_data=!empty($params['with_data']) ? $params['with_data'] : 0;
		$is_download=!empty($params['download']) ? $params['download'] : 0;
		$this->tpl_unique_type=!empty($params['tpl_unique_type']) ? $params['tpl_unique_type'] : 0;
		$max_length_customer_id = $this->ci->config->item('cx_max_length_customer_id');

		$spreadsheet = new Spreadsheet();

		//Create Example Sheet
		$spreadsheet->setActiveSheetIndex(0);
		$spreadsheet->getSheet(0)->setTitle("Customers");
		$example_sheet = $spreadsheet->getSheet(0);

		//Create Meta Sheet
		$import_column_start = 'A';
		$fld_array = $this->get_heading_list();
		$map_fld_array = array();
		$total_import_flds = count($fld_array);
		for($i=0;$i<$total_import_flds;$i++){
			$map_fld_array[$import_column_start] = $fld_array[$i];
			$example_sheet->SetCellValue($import_column_start."1", $fld_array[$i]);
			if($i!=$total_import_flds-1){
				$import_column_start++;
			}
		}
		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Csv($spreadsheet);
		// Set CSV options
		$writer->setDelimiter(',');
		$writer->setEnclosure('"');
		$writer->setLineEnding("\r\n");
		$writer->setSheetIndex(0);
		header('Cache-Control: max-age=60, must-revalidate');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.csv"');
		$writer->save('php://output');
	}
	
	protected function validate_data(){
		$customer_worksheet  = $this->sheet_refs[0];
		//Debug Control
		/********Gives more fine tuning with $this->debug_mode=TRUE
		@@Used to print all data grabbed at specific sheet level
		@@-1 will print all the data after all the sheet rows executed
		@@See the level values used in this method below
		*************/
		$debug_level = -1;
		//Stops execution after debug
		$halt_execution = FALSE;

		$this->has_data_error = FALSE;

		$highest_entry = $customer_worksheet->getHighestRow();

		$total_customer_entries = !empty($this->is_preview) ? 5 : (!isset($this->data_row_end) ? $highest_entry  : $this->data_row_end);

		if($total_customer_entries>$highest_entry){
			$total_customer_entries = $highest_entry;
		}

		if($this->debug_mode===TRUE){
			echo "<p>Sheet End Column</p>";
			trace($this->sheet_end_column);
		}

		$customer_data_row = array();

		$ip_address = $this->ci->input->ip_address();

		$total_rec_inserted=0;

		$exists_rec_count=0;

		if(!isset($this->data_row_start)){
			$this->data_row_start = 2;
		}
		$error_rows = array();
		$firstRow = $customer_worksheet->getRowIterator()->current();
		$headings = [];
		foreach ($firstRow->getCellIterator() as $key=>$cell) {
			 // Get the cell value
			 $column_heading_value = $cell->getValue();
			 $column_heading_value = mb_trim($column_heading_value);
			 $column_heading_value = mb_strtolower($column_heading_value);
			 $headings[$key] = $column_heading_value;
		}
		for($ox=$this->data_row_start;$ox<=$total_customer_entries;$ox++){
			$loop_row = $customer_worksheet->getRowIterator()->seek($ox)->current();
			$is_row_empty = self::isRowEmpty($loop_row,array());
			if($is_row_empty===TRUE){
				continue;
			}
			$customer_data_index = array_push($customer_data_row, array('row_index'=>$ox,'error'=>array(),'data'=>array()));
			$customer_data_index = $customer_data_index-1;
			$loop_error_obj = &$customer_data_row[$customer_data_index]['error'];
			$loop_data_obj = &$customer_data_row[$customer_data_index]['data'];
			$loop_has_data_error=FALSE;
			$this->loop_shipping_address_data = array();
			$this->loop_data_cs_info_setting = array();
			foreach ($loop_row->getCellIterator() as $key=>$cell) {
				$column_heading_value = $headings[$key];
				$row_column_data_value = $cell->getValue();
				$row_column_data_value = $row_column_data_value ?? '';
				$row_column_data_value = mb_trim($row_column_data_value);
				$row_column_data_value_length = mb_strlen($row_column_data_value ?? '');
				$this->readDataValue($column_heading_value,$row_column_data_value,$loop_data_obj,$loop_error_obj);
			}
			$this->rowIterationAfterCallback($loop_data_obj,$loop_error_obj);
			$loop_data_obj['ship_addresses'] = $this->loop_shipping_address_data;
			$loop_data_obj['cs_info_setting'] = $this->loop_data_cs_info_setting;
			$customer_data_row[$customer_data_index]['data'] = $loop_data_obj;
			if(!$this->apply_validation){
				$this->data_results = array($customer_data_row[$customer_data_index]);
				$this->upload_data();
			}
			if(!empty($loop_error_obj)){
				$error_rows['zx_'.$ox] = array('row'=>$ox,'error'=>$loop_error_obj);
			}
			//$customer_data_row[$ox]['error'] = $loop_error_obj;
		}

		if($this->debug_mode===TRUE){
			if($debug_level==1 || $debug_level==-1)
			{
				echo "<p>Data after the member sheet traversed</p>";
				echo '<p>Manipulated Data Tree</p>';
				trace($customer_data_row);
				echo "<p>Worksheet Validation Passed</p>";
				echo $this->has_data_error===FALSE ? 'Yes' : 'No';
				if($halt_execution===TRUE){
					exit;
				}
			}
		}
		if(!$this->has_data_error){
			$total_existing_records = count($customer_data_row);
			$this->has_data_error = !$total_existing_records;
		}
		$ret_customer_data_row = array(
															'has_data_error'=>$this->has_data_error,
															'data_row_result'=>$customer_data_row,
															'error_rows'=>&$error_rows
														);
		$customer_data_row = null;
		return $ret_customer_data_row;
	}
	
}
/*End of file */