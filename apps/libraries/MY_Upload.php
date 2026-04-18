<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
/**

* CI-CMS Upload class overwrite

* This file is part of CI-CMS

* @package   CI-CMS

* @copyright 2008 Hery.serasera.org

* @license   http://www.gnu.org/licenses/gpl.html

* @version   $Id$

*/

class MY_Upload extends CI_Upload
{
	public  function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
	}

	protected function _prep_filename($filename)
	{
		if ($this->mod_mime_fix === FALSE OR $this->allowed_types === '*' OR ($ext_pos = strrpos($filename, '.')) === FALSE)

		{
			return $filename;
		}
		$ext = substr($filename, $ext_pos);
		$filename = substr($filename, 0, $ext_pos);
		//return str_replace('.', '_', $filename).$ext;
		return url_title(strtolower($filename)).$ext;
	}


	function my_upload($filed,$path,$params=array())

	{

		$CI =$this->CI;
		$has_error=0;
		$allowed_size = isset($params['allowed_size']) && !empty($CI->config->item($params['allowed_size'])) ?  $CI->config->item($params['allowed_size']) : $CI->config->item('allow.file.size');

		$allowed_width	=	$CI->config->item('allow.image.width');

		$allowed_height	=	$CI->config->item('allow.image.height');
		$CI->load->library('upload');

		$config['upload_path'] = UPLOAD_DIR.'/'.$path.'/';

		$config['allowed_types'] = file_ext($_FILES[$filed]['name']);

		$config['max_size']  = $allowed_size;

		$config['max_width']  = $allowed_width;

		$config['max_height']  = $allowed_height;

		$config['remove_spaces'] = TRUE;
		$CI->upload->initialize($config);

		if ( ! $CI->upload->do_upload($filed))
		{
			$has_error=1;
		}else{
			$has_error=0;
		}
		$ret = array('err'=>$has_error,'upload_data'=>$has_error ? '' : $CI->upload->data(),'err_dtls'=>$has_error ? $CI->upload->display_errors()  : '');
		return $ret;

	}
		
	function my_upload_multiple($filed,$path,$params=array())
	{
		$CI =$this->CI;

		$allowed_size = isset($params['allowed_size']) && !empty($CI->config->item($params['allowed_size'])) ?  $CI->config->item($params['allowed_size']) : $CI->config->item('allow.file.size');
		$allowed_width	=	$CI->config->item('allow.image.width');
		$allowed_height	=	$CI->config->item('allow.image.height');
		$max_allowed_photos =  isset($params['max_allowed_photos']) ? (int) $params['max_allowed_photos'] : 10;

		$file_data = array();
		$file_err_data = array();
		$CI->load->library('upload');

		// Count total files
		$tmp_field_name = "xv_custom_pht";
		$countfiles = count($_FILES[$filed]['name']);
		$count_photo_uploaded=0;
		// Looping all files
		for($i=0;$i<$countfiles;$i++){
			$should_process_photo = $max_allowed_photos ? (($count_photo_uploaded<$max_allowed_photos) ? 1 : 0) : 1;
			if(!empty($_FILES[$filed]['name'][$i]) && $should_process_photo){
				// Define new $_FILES array - $_FILES['file']
				$_FILES[$tmp_field_name]['name'] = $_FILES[$filed]['name'][$i];
				$_FILES[$tmp_field_name]['type'] = $_FILES[$filed]['type'][$i];
				$_FILES[$tmp_field_name]['tmp_name'] = $_FILES[$filed]['tmp_name'][$i];
				$_FILES[$tmp_field_name]['error'] = $_FILES[$filed]['error'][$i];
				$_FILES[$tmp_field_name]['size'] = $_FILES[$filed]['size'][$i];

				// Set preference
				$config['upload_path'] = UPLOAD_DIR.'/'.$path.'/';
				$config['allowed_types'] = file_ext($_FILES[$filed]['name'][$i]);
				$config['max_size']  = $allowed_size;
				$config['max_width']  = $allowed_width;
				$config['max_height']  = $allowed_height;
				$config['remove_spaces'] = TRUE;
				$config['file_name'] = $_FILES[$filed]['name'][$i];
				//Load upload library
				$CI->upload->initialize($config);

				// File upload
				if($CI->upload->do_upload($tmp_field_name)){
					// Get data about the file
					$uploadData = $CI->upload->data();
					$filename = $uploadData['file_name'];
					// Initialize array
					$file_data[] = $filename;
					$count_photo_uploaded++;
				}else{
					$file_err_data[$i]=$CI->upload->display_errors();
				}
			}
		}
		$has_error = !empty($file_err_data) ? 1 : 0;
		$ret = array('err'=>$has_error,'uploaded_files'=>$file_data,'err_dtls'=>$file_err_data);
		return $ret;
	}
}
?>