<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(! function_exists('log_import_activity')){
	function log_import_activity($company_id,$mode='xls'){
		$CI = CI();
		if(!empty($CI->log_entry_db_import)){
			$batch_import_data = array();
			foreach($CI->log_entry_db_import as $val){
				$batch_import_data[] = array('type'=>$val['type'],'rec_id'=>$val['rec_id'],'company_id'=>$company_id,'mode'=>$mode,'added'=>xc_get_cur_date_time('config.date.time'));
			}
			$CI->db->insert_batch('wl_log_entry_db_import', $batch_import_data);
		}
	}
}
if(! function_exists('update_import_processed')){
	function update_import_processed($import_id,$stats=array()){
		$CI = CI();
		if(!empty($import_id) && $import_id>0){
			$processed_entries = $stats['processed_entries'] ?? 0;
			$total_entries = $stats['total_entries'] ?? 0;
			if($processed_entries>=$total_entries){
				$processed_entries = $total_entries;
				$import_status=2;
			}else{
				$import_status=1;
			}
			$CI->utils_model->safe_update('wl_imports',array('status'=>$import_status,'processed_entries'=>$processed_entries,'updated_date'=>xc_get_cur_date_time('config.date.time')),array('id'=>$import_id,'processed_entries<'=>$processed_entries),FALSE);
		}
	}
}