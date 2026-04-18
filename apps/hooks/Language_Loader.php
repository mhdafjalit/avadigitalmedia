<?php
class Language_Loader
{
	public  function initialize_language($lng_theme='')
	{
		$ci =& get_instance();
		$ci->load->helper('language');

		if($ci->uri->segment(1)=='sitepanel')
		{
			$switch_pg = FALSE;
			$ci->config->set_item('language','english');
		}
		else
		{
			$switch_pg = FALSE;
			$this->set_default_theme();
			/*if($ci->input->get('lang')!='')
			{
				$lng_theme = $ci->input->get('lang');
				$switch_pg = FALSE;
			}
			else
			{
				$lng_theme = $ci->session->userdata('lng_theme');
				$switch_pg = FALSE;
			}
			
			if($lng_theme == '')
			{
				$this->set_default_theme();
			}
			else
			{
				$cfg_lang_opts = $ci->config->item('lang_opts');
				if(!array_key_exists('_'.$lng_theme,$cfg_lang_opts))
				{
					$this->set_default_theme();
				}
				else
				{
					$cfg_theme_opts = $ci->config->item('theme_opts');
					if(!array_key_exists('_'.$lng_theme,$cfg_theme_opts))
					{
						$this->set_default_theme();
					}
					else
					{
						$ci->session->set_userdata('lng_theme',$lng_theme);
						$ci->config->set_item('language',$cfg_theme_opts['_'.$lng_theme]);
					}
				}
			}*/
		}
		if($switch_pg===TRUE)
		{
			redirect($_SERVER['HTTP_REFERER']);
		}
		else
		{
			$language = $ci->config->item('language');
			$ci->lang->load($language);
		}
	}

	private function set_default_theme()
	{
		$ci =& get_instance();
		$ci->session->set_userdata('lng_theme','arabic');
		$ci->config->set_item('language','arabic');
	}
}
/* End of file*/