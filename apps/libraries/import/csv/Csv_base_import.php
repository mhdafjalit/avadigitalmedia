<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Reader\Csv;
class Csv_base_import {
	public $heading_comparision_tree=array();
	public $apply_validation=TRUE;
	public $data_results = array();
	protected $sheet_refs = [];

	public function __construct(){
		if (!isset($this->ci)){
			$this->ci =& get_instance();
		}
	}

	private function get_last_filled_column($sheet){
		$end_column = $sheet->getHighestDataColumn();
		return $end_column;
	}

	protected function validate_column_headings($ref_sheet,$end_column){
		if($end_column==''){
			return FALSE;
		}
		$actual_start_column='A';
		$actual_end_column='A';
		$actual_headings = $this->get_heading_list();
		$total_actual_headings = count($actual_headings);
		for($ix=1;$ix<$total_actual_headings;$ix++){
			$actual_end_column++;
		}
		if($actual_end_column!=$end_column){
			return FALSE;
		}
		$zx=0;
		for($ix=$actual_start_column;$ix<=$actual_end_column;$ix++){
			$meta_sheet_value = !empty($actual_headings[$zx]) ? $actual_headings[$zx] : "";
			$meta_sheet_value = trim($meta_sheet_value);
			$ref_sheet_value = $ref_sheet->getCell($ix.'1')->getValue();
			$ref_sheet_value = trim($ref_sheet_value);
			if($meta_sheet_value!=$ref_sheet_value){
				return FALSE;
			}
			$zx++;
		}
		return TRUE;
	}

	protected function prepareHeadingComparisonTree(){
		$actual_headings = $this->get_heading_list();
		$posted_headings = array();
		$worksheet  = $this->sheet_refs[0];
		$highestColumn = $this->get_last_filled_column($worksheet);
		for($col ='A';$col!=$highestColumn;++$col){
			$column_heading_value = $worksheet->getCell($col."1")->getValue();
			$posted_headings[] = $column_heading_value;
		}
		$this->heading_comparision_tree =  array('actual_headings'=>$actual_headings,'posted_headings'=>$posted_headings);
	}

	protected function validate_meta_file(){
		//Parse Headings of Product Sheet
		$ref_sheet_obj  = $this->sheet_refs[0];
		$err_msg = array();
	
		//Validate Column
		$this->sheet_end_column = '';
		$skip_validation = FALSE;
			
		if($skip_validation===FALSE){
			//Get Meta Last Filled Column
			$meta_end_column = $this->get_last_filled_column($ref_sheet_obj);
			$this->sheet_end_column = $meta_end_column;
			$res_validate_column = $this->validate_column_headings($ref_sheet_obj,$meta_end_column);
			if($res_validate_column===FALSE){
				$this->prepareHeadingComparisonTree();
				array_push($err_msg,"<div>Sheet columns are not in right order.</div>");
			}
		}
		if(!empty($err_msg)){
			$err_msg = implode('',$err_msg);
			return array('error'=>TRUE,'msg'=>$err_msg);
		}
		return array('error'=>FALSE,'msg'=>'');
	}

	public function initialize_import(){
		//Debug Controls
		$debug = $this->debug_mode ?? FALSE;
		$res_meta_parsing = $this->validate_meta_file();
		if($debug === TRUE){
			echo '<p>Imported File Meta Parse Result</p>';
			trace($res_meta_parsing);
		}
		if($res_meta_parsing['error']===FALSE){
			$res_data_processing = $this->validate_data();
			$has_data_error = $res_data_processing['has_data_error'];
			if(!$has_data_error){
				$this->data_results = $res_data_processing['data_row_result'];
				$import_res = array(
												'error'=>FALSE,
												'msg'=>'',
												'res_data'=>$res_data_processing['data_row_result']
											);
			}else{
				$total_existing_records = count($res_data_processing['data_row_result']);
				$msg = $total_existing_records==0 ? 'Sheet is empty' : 'Error';
				$import_res = array(
												'error'=>TRUE,
												'error_level'=>'data',
												'msg'=>$msg,
												'error_rows'=>$res_data_processing['error_rows'],
												'is_sheet_empty'=>$total_existing_records==0 ? 1 : 0
											);
			}
			return $import_res;
		}else{
			$import_res = array(
			'error'=>TRUE,
			'error_level'=>'meta',
			'msg'=>$res_meta_parsing['msg'],
			'heading_comparision_tree'=>&$this->heading_comparision_tree
			);
		}
		if($debug === TRUE){
			echo '<p>Imported Final Result</p>';
			trace($import_res);
		}
		return $import_res;
	}

	public static function isRowEmpty($row,$skip_cells=array()){
		foreach ($row->getCellIterator() as $key=>$cell) {
			$cell_key = $cell->getColumn();
			$cell_key = preg_replace("~(\d+)$~","",$cell_key);
			if(!in_array($cell_key,$skip_cells)){
				if ($cell->getValue()) {
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	public function process_import_file($params=array()){
		$inputFileName = $params['src'];
		try {
				ini_set('memory_limit','-1');
				$reader = new Csv();
				$this->spreadsheet_obj = $reader->load($inputFileName);
				$sheet = $this->spreadsheet_obj->getActiveSheet();
				$this->sheet_refs[0] = &$sheet;
				$result_import = $this->initialize_import();
		} catch(Exception $e) {
				$err_msg = 'Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage();
				$result_import = array(
												'error'=>TRUE,
												'error_level'=>'load_file',
												'msg'=>$err_msg
											);
				//die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		return $result_import;
	}

	/*
	* This should be used if you have uploaded the file but failed to populate all sheet entries in db
	*/
	public function retry_entries($params=array()){
		$ret_data = array('err'=>1);
		$import_id = !empty($params['import_id']) ? (int) $params['import_id'] : 0;
		$total_entries = !empty($params['total_entries']) ? (int) $params['total_entries'] : 0;
		$processed_entries = !empty($params['processed_entries']) ? (int) $params['processed_entries'] : 0;
		$read_row_limit = !empty($params['read_row_limit']) && $params['read_row_limit']!=-1 ? (int) $params['read_row_limit'] : 0;
		$sheet_full_path = !empty($params['src']) ? $params['src'] : '';
		if($total_entries>0 && $sheet_full_path!='' && file_exists($sheet_full_path)){
			try{
				$this->is_preview = 0;
				$this->import_id=$import_id;
				$this->total_entries=$total_entries;
				$this->data_row_start = $processed_entries+$this->data_row_start;
				if($read_row_limit>0){
					$this->data_row_end = $read_row_limit;
				}
				$params_process_import = array(
																			'src'=>$sheet_full_path
																		);
				$res_import = $this->process_import_file($params_process_import);
				if($res_import['error']===FALSE){
					$res_upload_data = $this->upload_data();
					$ret_data = array('err'=>0);
				}
			}catch(Exception $e){

			}
		}
		return $ret_data;
	}

	public function formatSavePath($path){
		$formatted_path = str_replace(__FILE__,$path,__FILE__);
		return $formatted_path;
	}
	
}
/*End of file */