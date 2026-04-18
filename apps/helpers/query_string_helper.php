<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * query_string_helper
 *
 * Functions to help with assembling a XSS filtered query string, allowing
 * to remove and add key/value pairs.
 *
 * @license		http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author		Mike Funk
 * @link		http://mikefunk.com
 * @email		mike@mikefunk.com
 *
 * @file		query_string_helper.php
 * @version		1.2.4
 * @date		04/27/2012
 */

// --------------------------------------------------------------------------

/**
 * query_string function.
 *
 * Returns query string with added or removed key/value pairs.
 *
 * @param mixed $add (default: '') can be string or array
 * @param mixed $remove (default: '') can be string or array
 * @param bool $include_current (default: TRUE)
 * @return string
 */
function query_string($add = '', $remove = '', $include_current = TRUE)
{
	$_ci =& get_instance();

	// set initial query string
	$query_string = array();
	if ($include_current && $_ci->input->get() !== FALSE)
	{
		$query_string = $_ci->input->get();
	}

	// add to query string
	if ($add != '')
	{
		// convert to array
		if (is_string($add))
		{
			$add = array($add);
		}
		$query_string = array_merge($query_string, $add);
	}

	// remove from query string
	if ($remove != '')
	{
		// convert to array
		if (is_string($remove))
		{
			$remove = array($remove);
		}

		// remove from query_string
		foreach ($remove as $rm)
		{
			$key = array_search($rm, array_keys($query_string));
			if ($key !== FALSE)
			{
				unset($query_string[$rm]);
			}
		}
	}

	// return result
	$return = '';
	if (count($query_string) > 0)
	{
		$return = '?' . http_build_query($query_string);
	}
	return html_entity_decode($return);
}

// --------------------------------------------------------------------------

/**
 * uri_query_string function.
 *
 * returns uri_string with query_string on the end.
 *
 * @param mixed $add (default: '')
 * @param mixed $remove (default: '')
 * @param bool $include_current (default: TRUE) Whether to include the
 * current page's query string or start fresh.
 * @return string
 */
function uri_query_string($add = '', $remove = '', $include_current = TRUE)
{
	$_ci =& get_instance();
	return $_ci->uri->uri_string() . query_string($add, $remove, $include_current);
}

// --------------------------------------------------------------------------

/**
 * current_url_query_string function.
 *
 * returns uri_string with query_string on the end and current_url at the
 * beginning.
 *
 * @param mixed $add (default: '')
 * @param mixed $remove (default: '')
 * @param bool $include_current (default: TRUE) Whether to include the
 * current page's query string or start fresh.
 * @return string
 */
function current_url_query_string($add = '', $remove = '', $include_current = TRUE)
{
	$_ci =& get_instance();
	$_ci->load->helper('url');
	return current_url() . query_string($add, $remove, $include_current);
}

if(!function_exists('preservedRefUrl')){
	function preservedRefUrl($return=false){
		$_ci =& get_instance();
		$ref_url="";
		if( $_ci->input->get('ref')!=""){
			$curr_land_url =  current_url_query_string();
			if(preg_match("~ref=(.+)~",$curr_land_url,$matches)){
				$ref_url = $matches[1];
				$ref_url = urldecode($ref_url);
			}else{
				$ref_url = $_ci->input->get('ref');
			}
		}elseif( $_ci->input->post('ref')!=""){
			$ref_url = $_ci->input->post('ref');
		}else{
			$ref_url = $_ci->session->userdata('ref');
		}
		if($ref_url!=''){
			$ref_url = str_replace('.htm','',$ref_url);
		}
		if(!$return){
			//Commenting due to maintaining tab state.Every tab should has its own ref & should be passed from auth page
			//$this->session->set_userdata( array('ref'=>$ref_url ) );
		}else{
			return $ref_url;
		}
	}
}

if(! function_exists('validate_per_page')){
	function validate_per_page($params = array()){
		$options = array(
						  'fld_posted_per_page'=>'per_page',
						  'should_redirect'=>1,
						  'default_config_per_page'=>'per_page',
						  'per_page_cfg_type'=>'frontPageOpt'
						);
		$options = array_merge($options,$params);
		/*Validate the params if required*/
		$_ci =& get_instance();
		$error = 0;
		$posted_per_page =  $_ci->input->get_post($options['fld_posted_per_page']);
		if(isset($posted_per_page) || $posted_per_page!='' || $posted_per_page===0){
			$posted_per_page = (int) $posted_per_page;
			if($posted_per_page==0){
				 $posted_per_page = $_ci->config->item($options['default_config_per_page']);
			 	 $error = 1;
			}	
		}else{
			$posted_per_page = $_ci->config->item($options['default_config_per_page']);
		}
		$frontPageOpt = $_ci->config->item($options['per_page_cfg_type']);
		if(empty($error) && !in_array($posted_per_page,$frontPageOpt)){
		 $posted_per_page = $_ci->config->item($options['default_config_per_page']);
		 $error = 1;
		}
		if(!empty($error)){
			if(!empty($options['should_redirect'])){
				$final_href = site_url(uri_query_string(array('per_page'=>$posted_per_page)));
				redirect($final_href);
			}
		}
		return array('per_page'=>$posted_per_page,'error'=>$error);
	}
}
// --------------------------------------------------------------------------

/* End of file query_string_helper.php */
/* Location: ./query_string_helper/helpers/query_string_helper.php */
