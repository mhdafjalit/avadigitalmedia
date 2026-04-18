<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/* Param list 

	**** Single Dimension Associative Array i.e array('Size'=>'Large','Color'=>'Black','width'=>'22CM')
	**** ExcludeKeys array of keys that to be excluded
*/

if ( ! function_exists('create_hidden_fields'))
{
	function create_hidden_fields($arr_values,$excludeKeys = NULL)
	{
		$hiddenFields = '';
		if(is_array($arr_values) && count($arr_values)>0)
		{
			foreach($arr_values as $fieldKey=>$fieldVal)
			{
				if(is_array($excludeKeys) && in_array($fieldKey,$excludeKeys))
				{
					continue;
					
				}
				$hiddenFields .= "<input type='hidden' name='".$fieldKey."' value='".$fieldVal."'>\n";
				
				
			}
		}
		return $hiddenFields;
	}
}

/**
	 * Drop-down Menu
	 *
	 * @param	string
	 * @param	array
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	 
if ( ! function_exists('custom_drop_down'))
{
	function custom_drop_down($name = '', $options = array(), $selected = array(), $extra = '',$force_select = TRUE,$default_text = NULL)
	{
		// If name is really an array then we'll call the function again using the array
		if (is_array($name) && isset($name['name']))
		{
			isset($name['options']) OR $name['options'] = array();
			isset($name['selected']) OR $name['selected'] = array();
			isset($name['extra']) OR $name['extra'] = array();

			return custom_drop_down($name['name'], $name['options'], $name['selected'], $name['extra'],$name['force_select'],$name['default_option']);
		}

		if ( ! is_array($selected))
		{
			$selected = array($selected);
		}

		// If no selected state was submitted we will attempt to set it automatically
		if (count($selected) === 0 && isset($_POST[$name]))
		{
			$selected = array($_POST[$name]);
		}

		if ($extra != '')
		{
			$extra = ' '.$extra;
		}

		$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select name="'.$name.'"'.$extra.$multiple.">\n";
		
		if($force_select == TRUE)
		{
			$default_text = (!empty($default_text)) ? $default_text : 'Select';
			$form .= '<option value="">'.(string) $default_text."</option>\n";
			
		}

		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val))
			{
				if (empty($val))
				{
					continue;
				}

				$form .= '<optgroup label="'.$key."\">\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="'.$optgroup_key.'"'.$sel.'>'.(string) $optgroup_val."</option>\n";
				}

				$form .= "</optgroup>\n";
			}
			else
			{
				$form .= '<option value="'.$key.'"'.(in_array($key, $selected) ? ' selected="selected"' : '').'>'.(string) $val."</option>\n";
			}
		}

		return $form."</select>\n";
	}
}

if(!function_exists('db_drop_down'))
{
	function db_drop_down($varg=array()){

		$varg['default_text'] = (!array_key_exists('default_text',$varg)) ? "Select " : $varg['default_text'];

		$opt_val_fld = (!array_key_exists('opt_val_fld',$varg)) ? "id" : $varg['opt_val_fld'];

		$opt_txt_fld = (!array_key_exists('opt_txt_fld',$varg)) ? "" : $varg['opt_txt_fld'];

		$cond = (!array_key_exists('cond', $varg)) ? "" : $varg["cond"];


		$table_name = (!array_key_exists('table_name',$varg)) ? "" : $varg['table_name'];


		$CI = CI();
		$CI->db->select('*');
		if($cond!="")$CI->db->where($cond);
		$query=$CI->db->get($table_name);

		$arr=array();
		if($varg['default_text']!="")
			$arr=array(''=>$varg['default_text']);

		if($query->num_rows() > 0){
			$res=$query->result();
			$opt_txt_fld=explode(",",$opt_txt_fld);
			foreach($res as $val){
				$cid=$val->$opt_val_fld;
				$arr[$cid]=($val->$opt_txt_fld[0]).(isset($opt_txt_fld[1])?" ( ".$val->$opt_txt_fld[1]." ) ":"");

			}
		}
		return form_dropdown($varg['name'] ,$arr,$varg['current_selected_val'],$varg['format']);
	}
}

if(!function_exists('x_ref_fld_value')){
	function x_ref_fld_value($ref_obj,$fld_name,$type=''){
		//if(empty($ref_obj){ return '';}
		$fld_value = isset($ref_obj[$fld_name]) ? $ref_obj[$fld_name] : "";
		$fld_value = escape_chars($fld_value);
		return $fld_value;
	}
}


