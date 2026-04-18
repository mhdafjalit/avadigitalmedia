<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_Controller extends MY_Controller
{	
	public $admin_id;
	public $admin_type;
	public $admin_name;
	public function __construct()
	{
		 parent::__construct();			
		 $this->load->library(array('sitepanel/jquery_pagination'));		
		 $this->load->model(array('utils_model'));	
		 $this->admin_lib->is_admin_logged_in();
		 
		 $this->show_active_rec_only	= FALSE;
		 $this->is_admin_section = 1;
		 
		 $this->admin_type	=	$this->session->userdata('admin_type');
		 $this->admin_id		=	$this->session->userdata('admin_id');
		 $this->admin_name		=	$this->session->userdata('admin_name');
		 
		 /*if($this->admin_type==2)
		 {
			 $seg2=$this->uri->segment('2');
			 $seg3=$this->uri->segment('3');
			 $final_url=$seg2;
			
			 
			 if($seg2!='' && $seg2!="logout" && $seg2!='dashboard')
			 {
							 
				 if($seg2=="location" && ($seg3=="state" OR $seg3=="city"))
				 {
						 $final_url=$seg2."/".$seg3;
				 }							
				 elseif($seg2=="setting" && ($seg3=="update_setting"))
				 {
						 $final_url=$seg2."/".$seg3;
				 }				 
				 
				 $access_sec_id=get_db_field_value("tbl_admin_sections","id",array('section_controller'=>$final_url));
		 		$this->admin_lib->is_section_allowed($this->admin_id,$access_sec_id);
	 		 }
	 	 }*/
		 $this->activatePrvg=TRUE;
		 $this->deactivatePrvg=TRUE;
		$this->deletePrvg=TRUE;
		$this->enable_trash = $this->admin_type==1 ? 0 : 0;
		$this->is_trashed_req=$this->input->get_post('status')==2 ? 1 : 0;

		//Handle Revert Trash Request
		$activity_x=$this->input->get('activity_x',TRUE);
		if($activity_x=='revert'){
			$revert_x_type=$this->input->get('revert_x_type',TRUE);
			$revert_id=(int)$this->input->get('revert_id',TRUE);
			if($revert_x_type!='' && $revert_id>0){
				$this->revert_trash(array('type'=>$revert_x_type,'id'=>$revert_id));
			}
		}
		 $this->get_configuration_values();
		  
	}
	
	public function update_status($table,$auto_field='id')
	{
		$current_controller    = $this->router->fetch_class();
		$action                = $this->input->post('status_action',TRUE);
		$arr_ids               = $this->input->post('arr_ids',TRUE);
		$category_count        = $this->input->post('category_count',TRUE);
		$product_count         = $this->input->post('product_count',TRUE);
		
		$entity_type                = $this->input->post('entity_type',TRUE);

		if( is_array($arr_ids) )
		{
			$str_ids = implode(',', $arr_ids);
			
			if($action=='Activate')
			{
				foreach($arr_ids as $k=>$v )
				{
					$data = array('status'=>'1');
					$where = "$auto_field ='$v'";
					$this->utils_model->safe_update($table,$data,$where,FALSE);
					echo_sql();
					if($current_controller=='products'){
						$table1="wl_products_related";
						$where1 = "related_id ='$v'";
						$this->utils_model->safe_update($table1,$data,$where1,FALSE);							
					}
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success',lang('activate') );
				}
			}
			
			if($action=='Deactivate')
			{
				foreach($arr_ids as $k=>$v )
				{
					$total_category=($category_count!='')?count_category("AND parent_id='$v' AND status='1'") : '0';
					if($current_controller=='brand')
					{
						// $total_product   = count_products("AND brand_id='$v' ");
					}elseif($current_controller=='style_icons')
					{
						$total_product   = count_products("AND style_id='$v' ");
					}else if($current_controller=="color"){
						$this->db->where('color_id',$v);
						$this->db->from('wl_product_colors');
						$total_product=$this->db->count_all_results();
					}else if($current_controller=="size"){
						$this->db->where('size_id',$v);
						$this->db->from('wl_product_sizes');
						$total_product=$this->db->count_all_results();
					}else {
						$total_product = ($product_count!='') ? count_products("AND category_id='$v' AND status='1'") : '0';
					}
					if( $total_category>0)
					{
						$this->session->set_userdata(array('msg_type'=>'error'));
						$this->session->set_flashdata('error',lang('child_to_delete'));
					}elseif($total_product > 0 )
					{
						$this->session->set_userdata(array('msg_type'=>'error'));
						$this->session->set_flashdata('error',lang('products_to_delete'));
					}else
					{
						$data = array('status'=>'0');
						$where = "$auto_field ='$v'";
						$this->utils_model->safe_update($table,$data,$where,FALSE);
						if($current_controller=='products'){
							$table1="wl_products_related";
							$where1 = "related_id ='$v'";
							$this->utils_model->safe_update($table1,$data,$where1,FALSE);							
						}
						$this->session->set_userdata(array('msg_type'=>'success'));
						$this->session->set_flashdata('success',lang('deactivate') );
					}
				}
			}
			
			if($action=='Delete')
			{
				foreach($arr_ids as $k=>$v )
				{
					$total_category  = ( $category_count!='' ) ?  count_category("AND parent_id='$v' ")     : '0';
					if($current_controller=='brand')
					{
						// $total_product   = count_products("AND brand_id='$v' ");
					}else if($current_controller=="color"){
						$this->db->where('color_id',$v);
						//$this->db->from('wl_product_colors');
						$total_product=$this->db->count_all_results();
					}else if($current_controller=="size"){
						$this->db->where('size_id',$v);
						$this->db->from('wl_product_sizes');
						$total_product=$this->db->count_all_results();
					}else
					{
						$total_product   = ( $product_count!='' )  ?  count_products("AND category_id='$v' ")   : '0';
					}
					
					if( $total_category>0)
					{
						$this->session->set_userdata(array('msg_type'=>'error'));
						$this->session->set_flashdata('error',lang('child_to_delete'));
					}elseif($total_product > 0 )
					{
						$this->session->set_userdata(array('msg_type'=>'error'));
						$this->session->set_flashdata('error',lang('products_to_delete'));
					}else
					{
						$where = array($auto_field=>$v);
						$this->utils_model->safe_delete($table,$where,TRUE);
						
						if($current_controller=='products'){
							$where = array('product_id'=>$v);							
							//$this->utils_model->safe_delete('wl_product_colors',$where,TRUE);
							$this->utils_model->safe_delete('wl_product_sizes',$where,TRUE);
							$where = array('related_id'=>$v);							
							$this->utils_model->safe_delete('wl_products_related',$where,TRUE);
						}
						
						if(!empty($entity_type)){
							$where = array('entity_id'=>$v,"entity_type"=>$entity_type);
							$this->utils_model->safe_delete('wl_meta_tags',$where,FALSE); 
						}

						
						$this->session->set_userdata(array('msg_type'=>'success'));
						$this->session->set_flashdata('success',lang('deleted') );
					}
				}
			}
			
			if($action=='Tempdelete')
			{
				$data = array('status'=>'2');
				$where = "$auto_field IN ($str_ids)";
				$this->utils_model->safe_update($table,$data,$where,FALSE);
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',lang('deleted'));
			}
		}
		
		redirect($_SERVER['HTTP_REFERER'], '');
	}
	
	public function set_as($table,$auto_field='id',$data=array())
	{		
		$arr_ids               = $this->input->post('arr_ids',TRUE);
		
		if( is_array($arr_ids ) )
		{
			
			$str_ids = implode(',', $arr_ids);
			 
			if( is_array($data) && !empty($data) )
			{
				$data = $data;
				$where = "$auto_field IN ($str_ids)";
				$this->utils_model->safe_update($table,$data,$where,FALSE);
				
				
				$current_controller    = $this->router->fetch_class();
				
				if($current_controller=="orders" && $this->input->post("ord_status")!="" && ($this->input->post("ord_status")!="Pending" && $this->input->post("ord_status")!="Closed")){
					$this->load->library("dmailer");
					$mail_subject =$this->config->item('site_name')." Order overview";
				  $from_email   = $this->admin_info->admin_email;
				  $from_name    = $this->config->item('site_name');
				  
				  foreach($arr_ids as $key=>$val){
					  $order=get_db_single_row("wl_order",'*',array('order_id'=>$val));
					  $courier_details="";
					  if($this->input->post("ord_status")=="Dispatched"){
						  if($order['courier_company_name']!=""){
							  $courier_details.="<br/>Shipping Company Name : ".$order['courier_company_name'];
						  }
						  if($order['bill_number']!=""){
							  $courier_details.="<br/>Shipment Tracking No. : ".$order['bill_number'];
						  }
					  }

						$mail_to      = $order["email"];
						$body         = "Dear ".ucwords($order["first_name"]." ".$order["last_name"]);
						$body 					.=",<br /><br />";
						
						$body 					.="This is to notify you that your order is ".$this->input->post("ord_status")."  successfully .<br /><br />Here are the details<br /> Order Number: $order[invoice_number] <br/>".$this->input->post("ord_status")." Date/Time: ".date("d-m-Y h:i:s").$courier_details."<br /><br />Regards,<br />Customer Support Team<br />".$this->config->item('site_name');
						$mail_conf =  array(
						'subject'=>$this->config->item('site_name')." Order ".$this->input->post("ord_status"),
						'to_email'=>$mail_to,
						'from_email'=>$from_email,
						'from_name'=> $this->config->item('site_name'),
						'body_part'=>$body );
						$this->dmailer->mail_notify($mail_conf);
						
					}
				}
				
				
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success',"Record has been updated/deleted successfully.");			
			}	
			
		   redirect($_SERVER['HTTP_REFERER'], '');
		   
		}
		
	}
	
	
	/*
	
	$tblname = name of table 
	$fldname = order column name  of table 
	$fld_id  =  auto increment column name of table
			
	*/	
	
  	public function update_displayOrder($tblname, $fldname, $fld_id)
	{
	    $posted_order_data = $this->input->post('ord');

	    // Check if posted_order_data is an array
	    if (is_array($posted_order_data)) {
	        foreach ($posted_order_data as $key => $val) {
	            if ($val !== '') {
	                $val = (int) $val; // Ensure the value is an integer
	                $data = array($fldname => $val);
	                $where = "$fld_id = $key"; // Ensure proper spacing around '='
	                $this->utils_model->safe_update($tblname, $data, $where, TRUE);
	            }
	        }
	    }

	    $this->session->set_userdata(array('msg_type' => 'success'));
	    $this->session->set_flashdata('success', lang('order_updated'));
	    redirect($_SERVER['HTTP_REFERER'], '');
	}

	
	function get_max_disp_order($tbl,$cond)
	{
		$this->db->select_max('disp_order');
		$this->db->where($cond);
		$qry=$this->db->get($tbl);
		
		$dsorder=0;
		if($qry->num_rows() > 0)
		{
			$res=$qry->row();
			$dsorder= $res->disp_order;
		}
		return $dsorder+1;
	}

	public function upload_video($params=array()){
		$debug = isset($params['debug']) ?  $params['debug'] : FALSE;
		$field_name = isset($params['field_name']) ?  $params['field_name'] : 'file_upd';
		$max_chunk_file_size  = !empty($params['max_chunk_file_size']) ? $params['max_chunk_file_size'] : $this->config->item('max_video_bank_chunk_file_size');
		$file_allowed_type = !empty($params['file_allowed_type']) ? $params['file_allowed_type'] : 'video_bank_file';
		$dir_name = !empty($params['dir_name']) ? $params['dir_name'] : 'posts';
		$dir_name = trim($dir_name,'/');
		$ext = !empty($_FILES[$field_name]["name"]) ? pathinfo($_FILES[$field_name]["name"], PATHINFO_EXTENSION) : "";
		if($debug==TRUE){
			trace($_FILES);
			trace($_POST);
			echo $ext;
			die;
		}
		$err=1;
		$err_code="VID_FAILED400";
		$err_msg="Please upload file";
		$resp = array();
		$is_eof = $this->input->post('is_eof');
		$unique_key = $this->input->post('unique_key');
		$index = $this->input->post('index');
		if(isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH']>0 && empty($_FILES)){
			$resp = array('status'=>'error','err_code'=>'VID_FAILED413','err_msg'=>"Failed due to upload size restriction");
			echo json_encode($resp);
			exit;
		}
		
		$ex_rule_video = !$is_eof ? 'file_required|' : '';
		$ex_rule_video.=$index==0 ? "file_allowed_type[$file_allowed_type]|" : '';
		$this->form_validation->set_rules('file_upd','File',$ex_rule_video."file_size_max[".$max_chunk_file_size."~MB]");
		
		if($this->form_validation->run()===TRUE){
			if($ext!='' && !empty($_FILES) && $_FILES[$field_name]['tmp_name']!=''){
					$inp_path = $_FILES[$field_name]['tmp_name'];
					if($unique_key==''){
							$unique_key = uniqid()."_".random_string('alnum',4);
					}
					$ext_arr = explode('.',$_FILES[$field_name]['name']);
					$ext = end($ext_arr);
					$outfile_name = $unique_key.'.'.$ext;
					$oup_path = UPLOAD_DIR.'/'.$dir_name.'/'.$outfile_name;
					$out = @fopen($oup_path, $index == 0 ? "wb" : "ab");
					$chunk_size = 4*1024*1024;
					$handle = fopen($inp_path, "rb");
					if ($handle) {
						while (!feof($handle)) {
							$chunk = fread($handle, $chunk_size);
							fwrite($out, $chunk);
						}
						fclose($handle);
						$err=0;
						$res_temp_file = $this->db->select('id')->get_where('wl_temp_files',array('filename'=>$outfile_name,'folder'=>$dir_name))->row_array();
						if(!is_array($res_temp_file) || empty($res_temp_file)){
							$params_temp_insert = array('filename'=>$outfile_name,'folder'=>$dir_name,'added_date'=>$this->config->item('config.date.time'));
							$this->utils_model->safe_insert('wl_temp_files',$params_temp_insert,FALSE);
						}
					}else{
						$err=1;
						$err_code="VID_FAILED402";
						$err_msg="Failed to read file";
					}
			}else{
				$err=1;
				$err_code="VID_FAILED401";
				$err_msg="Please upload file";
			}
		}else{
			$err=1;
			$err_code="VID_FAILED422";
			$err_frm_flds = $this->form_validation->error_array();
			$err_msg=$err_frm_flds[$field_name];
		}

		if($err){
			$resp = array('status'=>'error','err_code'=>$err_code,'err_msg'=>$err_msg);
		}else{
			$resp = array('status'=>'success','unique_key'=>$unique_key,'ext'=>$ext);
		}
		echo json_encode($resp);
	}

	protected function fetch_gbl_admin_id(){
		$res_mem = $this->db->select('customers_id')->get_where('wl_customers',array('member_type'=>'1'))->row_array();
		if(is_array($res_mem) && !empty($res_mem)){
			return $res_mem['customers_id'];
		}
		return 0;
	}

	protected function fetch_admin_id(){
		$admin_id = (int) $this->session->userdata('admin_id');
		return $admin_id;
	}

	protected function check_mock_questions_stats($params=array()){
		$this->load->model(array('sitepanel/banks_model'));
		$question_id = !empty($params['question_id']) ? $params['question_id'] : 0;
		$mock_test_id = !empty($params['mock_test_id']) ? $params['mock_test_id'] : 0;
		$section_id = !empty($params['section_id']) ? $params['section_id'] : 0;
		if($mock_test_id>0 || $section_id>0){
			//Count Questions Section
			$fn_total_questions_section = function($section_id){
				$where_bank="qbk.status='1' AND mtqi.ref_mt_section_id='".$section_id."'";
				$where_bank = ltrim($where_bank," AND ");
				$params_bank = array(
								'fields'=>'COUNT(DISTINCT(qbk.id)) as gtotal',
								'where'=>$where_bank,
								'exjoin'=>array(
														//array('tbl'=>'wl_subjects as s','condition'=>'s.subject_id=qbk.ref_subject_id'),
														//array('tbl'=>'wl_subject_folders as fdr','condition'=>'fdr.folder_id=qbk.ref_folder_id'),
														array('tbl'=>'wl_mock_test_question as mtqi','condition'=>"mtqi.item_id=qbk.id",'type'=>'INNER')
													),
								'return_type'=>'row_array',
								'num_rows_required'=>FALSE,
								'debug'=>FALSE
								);

				$res_bank_count   = $this->banks_model->get_question_banks($params_bank);
				$total_section_questions_active = !empty($res_bank_count) ? (int) $res_bank_count['gtotal'] : 0;
				return $total_section_questions_active;
			};
			if($section_id > 0){/*Updation in sections*/

				$res_section = $this->db->select('mt_section_total_questions,mt_section_id,ref_mt_id,status')->get_where('wl_mock_test_sections',array('mt_section_id'=>$section_id))->row_array();
				if(!empty($res_section)){
					$res_mock_test = $this->db->select('mt_total_questions,mt_id')->get_where('wl_mock_test',array('mt_id'=>$res_section['ref_mt_id']))->row_array();
					if(!empty($res_mock_test)){
						$mock_test_id=$res_section['ref_mt_id'];
						if($res_section['status']!=1){
							$total_section_questions_active = $fn_total_questions_section($res_section['mt_section_id']);
							$actual_section_questions = $res_section['mt_section_total_questions'];
							$section_q_mismatch=$total_section_questions_active!=$actual_section_questions ? 1 : 0;
							$this->banks_model->safe_update('wl_mock_test_sections',array('section_q_mismatch'=>$section_q_mismatch,'sec_active_q'=>$total_section_questions_active),array('mt_section_id'=>$res_section['mt_section_id']),FALSE);
						}
					}
				}

			}
			//Update Related Master Mock Test
			if(empty($res_mock_test) && $mock_test_id>0){
				$res_mock_test = $this->db->select('mt_total_questions,mt_id')->get_where('wl_mock_test',array('mt_id'=>$mock_test_id))->row_array();
			}
			if(!empty($res_mock_test)){
				$actual_questions = $res_mock_test['mt_total_questions'];
				$total_questions_active = 0;
				$res_mock_test_sections = $this->db->select('mt_section_total_questions,mt_section_id')->get_where('wl_mock_test_sections',array('ref_mt_id'=>$mock_test_id,'status'=>1))->result_array();
				$total_actual_total_question = 0;
				if(!empty($res_mock_test_sections)){
						foreach($res_mock_test_sections as $secval){
							$total_section_questions_active = $fn_total_questions_section($secval['mt_section_id']);
							$actual_section_questions = $secval['mt_section_total_questions'];
							$total_actual_total_question+=$actual_section_questions;
							$section_q_mismatch=$total_section_questions_active!=$actual_section_questions ? 1 : 0;
							$this->banks_model->safe_update('wl_mock_test_sections',array('section_q_mismatch'=>$section_q_mismatch,'sec_active_q'=>$total_section_questions_active),array('mt_section_id'=>$secval['mt_section_id']),FALSE);
							$total_questions_active += $total_section_questions_active;
						}
				}
				//echo $total_questions_active."!====".$actual_questions;
				//echo '<br>'.$actual_questions."!=====".$total_actual_total_question;
				$q_mismatch=($total_questions_active!=$actual_questions || $actual_questions!=$total_actual_total_question) ? 1 : 0;
				$this->banks_model->safe_update('wl_mock_test',array('q_mismatch'=>$q_mismatch,'active_q'=>$total_questions_active),array('mt_id'=>$res_mock_test['mt_id']),FALSE);
				$this->banks_model->safe_update('wl_live_mock_test',array('live_q_mismatch'=>$q_mismatch),array('mock_test_id'=>$res_mock_test['mt_id']),FALSE);
			}

		}elseif($question_id>0){
			$where_bank="mtqi.item_id='".$question_id."' AND mt.status!='2'";
			$where_bank = ltrim($where_bank," AND ");
			$params_bank = array(
							'fields'=>'DISTINCT(mt.mt_id)',
							'where'=>$where_bank,
							'exjoin'=>array(
										//array('tbl'=>'wl_subjects as s','condition'=>'s.subject_id=qbk.ref_subject_id'),
										//array('tbl'=>'wl_subject_folders as fdr','condition'=>'fdr.folder_id=qbk.ref_folder_id'),
										array('tbl'=>'wl_mock_test_question as mtqi','condition'=>"mtqi.item_id=qbk.id",'type'=>'INNER'),
										array('tbl'=>'wl_mock_test_sections as mtqs','condition'=>"mtqi.ref_mt_section_id=mtqs.mt_section_id",'type'=>'INNER'),
										array('tbl'=>'wl_mock_test as mt','condition'=>"mtqs.ref_mt_id=mt.mt_id",'type'=>'INNER')
									),
							'debug'=>FALSE
							);

				$res_bank_array   = $this->banks_model->get_question_banks($params_bank);
				if(!empty($res_bank_array)){
					foreach($res_bank_array as $val){
						$this->check_mock_questions_stats(array('mock_test_id'=>$val['mt_id']));
					}
				}

		}
	}
	public function log_trash($params=array()){
		$log_type = !empty($params['type']) ? $params['type'] : '';
		$log_id = !empty($params['id']) ? (int) $params['id'] : 0;
		if($log_type!='' && $log_id>0 && $this->admin_type==2){
			$admin_id = $this->admin_id;
			switch($log_type){
				case 'category':
					$res = $this->db->select('status,category_id')->get_where('wl_categories',array('category_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_categories',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('category_id'=>$res['category_id']),FALSE);
					}
				break;
				case 'subject':
					$res = $this->db->select('status,subject_id')->get_where('wl_subjects',array('subject_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_subjects',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('subject_id'=>$res['subject_id']),FALSE);
					}
				break;
				case 'folder':
					$res = $this->db->select('status,folder_id')->get_where('wl_subject_folders',array('folder_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_subject_folders',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('folder_id'=>$res['folder_id']),FALSE);
					}
				break;
				case 'faculty':
					$res = $this->db->select('status,faculty_id')->get_where('wl_faculty',array('faculty_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_faculty',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('faculty_id'=>$res['faculty_id']),FALSE);
					}
				break;
				case 'coupon':
					$res = $this->db->select('status,coupon_id')->get_where('wl_coupons',array('coupon_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_coupons',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('coupon_id'=>$res['coupon_id']),FALSE);
					}
				break;
				case 'customers':
					$res = $this->db->select('status,customers_id')->get_where('wl_customers',array('customers_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_customers',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('customers_id'=>$res['customers_id']),FALSE);
					}
				break;
				case 'mock_test':
					$res = $this->db->select('status,mt_id')->get_where('wl_mock_test',array('mt_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_mock_test',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('mt_id'=>$res['mt_id']),FALSE);
					}
				break;
				case 'mock_test_sections':
					$res = $this->db->select('status,mt_section_id')->get_where('wl_mock_test_sections',array('mt_section_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_mock_test_sections',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('mt_section_id'=>$res['mt_section_id']),FALSE);
					}
				break;
				case 'notes':
					$res = $this->db->select('status,notes_id')->get_where('wl_notes',array('notes_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_notes',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('notes_id'=>$res['notes_id']),FALSE);
					}
				break;
				case 'notification':
					$res = $this->db->select('status,notification_id')->get_where('wl_notification',array('notification_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_notification',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('notification_id'=>$res['notification_id']),FALSE);
					}
				break;
				case 'pdf_bank':
					$res = $this->db->select('status,id')->get_where('wl_pdf_bank',array('id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_pdf_bank',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'video_bank':
					$res = $this->db->select('status,id')->get_where('wl_video_bank',array('id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_video_bank',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'question_bank':
					$res = $this->db->select('status,id')->get_where('wl_question_bank',array('id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_question_bank',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'course':
					$res = $this->db->select('status,course_id')->get_where('wl_courses',array('course_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_courses',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('course_id'=>$res['course_id']),FALSE);
					}
				break;
				case 'video_courses':
					$res = $this->db->select('status,vc_id')->get_where('wl_video_courses',array('vc_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_video_courses',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('vc_id'=>$res['vc_id']),FALSE);
					}
				break;
				case 'subscription_packages':
					$res = $this->db->select('status,subscription_id')->get_where('wl_subscription_packages',array('subscription_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_subscription_packages',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('subscription_id'=>$res['subscription_id']),FALSE);
					}
				break;
				case 'reported_question':
					$res = $this->db->select('status,report_id')->get_where('wl_report_question',array('report_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_report_question',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('report_id'=>$res['report_id']),FALSE);
					}
				break;
				case 'live_class_group':
					$res = $this->db->select('status,group_id')->get_where('wl_live_class_groups',array('group_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_class_groups',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('group_id'=>$res['group_id']),FALSE);
					}
				break;
				case 'live_classes':
					$res = $this->db->select('status,live_class_id')->get_where('wl_live_classes',array('live_class_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_classes',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('live_class_id'=>$res['live_class_id']),FALSE);
					}
				break;
				case 'live_class_comments':
					$res = $this->db->select('status,comment_id')->get_where('wl_live_class_feedback',array('comment_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_class_feedback',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('comment_id'=>$res['comment_id']),FALSE);
					}
				break;
				case 'live_class_chats':
					$res = $this->db->select('status,comment_id')->get_where('wl_live_class_chat',array('comment_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_class_chat',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('comment_id'=>$res['comment_id']),FALSE);
					}
				break;
				case 'member_groups':
					$res = $this->db->select('status,group_id')->get_where('wl_member_groups',array('group_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_member_groups',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('group_id'=>$res['group_id']),FALSE);
					}
				break;
				case 'live_mock_test':
					$res = $this->db->select('status,live_mt_id')->get_where('wl_live_mock_test',array('live_mt_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_mock_test',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('live_mt_id'=>$res['live_mt_id']),FALSE);
					}
				break;
				case 'posts':
					$res = $this->db->select('status,id')->get_where('wl_site_posts',array('id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_site_posts',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'post_comments':
					$res = $this->db->select('status,comment_id')->get_where('wl_post_comments',array('comment_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_post_comments',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('comment_id'=>$res['comment_id']),FALSE);
					}
				break;
				case 'mock_test_packages':
					$res = $this->db->select('status,mtp_id')->get_where('wl_mock_test_packages',array('mtp_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_mock_test_packages',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('mtp_id'=>$res['mtp_id']),FALSE);
					}
				break;
				case 'popup_banner':
					$res = $this->db->select('status,banner_id')->get_where('wl_banners',array('banner_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_banners',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('banner_id'=>$res['banner_id']),FALSE);
					}
				break;
				case 'banner':
					$res = $this->db->select('status,banner_id')->get_where('wl_banners',array('banner_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_banners',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('banner_id'=>$res['banner_id']),FALSE);
					}
				break;
				case 'member_msg':
					$res = $this->db->select('status,id')->get_where('wl_support_enquiry',array('id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_support_enquiry',array('last_status'=>$res['status'],'trashed_by'=>$admin_id,'trashed'=>1),array('id'=>$res['id']),FALSE);
					}
				break;
			}
			
		}

	}

	public function revert_trash($params=array()){
		$log_type = !empty($params['type']) ? $params['type'] : '';
		$log_id = !empty($params['id']) ? (int) $params['id'] : 0;
		if($log_type!='' && $log_id>0 && $this->admin_type==1){
			$admin_id = $this->admin_id;
			switch($log_type){
				case 'category':
					$res = $this->db->select('status,category_id,last_status,parent_id,category_name')->get_where('wl_categories',array('category_id'=>$log_id))->row_array();
					if(!empty($res)){
						$res_cat_exists=$this->db->select('COUNT(*) as gtotal')->get_where('wl_categories',array('category_name'=>$this->db->escape_str($res['category_name']),'parent_id'=>$res['parent_id'],'category_id !='=>$res['category_id'],'status !='=>'2'))->row_array();
						$is_cat_exists=!empty($res_cat_exists) ? (int) $res_cat_exists['gtotal'] : 0;
						if(!$is_cat_exists){
							$this->utils_model->safe_update('wl_categories',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('category_id'=>$res['category_id']),FALSE);
						}else{
							$err_msg="Cannot be reverted!! Exam Category already exists.";
						}
					}
				break;
				case 'subject':
					$res = $this->db->select('status,subject_id,last_status,subject_name')->get_where('wl_subjects',array('subject_id'=>$log_id))->row_array();
					if(!empty($res)){
						$res_subject_exists=$this->db->select('COUNT(*) as gtotal')->get_where('wl_subjects',array('subject_name'=>$this->db->escape_str($res['subject_name']),'subject_id !='=>$res['subject_id'],'status !='=>'2'))->row_array();
						$is_subject_exists=!empty($res_subject_exists) ? (int) $res_subject_exists['gtotal'] : 0;
						if(!$is_subject_exists){
							$this->utils_model->safe_update('wl_subjects',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('subject_id'=>$res['subject_id']),FALSE);
						}else{
							$err_msg="Cannot be reverted!! Subject already exists.";
						}
					}
				break;
				case 'folder':
					$res = $this->db->select('status,folder_id,last_status,ref_subject_id,folder_type,folder_name')->get_where('wl_subject_folders',array('folder_id'=>$log_id))->row_array();
					if(!empty($res)){
						$res_folder_exists=$this->db->select('COUNT(*) as gtotal')->get_where('wl_subject_folders',array('folder_name'=>$this->db->escape_str($res['folder_name']),'folder_type'=>$res['folder_type'],'ref_subject_id'=>$res['ref_subject_id'],'folder_id !='=>$res['folder_id'],'status !='=>'2'))->row_array();
						$is_folder_exists=!empty($res_folder_exists) ? (int) $res_folder_exists['gtotal'] : 0;
						if(!$is_folder_exists){
							$this->utils_model->safe_update('wl_subject_folders',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('folder_id'=>$res['folder_id']),FALSE);
						}else{
							$err_msg="Cannot be reverted!! Folder already exists.";
						}
					}
				break;
				case 'faculty':
					$res = $this->db->select('status,faculty_id,last_status')->get_where('wl_faculty',array('faculty_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_faculty',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('faculty_id'=>$res['faculty_id']),FALSE);
					}
				break;
				case 'coupon':
					$res = $this->db->select('status,coupon_id,last_status')->get_where('wl_coupons',array('coupon_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_coupons',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('coupon_id'=>$res['coupon_id']),FALSE);
					}
				break;
				case 'customers':
					$res = $this->db->select('status,customers_id,last_status,user_name,mobile_number')->get_where('wl_customers',array('customers_id'=>$log_id))->row_array();
					if(!empty($res)){
						$res_mem_exists=$this->db->select('COUNT(*) as gtotal')->where("(user_name='".$this->db->escape_str($res['user_name'])."' OR mobile_number='".$this->db->escape_str($res['mobile_number'])."')")->where(array('customers_id !='=>$res['customers_id'],'status !='=>'2'))->from('wl_customers')->get()->row_array();
						$is_mem_exists=!empty($res_mem_exists) ? (int) $res_mem_exists['gtotal'] : 0;
						if(!$is_mem_exists){
							$this->utils_model->safe_update('wl_customers',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('customers_id'=>$res['customers_id']),FALSE);
						}else{
							$err_msg="Cannot be reverted!! Member already exists.";
						}
					}
				break;
				case 'mock_test':
					$res = $this->db->select('status,mt_id,last_status')->get_where('wl_mock_test',array('mt_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_mock_test',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('mt_id'=>$res['mt_id']),FALSE);
						$this->check_mock_questions_stats(array('mock_test_id'=>$res['mt_id']));
					}
				break;
				case 'mock_test_sections':
					$res = $this->db->select('status,mt_section_id,last_status,ref_mt_id')->get_where('wl_mock_test_sections',array('mt_section_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_mock_test_sections',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('mt_section_id'=>$res['mt_section_id']),FALSE);
						$this->check_mock_questions_stats(array('mock_test_id'=>$res['ref_mt_id']));
					}
				break;
				case 'notes':
					$res = $this->db->select('status,notes_id,last_status')->get_where('wl_notes',array('notes_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_notes',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('notes_id'=>$res['notes_id']),FALSE);
					}
				break;
				case 'notification':
					$res = $this->db->select('status,notification_id,last_status')->get_where('wl_notification',array('notification_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_notification',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('notification_id'=>$res['notification_id']),FALSE);
					}
				break;
				case 'pdf_bank':
					$res = $this->db->select('status,id,last_status')->get_where('wl_pdf_bank',array('id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_pdf_bank',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'video_bank':
					$res = $this->db->select('status,id,last_status')->get_where('wl_video_bank',array('id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_video_bank',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'question_bank':
					$res = $this->db->select('status,id,last_status')->get_where('wl_question_bank',array('id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_question_bank',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('id'=>$res['id']),FALSE);
						$this->check_mock_questions_stats(array('question_id'=>$res['id']));
					}
				break;
				case 'course':
					$res = $this->db->select('status,course_id,last_status')->get_where('wl_courses',array('course_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_courses',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('course_id'=>$res['course_id']),FALSE);
					}
				break;
				case 'video_courses':
					$res = $this->db->select('status,vc_id,last_status')->get_where('wl_video_courses',array('vc_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_video_courses',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('vc_id'=>$res['vc_id']),FALSE);
					}
				break;
				case 'subscription_packages':
					$res = $this->db->select('status,subscription_id,last_status')->get_where('wl_subscription_packages',array('subscription_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_subscription_packages',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('subscription_id'=>$res['subscription_id']),FALSE);
					}
				break;
				case 'reported_question':
					$res = $this->db->select('status,report_id,last_status')->get_where('wl_report_question',array('report_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_report_question',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('report_id'=>$res['report_id']),FALSE);
					}
				break;
				case 'live_class_group':
					$res = $this->db->select('status,group_id,last_status,group_name')->get_where('wl_live_class_groups',array('group_id'=>$log_id))->row_array();
					if(!empty($res)){
						$res_group_exists=$this->db->select('COUNT(*) as gtotal')->get_where('wl_live_class_groups',array('group_name'=>$this->db->escape_str($res['group_name']),'group_id !='=>$res['group_id'],'status !='=>'2'))->row_array();
						$is_group_exists=!empty($res_group_exists) ? (int) $res_group_exists['gtotal'] : 0;
						if(!$is_group_exists){
							$this->utils_model->safe_update('wl_live_class_groups',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('group_id'=>$res['group_id']),FALSE);
						}else{
							$err_msg="Cannot be reverted!! Live Classs Group already exists.";
						}
					}
				break;
				case 'live_classes':
					$res = $this->db->select('status,live_class_id,last_status')->get_where('wl_live_classes',array('live_class_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_classes',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('live_class_id'=>$res['live_class_id']),FALSE);
					}
				break;
				case 'live_class_comments':
					$res = $this->db->select('status,comment_id,last_status')->get_where('wl_live_class_feedback',array('comment_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_class_feedback',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('comment_id'=>$res['comment_id']),FALSE);
					}
				break;
				case 'live_class_chats':
					$res = $this->db->select('status,comment_id,last_status')->get_where('wl_live_class_chat',array('comment_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_class_chat',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('comment_id'=>$res['comment_id']),FALSE);
					}
				break;
				case 'member_groups':
					$res = $this->db->select('status,group_id,last_status,group_name')->get_where('wl_member_groups',array('group_id'=>$log_id))->row_array();
					if(!empty($res)){
						$res_group_exists=$this->db->select('COUNT(*) as gtotal')->get_where('wl_member_groups',array('group_name'=>$this->db->escape_str($res['group_name']),'group_id !='=>$res['group_id'],'status !='=>'2'))->row_array();
						$is_group_exists=!empty($res_group_exists) ? (int) $res_group_exists['gtotal'] : 0;
						if(!$is_group_exists){
							$this->utils_model->safe_update('wl_member_groups',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('group_id'=>$res['group_id']),FALSE);
						}else{
							$err_msg="Cannot be reverted!! Group already exists.";
						}
					}
				break;
				case 'live_mock_test':
					$res = $this->db->select('status,live_mt_id,last_status')->get_where('wl_live_mock_test',array('live_mt_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_mock_test',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('live_mt_id'=>$res['live_mt_id']),FALSE);
					}
				break;
				case 'posts':
					$res = $this->db->select('status,id,last_status')->get_where('wl_site_posts',array('id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_site_posts',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'post_comments':
					$res = $this->db->select('status,comment_id,last_status')->get_where('wl_post_comments',array('comment_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_post_comments',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('comment_id'=>$res['comment_id']),FALSE);
					}
				break;
				case 'mock_test_packages':
					$res = $this->db->select('status,mtp_id,last_status')->get_where('wl_mock_test_packages',array('mtp_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_mock_test_packages',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('mtp_id'=>$res['mtp_id']),FALSE);
					}
				break;
				case 'popup_banner':
					$res = $this->db->select('status,banner_id,last_status')->get_where('wl_banners',array('banner_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_banners',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('banner_id'=>$res['banner_id']),FALSE);
					}
				break;
				case 'banner':
					$res = $this->db->select('status,banner_id,last_status')->get_where('wl_banners',array('banner_id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_banners',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('banner_id'=>$res['banner_id']),FALSE);
					}
				break;
				case 'member_msg':
					$res = $this->db->select('status,id,last_status')->get_where('wl_support_enquiry',array('id'=>$log_id))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_support_enquiry',array('status'=>$res['last_status'],'is_reverted'=>1,'trashed_by'=>0,'trashed'=>0),array('id'=>$res['id']),FALSE);
					}
				break;
			}

			if(!empty($res)){
					$msg=empty($err_msg) ? "Reverted successfully" : $err_msg;
					$msg_type=empty($err_msg) ? "success" : "error";
					$this->session->set_userdata(array('msg_type'=>$msg_type));
					$this->session->set_flashdata($msg_type,$msg );
			}
			
		}
		redirect($_SERVER['HTTP_REFERER'], '');
	}

	//Delete trash which means super admin also cannot view records
	public function delete_trash($params=array()){
		$log_type = !empty($params['type']) ? $params['type'] : '';
		$log_id = !empty($params['id']) ? (int) $params['id'] : 0;
		if($log_type!='' && $log_id>0 && $this->admin_type==1){
			$admin_id = $this->admin_id;
			switch($log_type){
				case 'category':
					$res = $this->db->select('status,category_id,last_status')->get_where('wl_categories',array('category_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_categories',array('is_reverted'=>2),array('category_id'=>$res['category_id']),FALSE);
					}
				break;
				case 'subject':
					$res = $this->db->select('status,subject_id,last_status')->get_where('wl_subjects',array('subject_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_subjects',array('is_reverted'=>2),array('subject_id'=>$res['subject_id']),FALSE);
					}
				break;
				case 'folder':
					$res = $this->db->select('status,folder_id,last_status')->get_where('wl_subject_folders',array('folder_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_subject_folders',array('is_reverted'=>2),array('folder_id'=>$res['folder_id']),FALSE);
					}
				break;
				case 'faculty':
					$res = $this->db->select('status,faculty_id,last_status')->get_where('wl_faculty',array('faculty_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_faculty',array('is_reverted'=>2),array('faculty_id'=>$res['faculty_id']),FALSE);
					}
				break;
				case 'coupon':
					$res = $this->db->select('status,coupon_id,last_status')->get_where('wl_coupons',array('coupon_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_coupons',array('is_reverted'=>2),array('coupon_id'=>$res['coupon_id']),FALSE);
					}
				break;
				case 'customers':
					$res = $this->db->select('status,customers_id,last_status')->get_where('wl_customers',array('customers_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_customers',array('is_reverted'=>2),array('customers_id'=>$res['customers_id']),FALSE);
					}
				break;
				case 'mock_test':
					$res = $this->db->select('status,mt_id,last_status')->get_where('wl_mock_test',array('mt_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_mock_test',array('is_reverted'=>2),array('mt_id'=>$res['mt_id']),FALSE);
					}
				break;
				case 'mock_test_sections':
					$res = $this->db->select('status,mt_section_id,last_status,ref_mt_id')->get_where('wl_mock_test_sections',array('mt_section_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_mock_test_sections',array('is_reverted'=>2),array('mt_section_id'=>$res['mt_section_id']),FALSE);
					}
				break;
				case 'notes':
					$res = $this->db->select('status,notes_id,last_status')->get_where('wl_notes',array('notes_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_notes',array('is_reverted'=>2),array('notes_id'=>$res['notes_id']),FALSE);
					}
				break;
				case 'notification':
					$res = $this->db->select('status,notification_id,last_status')->get_where('wl_notification',array('notification_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_notification',array('is_reverted'=>2),array('notification_id'=>$res['notification_id']),FALSE);
					}
				break;
				case 'pdf_bank':
					$res = $this->db->select('status,id,last_status')->get_where('wl_pdf_bank',array('id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_pdf_bank',array('is_reverted'=>2),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'video_bank':
					$res = $this->db->select('status,id,last_status')->get_where('wl_video_bank',array('id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_video_bank',array('is_reverted'=>2),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'question_bank':
					$res = $this->db->select('status,id,last_status')->get_where('wl_question_bank',array('id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_question_bank',array('is_reverted'=>2),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'course':
					$res = $this->db->select('status,course_id,last_status')->get_where('wl_courses',array('course_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_courses',array('is_reverted'=>2),array('course_id'=>$res['course_id']),FALSE);
					}
				break;
				case 'video_courses':
					$res = $this->db->select('status,vc_id,last_status')->get_where('wl_video_courses',array('vc_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_video_courses',array('is_reverted'=>2),array('vc_id'=>$res['vc_id']),FALSE);
					}
				break;
				case 'subscription_packages':
					$res = $this->db->select('status,subscription_id,last_status')->get_where('wl_subscription_packages',array('subscription_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_subscription_packages',array('is_reverted'=>2),array('subscription_id'=>$res['subscription_id']),FALSE);
					}
				break;
				case 'reported_question':
					$res = $this->db->select('status,report_id,last_status')->get_where('wl_report_question',array('report_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_report_question',array('is_reverted'=>2),array('report_id'=>$res['report_id']),FALSE);
					}
				break;
				case 'live_class_group':
					$res = $this->db->select('status,group_id,last_status')->get_where('wl_live_class_groups',array('group_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_class_groups',array('is_reverted'=>2),array('group_id'=>$res['group_id']),FALSE);
					}
				break;
				case 'live_classes':
					$res = $this->db->select('status,live_class_id,last_status')->get_where('wl_live_classes',array('live_class_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_classes',array('is_reverted'=>2),array('live_class_id'=>$res['live_class_id']),FALSE);
					}
				break;
				case 'live_class_comments':
					$res = $this->db->select('status,comment_id,last_status')->get_where('wl_live_class_feedback',array('comment_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_class_feedback',array('is_reverted'=>2),array('comment_id'=>$res['comment_id']),FALSE);
					}
				break;
				case 'live_class_chats':
					$res = $this->db->select('status,comment_id,last_status')->get_where('wl_live_class_chat',array('comment_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_class_chat',array('is_reverted'=>2),array('comment_id'=>$res['comment_id']),FALSE);
					}
				break;
				case 'member_groups':
					$res = $this->db->select('status,group_id,last_status')->get_where('wl_member_groups',array('group_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_member_groups',array('is_reverted'=>2),array('group_id'=>$res['group_id']),FALSE);
					}
				break;
				case 'live_mock_test':
					$res = $this->db->select('status,live_mt_id,last_status')->get_where('wl_live_mock_test',array('live_mt_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_live_mock_test',array('is_reverted'=>2),array('live_mt_id'=>$res['live_mt_id']),FALSE);
					}
				break;
				case 'posts':
					$res = $this->db->select('status,id,last_status')->get_where('wl_site_posts',array('id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_site_posts',array('is_reverted'=>2),array('id'=>$res['id']),FALSE);
					}
				break;
				case 'post_comments':
					$res = $this->db->select('status,comment_id,last_status')->get_where('wl_post_comments',array('comment_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_post_comments',array('is_reverted'=>2),array('comment_id'=>$res['comment_id']),FALSE);
					}
				break;
				case 'mock_test_packages':
					$res = $this->db->select('status,mtp_id,last_status')->get_where('wl_mock_test_packages',array('mtp_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_mock_test_packages',array('is_reverted'=>2),array('mtp_id'=>$res['mtp_id']),FALSE);
					}
				break;
				case 'popup_banner':
					$res = $this->db->select('status,banner_id,last_status')->get_where('wl_banners',array('banner_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_banners',array('is_reverted'=>2),array('banner_id'=>$res['banner_id']),FALSE);
					}
				break;
				case 'banner':
					$res = $this->db->select('status,banner_id,last_status')->get_where('wl_banners',array('banner_id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_banners',array('is_reverted'=>2),array('banner_id'=>$res['banner_id']),FALSE);
					}
				break;
				case 'member_msg':
					$res = $this->db->select('status,id,last_status')->get_where('wl_support_enquiry',array('id'=>$log_id,'trashed'=>1))->row_array();
					if(!empty($res)){
						$this->utils_model->safe_update('wl_support_enquiry',array('is_reverted'=>2),array('id'=>$res['id']),FALSE);
					}
				break;
			}

			/*if(!empty($res)){
					$msg=empty($err_msg) ? "Deleted successfully" : $err_msg;
					$msg_type=empty($err_msg) ? "success" : "error";
					$this->session->set_userdata(array('msg_type'=>$msg_type));
					$this->session->set_flashdata($msg_type,$msg );
			}*/
			
		}
		//redirect($_SERVER['HTTP_REFERER'], '');
	}

	public function checkmetaurl($url,$id=0,$prefix=''){
		$id=(int)$id;
		
		if($id>0){
			$cont='and entity_id !='.$id;
		}else{
			$cont='';
		}
		
		$prefix_url = '';
		/*Set this just before calling this method*/
		if(!empty($this->use_prefix_seo_url)){
			$prefix_url = trim($this->use_prefix_seo_url,'/').'/';
		}
		$cbk_friendly_url = $prefix_url.seo_url_title($url);
		$urlcount=$this->db->query("select * from wl_meta_tags where page_url='".$cbk_friendly_url."'".$cont."")->num_rows();
		
		if($urlcount>0)
		{
			$this->form_validation->set_message('checkmetaurl', 'URL already exists.');
			return FALSE;
		}else
		{
			return TRUE;
		}
		
	}
}
/*End of file*/