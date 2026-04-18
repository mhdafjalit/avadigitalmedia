<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'libraries/import/utility/release/Release_import.php');

require_once(APPPATH.'libraries/import/excel/release/Release_base_import_excel.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;

#[AllowDynamicProperties]

class Release_import_excel extends Release_base_import_excel {

	use Release_import;

	private $process_version;

	private $available_stable_versions = array();

	private $releases_custom_fields_res;

	public $debug_mode=FALSE;

	public function __construct($params=array()){

		if (!isset($this->ci)){

			$this->ci =& get_instance();

		}

		$this->ci->load->model('utils_model');

		$this->process_version = '1.0.0';

		$this->company_id = $params['company_id'] ?? 0;

		$this->debug_mode = $params['debug_mode'] ?? FALSE;

		$this->data_initial_row_start = 3;

		$this->data_row_start = $this->data_initial_row_start;

		$this->max_custom_fields = 5;

	}



	public function create_sample_for_import($params=array()){

		ini_set('memory_limit','-1');

		$filename=$params['filename'] ?? 'sample_release_'.date("Y-m-d H:i:s");

		$with_data=!empty($params['with_data']) ? $params['with_data'] : 0;

		$is_download=!empty($params['download']) ? $params['download'] : 0;

		$max_length_release_id = $this->ci->config->item('cx_max_length_release_id');

		//Styles

		$styleArray = array(

			'font' => array(

				'bold' => true

			),

		'alignment' => array(

			'horizontal' => Alignment::HORIZONTAL_RIGHT

		),

		'borders' => array(

			'top' => array(

				'borderStyle' => Border::BORDER_THIN

			)

		),

		'fill' => array(

				'fillType' => Fill::FILL_GRADIENT_LINEAR,

				'rotation' => 90,

				'startColor' => array(

					'argb' => '0000FFA0'

					),

				'endColor' => array(

					'argb' => 'FFFFFFFF'

				)

			)

		);





		$styleDescriptionArray = array(

		'font' => array(

			'bold' => true,

			'color' => array('rgb' => '0000FF')

		),

		'alignment' => array(

		'horizontal' => Alignment::HORIZONTAL_JUSTIFY,

		'vertical' => Alignment::VERTICAL_TOP,

		),

		'borders' => array(

			'top' => array(

				'borderStyle' => Border::BORDER_THIN,

			),

		),

		'fill' => array(

				'fillType' => Fill::FILL_SOLID,

				'color' => array('rgb' => 'FFFF00')

			)

		);



		$spreadsheet = new Spreadsheet();



		//Create Example Sheet

		$spreadsheet->setActiveSheetIndex(0);

		$spreadsheet->getSheet(0)->setTitle("releases");

		$example_sheet = $spreadsheet->getSheet(0);



		//Create Meta Sheet

		$import_column_start = 'A';

		$fld_array = $this->get_heading_list();

		$map_fld_array = array();

		$total_import_flds = count($fld_array);

		for($i=0;$i<$total_import_flds;$i++){

			$map_fld_array[$import_column_start] = $fld_array[$i];

			$example_sheet->SetCellValue($import_column_start."1", $fld_array[$i]);

			$example_sheet->getColumnDimension($import_column_start)->setWidth(35);

			$objValidation = $example_sheet->getCell($import_column_start."1")->getDataValidation();

			

			switch($fld_array[$i]){

				case 'release ID':

					$example_sheet->SetCellValue($import_column_start."2",  "Field is required.\r\nMust be Unique value.\r\nMaximum $max_length_release_id characters.");

				break;

				case 'release Name':

					$example_sheet->SetCellValue($import_column_start."2", "Maximum 40 characters");

				break;

				case 'Prospect':

					$example_sheet->SetCellValue($import_column_start."2",  "TRUE/FALSE.\r\nDefault:FALSE");

				break;

				case 'Inactive':

					$example_sheet->SetCellValue($import_column_start."2", "TRUE/FALSE.\r\nDefault:TRUE");

				break;

				case 'Contact Name':

					$example_sheet->SetCellValue($import_column_start."2", "Maximum 40 characters");

				break;

				case 'Account #':

					$example_sheet->SetCellValue($import_column_start."2", "Maximum 40 alphanumeric characters");

				break;

				case 'Use Standard Terms':

				case 'C.O.D. Terms':

				case 'Prepaid Terms':

				case 'Due Next Month':

				case 'Due End Month':

					$example_sheet->SetCellValue($import_column_start."2", "TRUE/FALSE.\r\nDefault:FALSE");

				break;

				case 'release Since Date':

					$example_sheet->SetCellValue($import_column_start."2", "mm/dd/yy");

				break;

			}

			if($i!=$total_import_flds-1){

				$import_column_start++;

			}

		}



		$example_sheet->getStyle('A1:'.$import_column_start.'1')->applyFromArray($styleArray);

		$example_sheet->getStyle('A2:'.$import_column_start.'2')->applyFromArray($styleDescriptionArray);



		/*$example_sheet->protectCells('A2:'.$import_column_start.'2', 'PHP');

		$example_sheet->getProtection()->setSheet(true);

		$total_data_rows_allowed = 10000;

		if($with_data){

				$example_sheet->getStyle('A'.$data_offset.':'.$import_column_start.($end_data_offset))->getProtection()->setLocked( Protection::PROTECTION_UNPROTECTED );

				$example_sheet->protectCells('A'.($end_data_offset).':'.$import_column_start.($end_data_offset+$total_data_rows_allowed), 'PHP');

		}else{

			$example_sheet->getStyle('A'.$this->data_row_start.':'.$import_column_start.$total_data_rows_allowed)->getProtection()->setLocked( Protection::PROTECTION_UNPROTECTED );

		}*/

		$spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($spreadsheet);

		header('Cache-Control: max-age=60, must-revalidate');

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');

		$writer->save('php://output');

		exit;

	}

	

	protected function validate_data(){

		$release_worksheet  = $this->sheet_refs[0];

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



		$highest_entry = $release_worksheet->getHighestRow();



		$total_release_entries = !empty($this->is_preview) ? 5 : (!isset($this->data_row_end) ? $highest_entry  : $this->data_row_end);



		if($total_release_entries>$highest_entry){

			$total_release_entries = $highest_entry;

		}



		if($this->debug_mode===TRUE){

			echo "<p>Sheet End Column</p>";

			trace($this->sheet_end_column);

		}



		$release_data_row = array();



		$ip_address = $this->ci->input->ip_address();



		$total_rec_inserted=0;



		$exists_rec_count=0;



		$this->sheet_end_column++;



		if(!isset($this->data_row_start)){

			$this->data_row_start = 3;

		}

		$error_rows = array();

		for($ox=$this->data_row_start;$ox<=$total_release_entries;$ox++){

			$loop_row = $release_worksheet->getRowIterator($ox)->current();

			$is_row_empty = self::isRowEmpty($loop_row,array());

			if($is_row_empty===TRUE){

				continue;

			}

			$release_data_index = array_push($release_data_row, array('row_index'=>$ox,'error'=>array(),'data'=>array()));

			$release_data_index = $release_data_index-1;

			$loop_error_obj = &$release_data_row[$release_data_index]['error'];

			$loop_data_obj = &$release_data_row[$release_data_index]['data'];

			$loop_has_data_error=FALSE;

			$this->loop_shipping_address_data = array();

			$this->loop_data_cs_info_setting = array();

			for($ix='A';$ix!=$this->sheet_end_column;$ix++){

				$column_heading_value = $release_worksheet->getCell($ix."1")->getValue();

				$column_heading_value = mb_trim($column_heading_value);

				$column_heading_value = mb_strtolower($column_heading_value);

				$row_column_data_value = $release_worksheet->getCell($ix.$ox)->getValue();

				$row_column_data_value = $row_column_data_value ?? '';

				$row_column_data_value = mb_trim($row_column_data_value);

				$row_column_data_value_length = mb_strlen($row_column_data_value ?? '');

				$this->readDataValue($column_heading_value,$row_column_data_value,$loop_data_obj,$loop_error_obj);

			}

			$this->rowIterationAfterCallback($loop_data_obj,$loop_error_obj);

			$loop_data_obj['ship_addresses'] = $this->loop_shipping_address_data;

			$loop_data_obj['cs_info_setting'] = $this->loop_data_cs_info_setting;

			$release_data_row[$release_data_index]['data'] = $loop_data_obj;

			if(!$this->apply_validation){

				$this->data_results = array($release_data_row[$release_data_index]);

				$this->upload_data();

			}

			if(!empty($loop_error_obj)){

				$error_rows['zx_'.$ox] = array('row'=>$ox,'error'=>$loop_error_obj);

			}

			//$member_data_row[$ox]['error'] = $loop_error_obj;

		}



		if($this->debug_mode===TRUE){

			if($debug_level==1 || $debug_level==-1)

			{

				echo "<p>Data after the member sheet traversed</p>";
				echo '<p>Manipulated Data Tree</p>';
				trace($release_data_row);
				echo "<p>Worksheet Validation Passed</p>";
				echo $this->has_data_error===FALSE ? 'Yes' : 'No';
				if($halt_execution===TRUE){
					exit;
				}
			}
		}
		if(!$this->has_data_error){
			$total_existing_records = count($release_data_row);
			$this->has_data_error = !$total_existing_records;

		}
		$ret_release_data_row = array(
							'has_data_error'=>$this->has_data_error,
							'data_row_result'=>$release_data_row,
							'error_rows'=>&$error_rows
						);
		$release_data_row = null;
		return $ret_release_data_row;
	}
}

/*End of file */