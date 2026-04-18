<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\IOFactory;
class Excel_base_import {
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
		$end_column = $sheet->getHighestColumn();
		//Traverse Headings
		/*$end_column = '';
		$column_has_value = TRUE;
		while($column_has_value===TRUE){
			//Initialize Traversing
			if($end_column==''){
				$column_value = trim($sheet->getCell('A'.$row_index)->getValue() ?? '');
			}
			if($column_value!=''){
				$next_column = $end_column = $end_column=='' ? 'A' : $end_column;
				$next_column++;
				$next_column_value = $sheet->getCell($next_column.$row_index)->getValue();
				$next_column_value = trim($next_column_value ?? '');
				if($next_column_value==''){
					$column_has_value = FALSE;
				}
				else{
					$end_column++;
				}
			}
			else{
				$column_has_value = FALSE;
			}
		}*/
		return $end_column;
	}

	protected function validate_column_headings($ref_sheet,$end_column){
		if($end_column==''){
			return FALSE;
		}
		$start_column='A';
		$actual_headings = $this->get_heading_list();
		$total_actual_headings = count($actual_headings);
		$posted_sheet_total_headings = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($end_column);
		//trace($total_actual_headings."******".$posted_sheet_total_headings);
		if($total_actual_headings!=$posted_sheet_total_headings){
			//return FALSE;
		}
		for($ix=0;$ix<$total_actual_headings;$ix++){
			$meta_sheet_value = !empty($actual_headings[$ix]) ? $actual_headings[$ix] : "";
			$meta_sheet_value = mb_trim($meta_sheet_value);
			$ref_sheet_value = $ref_sheet->getCell($start_column.'1')->getFormattedValue();
			$ref_sheet_value = mb_trim($ref_sheet_value);
			if($meta_sheet_value!=$ref_sheet_value){
				return FALSE;
			}
			$start_column++;
		}
		return TRUE;
	}

	protected function prepareHeadingComparisonTree(){
		$actual_headings = $this->get_heading_list();
		$posted_headings = array();
		$worksheet  = $this->sheet_refs[0];
		$highestColumn = $this->get_last_filled_column($worksheet);
		//$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
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
			$has_data_error = $this->apply_validation && $res_data_processing['has_data_error'];
			if(!$has_data_error){
				$this->data_results = $res_data_processing['data_row_result'];
				$import_res = array(
												'error'=>FALSE,
												'msg'=>'',
												'res_data'=>$res_data_processing['data_row_result']
											);
			}else{
				$total_existing_records = count($res_data_processing['data_row_result']);
				$msg = $total_existing_records==0 ? 'Sheet is empty' : 'Found error in the below row(s) ';
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
				$this->spreadsheet_obj = IOFactory::load($inputFileName);
				$sheetCount = $this->spreadsheet_obj->getSheetCount();
			  for ($i = 0; $i < $sheetCount; $i++) {
				  $sheet = $this->spreadsheet_obj->getSheet($i);
					$this->sheet_refs[$i] = &$sheet;
			  }
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
					/*$res_upload_data = $this->upload_data();*/
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