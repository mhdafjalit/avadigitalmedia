<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/* Returns list Of banner Positions */
if ( ! function_exists('get_banner_positions_array'))
{
	function get_banner_positions_array($banner_section = NULL)
	{
		$ci = &get_instance();
		
		$postions_arr = array();
		
		$position_key_arr = array();
		
		$conf_ban_postions = $ci->config->item('bannersz');  // List Of All Positions
		
		
		$ban_section_positions = $ci->config->item('banner_section_positions');  // List of section key and postions array key mapped
		 
		if(!empty($banner_section))
		{
			
			if(array_key_exists($banner_section,$ban_section_positions))
			{
				$position_key_arr = $ban_section_positions[$banner_section];
				
					if(count($position_key_arr)>0)
					{
						foreach($position_key_arr as $postion_key)
						{
							if(array_key_exists($postion_key,$conf_ban_postions))
							{
								$postions_arr[$postion_key] = $postion_key. " &raquo; Best banner Size ".$conf_ban_postions[$postion_key];
								
							}
							
							
							
						}
					}
				
				
			}
			
		}
		else{
			
			$postions_arr = $conf_ban_postions;
			
			if(count($postions_arr)>0)
					{
						foreach($postions_arr as $postion_key=>$position_val)
						{
							
								$postions_arr[$postion_key] = $postion_key. " &raquo; Best banner Size ".$position_val;
							
							
							
						}
				}
			
		}
		
		return $postions_arr;
		
		
		
	}

}
if ( ! function_exists('banner_postion_drop_down'))
{
	function banner_postion_drop_down($name = '',$default_sel = '', $ban_section = NULL,$extra = '')
	{
		$html = '';
		$banner_postions = array();
		$ci = &get_instance();
		
		if(!empty($ban_section))
		{
			
			$banner_postions = get_banner_positions_array($ban_section);
			
		}
		else
		{
			$banner_postions = get_banner_positions_array();
			
		}
		
		$html = custom_drop_down($name,$banner_postions,$default_sel,$extra,TRUE,'Select Position');	
		echo $html;	

		
	}
}

