<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/* Created by Anand */


function db_drop_down($tbl_name,$condition,$varg=array())
{	
	$CI = CI();	
	$var="";
	
	$varg['default_text']=!array_key_exists('default_text',$varg) ? "Select" : $varg['default_text'];
	$varg['id']=!array_key_exists('id',$varg) ? $varg['name'] : $varg['id'];
	$opt_val_fld = $varg['opt_val_fld'];
	$opt_txt_fld = $varg['opt_txt_fld']; 
	//trace($varg['current_selected_val']);
	$var.='<select name="'.$varg['name'].'" id="'.$varg['id'].'" '.$varg['format'].'>';
	if($varg['default_text']!="")
	{
		$var.='<option value="" selected="selected">'.$varg['default_text'].'</option>';
	}	
	
	$CI->db->select("$opt_val_fld,$opt_txt_fld");
	$CI->db->from("$tbl_name");
	if(!empty($condition))
	$CI->db->where($condition);
	
	$contry_res=$CI->db->get()->result_array();
	
	foreach($contry_res as $key=>$val)
	{		
	
	
		if(is_array($varg['current_selected_val']))
		{
			$select_element=in_array($val[$opt_val_fld],$varg['current_selected_val']) ? "selected" : "";
		}else
		{
			$select_element=( $varg['current_selected_val']==$val[$opt_val_fld] ) ? "selected" : "";
		}		
		$var.='<option value="'.$val[$opt_val_fld].'" '.$select_element.'>'.ucfirst($val[$opt_txt_fld]).'</option>';
	}
	$var.='</select>';
	return $var;
}

   
/* Ends */