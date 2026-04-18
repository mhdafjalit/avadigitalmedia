<?php
if(is_array($res) && !empty($res))
{
  foreach($res as $val)
  {
	$option_text_str = $val[$option_text_field];
	if(is_array($selected_id))
	{
	  $selected = in_array($val[$option_val_field],$selected_id) ? ' selected="selected"' : '';
	}
	else
	{
	  $selected = $val[$option_val_field]==$selected_id ? ' selected="selected"' : '';
	}
	if(isset($status_active_value) && isset($option_status_field) && isset($status_arr) && !empty($show_status)){
			$option_status_value = $val[$option_status_field];
			if($option_status_value!=$status_active_value && isset($status_arr[$option_status_value])){
				$option_text_str.=" --- (".$status_arr[$option_status_value].")";	
			}
	}
  ?>
	<option value="<?php echo $val[$option_val_field];?>"<?php echo $selected;?><?php echo !empty($show_title) ? ' title="'.escape_chars($option_text_str).'"' : '';?>><?php echo $option_text_str;?></option>
<?php
  }
}
  