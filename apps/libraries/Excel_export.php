<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Excel_export
{
	
	public function __construct()
	{
	   if (!isset($this->ci->ci))
	   {
			$this->ci =& get_instance();
	   }		
	   $this->ci->load->library('excel','safe_encrypt');
	   $this->ci->load->helper(array('category/category'));
	   $this->ci->load->model(array('category/category_model'));
	  
	}	
	
	public function export_excel_file($export_file_name,$fldlist,$sql,$headeings=array())
	{
		$filename=$export_file_name; //save our workbook as this file name
		$heading_fld_array=$fldlist;// Editable area
		$res=custom_result_set($sql);
		
		/*	Editable Area End	*/
		
		/* NOTE :	PLEASE DON'T TOUCH BELOW CODE */		
	
	
		if(is_array($res) && count($res)>0)
		{
			$cell_range=range('A','Z');
			
			$this->ci->load->library('excel');
			//activate worksheet number 1
			$this->ci->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->ci->excel->getActiveSheet()->setTitle('worksheet');
			//set cell A1 content with some text
			$prefix='';
			for($i=0,$j=0,$l=0;$i<count($heading_fld_array);$i++,$j++)
			{
				$cellnum=$i+1;
				
				if($j%26==0 && $j!=0){
					$j=0;
					$prefix=$cell_range[$l];
					$l++;
				}
				
				$cellname=$prefix.$cell_range[$j].'1';
				$this->ci->excel->getActiveSheet()->setCellValue($cellname, $heading_fld_array[$i]);
				//change the font size
				$this->ci->excel->getActiveSheet()->getStyle($cellname)->getFont()->setSize(12);
				
				//make the font become bold
				$this->ci->excel->getActiveSheet()->getStyle($cellname)->getFont()->setBold(true);
				//$this->ci->excel->getActiveSheet()->getColumnDimension($heading_fld_array[$i])->setAutoSize(true); 
			}
			
			$cnt=1;
			$fld_row=2;
			$prefix='';
			$k=0;
			$l=0;
		
			foreach($res as $data)
			{
				
				if(is_array($data) && count($data) > 0)
				{
					//$k=0;
					$i=0;
					$w=1;
					foreach($data as $subkey=>$subdataval)
					{
						
						if($subkey=='category_id'){
							  if(strlen($subdataval)>0){
								 $subdataval=  get_category_list_by_str($subdataval) ;
							   };
						}
						if($subkey=='bill_country'){
							  if(strlen($subdataval)>0){
								 $subdataval=  country_name($subdataval) ;
							   };
						}
						if($subkey=='bill_state'){
							  if(strlen($subdataval)>0){
								 $subdataval=  state_name($subdataval) ;
							   };
						}
						if($subkey=='bill_city'){
							  if(strlen($subdataval)>0){
								 $subdataval=  city_name($subdataval) ;
							   };
						}
						if($subkey=='pick_country'){
							  if(strlen($subdataval)>0){
								 $subdataval=  country_name($subdataval) ;
							   };
						}
						if($subkey=='pick_state'){
							  if(strlen($subdataval)>0){
								 $subdataval=  state_name($subdataval) ;
							   };
						}
						if($subkey=='pick_city'){
							  if(strlen($subdataval)>0){
								 $subdataval=  city_name($subdataval) ;
							   };
						}
						if($subkey=='ship_country'){
							  if(strlen($subdataval)>0){
								 $subdataval=  country_name($subdataval) ;
							   };
						}
						if($subkey=='ship_state'){
							  if(strlen($subdataval)>0){
								 $subdataval=  state_name($subdataval) ;
							   };
						}
						if($subkey=='ship_city'){
							  if(strlen($subdataval)>0){
								 $subdataval=  city_name($subdataval) ;
							   };
						}
						if($subkey=='shipping_charges'){
							$subdataval= ($subdataval>0)?display_price($subdataval):'Free';
						}
						if($subkey=='total_coupon_discount'){
							$subdataval= ($subdataval>0)?display_price($subdataval):'NA';
						}
						if($subkey=='avail_credit_points_value'){
							$subdataval= ($subdataval>0)?display_price($subdataval):'NA';
						}
						if($subkey=='payment_status'){
							$subdataval= ($subdataval==1)?'Paid':'Pending';
						}
						if($subkey=='status'){
							$subdataval= ($subdataval==1)?'Active':'Inactive';
						}
						if($subkey=='is_home'){
							$subdataval= ($subdataval==1)?'Yes':'No';
						}
						if($subkey=='cat_breadcum_id'){
							$subdataval= category_breadcrumbs_without_link($subdataval);
							$subdataval= strip_tags($subdataval);
							$subdataval= str_replace("&gt;",">",$subdataval);
						}
						$attnamestr='';
						if($subkey=='attribues'){
							$row_data=$this->ci->category_model->get_cat_attr_map_value_by_pid(18);	
							$att_name=array();
							if(is_array($row_data) && !empty($row_data)){
								foreach($row_data as $attval){
									$att_name[]=$attval['input_fld_label'];
								}
								$attnamestr=implode(",",$att_name);
							}
							
							$subdataval=$attnamestr;
						}
						if($subkey=='no_of_products'){
						   $total_records= count_record ("tbl_products","cat_id ='".$subdataval."' and status ='1'");
						   $subdataval=  $total_records;
						}
						
						
						
						
						if($k%26==0  && $k!=0){
							$k=0;
							$prefix=$cell_range[$l];
							$l++;
						}
						
						$cell_name=$prefix.$cell_range[$k].$fld_row;
					
						$this->ci->excel->getActiveSheet()->setCellValue($cell_name, $subdataval);
						$k++;
					}
					
				}
			   $k=0;
				$fld_row++;
				$cnt++;
			}
						
			$this->ci->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->ci->excel, 'Excel5');
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
		
		}
		else
		{
			echo 'No record found...';
		}
	}
	
	
}