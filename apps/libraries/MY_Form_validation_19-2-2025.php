<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
/**

 * Bonfire

 *

 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications

 *

 * @package   Bonfire

 * @author    Bonfire Dev Team

 * @copyright Copyright (c) 2011 - 2012, Bonfire Dev Team

 * @license   http://guides.cibonfire.com/license.html

 * @link      http://cibonfire.com

 * @since     Version 1.0

 * @filesource

 */



// ------------------------------------------------------------------------



/**

 * Form Validation

 *

 * This class extends the CodeIgniter core Form_validation library to add

 * extra functionality used in Bonfire.

 *

 * @package    Bonfire

 * @subpackage Libraries

 * @category   Libraries

 * @author     Bonfire Dev Team

 * @link       http://guides.cibonfire.com/core/form_validation.html

 *

 */

class MY_Form_validation extends CI_Form_validation
{


	/**

	 * Stores the CodeIgniter core object.

	 *

	 * @access public

	 *

	 * @var object

	 */

	public $CI;

    

	public $_error_array;

	

	//--------------------------------------------------------------------



      

	/**

	 * Constructor

	 *

	 * @return void

	 */

	public function __construct()

	{

		// Merged super-global $_FILES to $_POST to allow for better file validation inside of Form_validation library

		//$_POST = (isset($_FILES) && is_array($_FILES) && count($_FILES) > 0) ? array_merge($_POST,$_FILES) : $_POST;

		

		parent::__construct(); 

	



	}//end __construct()



function set_rules($field, $label = '', $rules = array(), $errors = array())

	{

		if (count($_POST) === 0 AND count($_FILES) > 0) //it will prevent the form_validation from working

		{

			//add a dummy $_POST

			$_POST['DUMMY_ITEM'] = '';

			parent::set_rules($field, $label, $rules, $errors);

			unset($_POST['DUMMY_ITEM']);

		}

		else

		{

			//we are safe just run as is

			parent::set_rules($field, $label, $rules, $errors);

		}	

	}

	

	function run($group='')

	{

		$rc = FALSE;

		log_message('DEBUG','called MY_form_validation:run()');

		if(count($_POST)===0 AND count($_FILES)>0)//does it have a file only form?

		{

			//add a dummy $_POST

			$_POST['DUMMY_ITEM'] = '';

			$rc = parent::run($group);

			unset($_POST['DUMMY_ITEM']);

		}

		else

		{

			//we are safe just run as is

			$rc = parent::run($group);

		}

		

		return $rc;

	}



	 function file_upload_error_message($field, $error_code)

	{

		switch ($error_code)

		{

			case UPLOAD_ERR_INI_SIZE:

				return $this->CI->lang->line('error_max_filesize_phpini');

			case UPLOAD_ERR_FORM_SIZE:

				return $this->CI->lang->line('error_max_filesize_form');

			case UPLOAD_ERR_PARTIAL:

				return $this->CI->lang->line('error_partial_upload');

			case UPLOAD_ERR_NO_FILE:

				$line = $this->CI->lang->line('file_required');

				return sprintf($line, $this->_translate_fieldname($field));

			case UPLOAD_ERR_NO_TMP_DIR:

				return $this->CI->lang->line('error_temp_dir');

			case UPLOAD_ERR_CANT_WRITE:

				return $this->CI->lang->line('error_disk_write');

			case UPLOAD_ERR_EXTENSION:

				return $this->CI->lang->line('error_stopped');

			default:

				return $this->CI->lang->line('error_unexpected').$error_code;

		}

	}	 

	

	

	

	function _execute($row, $rules, $postdata = NULL, $cycles = 0)

	{



		log_message('DEBUG','called MY_form_validation::_execute ' . $row['field']);

		//changed based on

		//http://codeigniter.com/forums/viewthread/123816/P10/#619868

		if(isset($_FILES[$row['field']]))

		{// it is a file so process as a file

			log_message('DEBUG','processing as a file');

			$postdata = $_FILES[$row['field']];

			

			//required bug

			//if some stupid like me never remember that it's file_required and not required

			//this will save a lot of var_dumping time.

			if(in_array('required', $rules))

			{

				$rules[array_search('required', $rules)] = 'file_required';

			}

			//before doing anything check for errors

		  if($postdata['error'] !== UPLOAD_ERR_OK)

			{
				//trace($row);
				//trace($postdata);die;

				//If the error it's 4 (ERR_NO_FILE) and the file required it's deactivated don't call an error

				if($postdata['error'] != UPLOAD_ERR_NO_FILE)

				{

					  $this->_error_array[$row['field']] = $this->file_upload_error_message($row['label'], $postdata['error']);
					  $this->_field_data[$row['field']]['error'] = $this->_error_array[$row['field']];

					  return FALSE;

				}

				elseif($postdata['error'] == UPLOAD_ERR_NO_FILE and in_array('file_required', $rules))

				{

					  $this->_error_array[$row['field']] = $this->file_upload_error_message($row['label'], $postdata['error']);

					// Below line added on 30-7-2015 for required file error show

					$this->_field_data[$row['field']]['error'] = $this->_error_array[$row['field']];

					  return FALSE;

				}

			}

			

			$_in_array = FALSE;		

		

			// If the field is blank, but NOT required, no further tests are necessary

			$callback = FALSE;

			if ( ! in_array('file_required', $rules) AND $postdata['size']==0)

			{

				// Before we bail out, does the rule contain a callback?

				if (preg_match("/(callback_\w+)/", implode(' ', $rules), $match))

				{

					$callback = TRUE;

					$rules = (array('1' => $match[1]));

				}

				else

				{

					return;

				}

			}		

			

			foreach($rules as $rule)

			{

				/// COPIED FROM the original class

				

				// Is the rule a callback?			

				$callback = FALSE;

				if (substr($rule, 0, 9) == 'callback_')

				{

					$rule = substr($rule, 9);

					$callback = TRUE;

				}

				

				// Strip the parameter (if exists) from the rule

				// Rules can contain a parameter: max_length[5]

				$param = FALSE;

				if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))

				{

					$rule	= $match[1];

					$param	= $match[2];

				}			

				

				// Call the function that corresponds to the rule

				if ($callback === TRUE)

				{

					if ( ! method_exists($this->CI, $rule))

					{		 

						continue;

					}

					

					// Run the function and grab the result

					$result = $this->CI->$rule($postdata, $param);



					// Re-assign the result to the master data array

					if ($_in_array == TRUE)

					{

						$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;

					}

					else

					{

						$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;

					}

				

					// If the field isn't required and we just processed a callback we'll move on...

					if ( ! in_array('file_required', $rules, TRUE) AND $result !== FALSE)

					{

						return;

					}

				}

				else

				{				

					if ( ! method_exists($this, $rule))

					{

						// If our own wrapper function doesn't exist we see if a native PHP function does. 

						// Users can use any native PHP function call that has one param.

						if (function_exists($rule))

						{

							$result = $rule($postdata);

												

							if ($_in_array == TRUE)

							{

								$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;

							}

							else

							{

								$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;

							}

						}

											

						continue;

					}



					$result = $this->$rule($postdata, $param);



					if ($_in_array == TRUE)

					{

						$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;

					}

					else

					{

						$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;

					}

				}

				

				//this line needs testing !!!!!!!!!!!!! not sure if it will work

				//it basically puts back the tested values back into $_FILES

				//$_FILES[$row['field']] = $this->_field_data[$row['field']]['postdata'];

								

				// Did the rule test negatively?  If so, grab the error.

				if ($result === FALSE)

				{			

					if ( ! isset($this->_error_messages[$rule]))

					{

						if (FALSE === ($line = $this->CI->lang->line($rule)))

						{

							$line = 'Unable to access an error message corresponding to your field name.';

						}						

					}

					else

					{

						$line = $this->_error_messages[$rule];

					}

					

					// Is the parameter we are inserting into the error message the name

					// of another field?  If so we need to grab its "field label"

					if (isset($this->_field_data[$param]) && isset($this->_field_data[$param]['label']))

					{

						$param = $this->_field_data[$param]['label'];

					}

					

					// Build the error message

					$message = sprintf($line, $this->_translate_fieldname($row['label']), $param);



					// Save the error message

					$this->_field_data[$row['field']]['error'] = $message;

				

					$this->_error_array[$row['field']]= $message;

					

					

					return;

				}				

			}		

		}

		else

		{

			log_message('DEBUG', 'Called parent _execute');

			parent::_execute($row, $rules, $postdata,$cycles);

		}

	}



	//--------------------------------------------------------------------



	/**

	 * Returns Form Validation Errors in a HTML Un-ordered list format.

	 *

	 * @access public

	 *

	 * @return string Returns Form Validation Errors in a HTML Un-ordered list format.

	 */
	 
	 
	 public function web_service_validation_errors_list()
	 {
		if (is_array($this->CI->form_validation->_error_array))
		 return $this->CI->form_validation->_error_array;
      
	} 
	 
 

	public function validation_errors_list()

	{

		if (is_array($this->CI->form_validation->_error_array))

		{

			$errors = (array) $this->CI->form_validation->_error_array;

			$error  = '<ul>' . PHP_EOL;
 

			foreach ($errors as $error)

			{

				$error .= "	<li>{$error}</li>" . PHP_EOL;

			}



			$error .= '</ul>' . PHP_EOL;

			return $error;

		}



		return FALSE;



	}//end validation_errors_list()



	//--------------------------------------------------------------------



	/**

	 * Performs the actual form validation

	 *

	 * @access public

	 *

	 * @param string $module Name of the module

	 * @param string $group  Name of the group array containing the rules

	 *

	 * @return bool Success or Failure

	 */

	 

		

	//end run()



	//--------------------------------------------------------------------

    

	/**

	 * Checks that a value is unique in the database

	 *

	 * i.e. '…|required|unique[users.name.id.4]|trim…'

	 *

	 * @abstract Rule to force value to be unique in table

	 * @usage "unique[tablename.fieldname.(primaryKey-used-for-updates).(uniqueID-used-for-updates)]"

	 * @access public

	 *

	 * @param mixed $value  The value to be checked

	 * @param mixed $params The table and field to check against, if a second field is passed in this is used as "AND NOT EQUAL"

	 * unique[roles.role_name]  | edit unique[roles.role_name,roles.role_id]   

	 

    	unique[users.email] | unique[users.email,users.id]

	 

	 * @return bool

	 

	 */

	 

	public function unique($str, $field) //unique used in for add record

	{

		list($table, $wherecond) = explode('.', $field, 2);

		$extcond =($wherecond!='') ? $wherecond : "";

		//$str =  $this->CI->db->escape_str($str);

		$this->CI->form_validation->set_message('unique','The <b> %s </b> already exists.');

		$query = $this->CI->db->query("SELECT COUNT(*) 

																	AS dupe 

																	FROM $table 

																	WHERE $extcond "

																	);

		$row = $query->row();

	//	echo $this->CI->db->last_query(); exit;

		return ($row->dupe > 0) ? FALSE : TRUE;

	} 



	// --------------------------------------------------------------------



	/**

	 * Check that a string only contains Alpha-numeric characters with

	 * periods, underscores, spaces and dashes

	 *

	 * @abstract Alpha-numeric with periods, underscores, spaces and dashes

	 * @access public

	 *

	 * @param string $str The string value to check

	 *

	 * @return	bool

	 */

	public function alpha_extra($str)

	{

		$this->CI->form_validation->set_message('alpha_extra', 'The %s field may only contain alpha-numeric characters, spaces, periods, underscores, and dashes.');

		return ( ! preg_match("/^([\.\s-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;



	}//end alpha_extra()



	// --------------------------------------------------------------------



	/**

	 * Check that the string matches a specific regex pattern

	 *

	 * @access public

	 *

	 * @param string $str     The string to check

	 * @param string $pattern The pattern used to check the string

	 *

	 * @return bool

	 */

	public function matches_pattern($str, $pattern)

	{

		if (preg_match('/^' . $pattern . '$/', $str))

		{

			return TRUE;

		}



		$this->CI->form_validation->set_message('matches_pattern', 'The %s field does not match the required pattern.');



		return FALSE;



	}//end matches_pattern()



	// --------------------------------------------------------------------



	/**

	 * Check if the field has an error associated with it.

	 *

	 * @access public

	 *

	 * @param string $field The name of the field

	 *

	 * @return bool

	 */

	public function has_error($field=null)

	{

		if (empty($field))

		{

			return FALSE;

		}



		return !empty($this->_field_data[$field]['error']) ? TRUE : FALSE;



	}//end has_error()



	//--------------------------------------------------------------------





	/**

	 * Check the entered password against the password strength settings.

	 *

	 * @access public

	 *

	 * @param string $str The password string to check

	 *

	 * @return bool

	 */

	public function valid_password($str)

	{

		// get the password strength settings from the database ex : 1a3!567A

		$min_length	= $this->CI->config->item('auth.password_min_length');

		$use_nums   = $this->CI->config->item('auth.password_force_numbers');

		$use_syms   = $this->CI->config->item('auth.password_force_symbols');

		$use_mixed  = $this->CI->config->item('auth.password_force_mixed_case');



		// Check length

		if (strlen($str) < $min_length)

		{

			$this->CI->form_validation->set_message('valid_password', '%s should  be at least '. $min_length .' character in length.');

			return FALSE;

		}



		// Check numbers

		if ($use_nums)

		{

			if (0 === preg_match('/[0-9]/', $str))

			{

				$this->CI->form_validation->set_message('valid_password', '%s must contain at least 1 number.');

				return FALSE;

			}

		}



		// Check Symbols

		if ($use_syms)

		{

			if (0 === preg_match('/[!@#$%^&*()._]/', $str))

			{

				$this->CI->form_validation->set_message('valid_password', '%s must contain at least 1 special character.');

				return FALSE;

			}

		}



		// Mixed Case?

		if ($use_mixed)

		{

			if (0 === preg_match('/[A-Z]/', $str))

			{

				$this->CI->form_validation->set_message('valid_password', '%s must contain at least 1 uppercase characters.');

				return FALSE;

			}



			if (0 === preg_match('/[a-z]/', $str))

			{

				$this->CI->form_validation->set_message('valid_password', '%s must contain at least 1 lowercase characters.');

				return FALSE;

			}

		}



		return TRUE;



	}//end valid_password()

	

	

	

	public function file_required($file)

	{	

	  

     	$filesz = $file['size'];	

		

		if($filesz===0)

		{

			$this->CI->form_validation->set_message('file_required', 'Uploading a file for %s is required');			

			return FALSE;

			

		}else

		{  

			return TRUE;

		}

	}

	

	

	/*

	$this->form_validation->set_rules('photo','photo','file_required|file_allowed_type[image]');

	

	*/
	
 function _file_mime_type($file)
	{
		// We'll need this to validate the MIME info string (e.g. text/plain; charset=us-ascii)
		$regexp = '/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/';

		/* Fileinfo extension - most reliable method
		 *
		 * Unfortunately, prior to PHP 5.3 - it's only available as a PECL extension and the
		 * more convenient FILEINFO_MIME_TYPE flag doesn't exist.
		 */
		if (function_exists('finfo_file'))
		{
			$finfo = @finfo_open(FILEINFO_MIME);
			if (is_resource($finfo)) // It is possible that a FALSE value is returned, if there is no magic MIME database file found on the system
			{
				$mime = @finfo_file($finfo, $file['tmp_name']);
				finfo_close($finfo);

				/* According to the comments section of the PHP manual page,
				 * it is possible that this function returns an empty string
				 * for some files (e.g. if they don't exist in the magic MIME database)
				 */
				if (is_string($mime) && preg_match($regexp, $mime, $matches))
				{
					$this->file_type = $matches[1];
					return;
				}
			}
		}

		/* This is an ugly hack, but UNIX-type systems provide a "native" way to detect the file type,
		 * which is still more secure than depending on the value of $_FILES[$field]['type'], and as it
		 * was reported in issue #750 (https://github.com/EllisLab/CodeIgniter/issues/750) - it's better
		 * than mime_content_type() as well, hence the attempts to try calling the command line with
		 * three different functions.
		 *
		 * Notes:
		 *	- the DIRECTORY_SEPARATOR comparison ensures that we're not on a Windows system
		 *	- many system admins would disable the exec(), shell_exec(), popen() and similar functions
		 *	  due to security concerns, hence the function_usable() checks
		 */
		if (DIRECTORY_SEPARATOR !== '\\')
		{
			$cmd = function_exists('escapeshellarg')
				? 'file --brief --mime '.escapeshellarg($file['tmp_name']).' 2>&1'
				: 'file --brief --mime '.$file['tmp_name'].' 2>&1';

			if (function_usable('exec'))
			{
				/* This might look confusing, as $mime is being populated with all of the output when set in the second parameter.
				 * However, we only need the last line, which is the actual return value of exec(), and as such - it overwrites
				 * anything that could already be set for $mime previously. This effectively makes the second parameter a dummy
				 * value, which is only put to allow us to get the return status code.
				 */
				$mime = @exec($cmd, $mime, $return_status);
				if ($return_status === 0 && is_string($mime) && preg_match($regexp, $mime, $matches))
				{
					$this->file_type = $matches[1];
					return;
				}
			}

			if ( ! ini_get('safe_mode') && function_usable('shell_exec'))
			{
				$mime = @shell_exec($cmd);
				if (strlen($mime) > 0)
				{
					$mime = explode("\n", trim($mime));
					if (preg_match($regexp, $mime[(count($mime) - 1)], $matches))
					{
						$this->file_type = $matches[1];
						return;
					}
				}
			}

			if (function_usable('popen'))
			{
				$proc = @popen($cmd, 'r');
				if (is_resource($proc))
				{
					$mime = @fread($proc, 512);
					@pclose($proc);
					if ($mime !== FALSE)
					{
						$mime = explode("\n", trim($mime));
						if (preg_match($regexp, $mime[(count($mime) - 1)], $matches))
						{
							$this->file_type = $matches[1];
							return;
						}
					}
				}
			}
		}

		// Fall back to the deprecated mime_content_type(), if available (still better than $_FILES[$field]['type'])
		if (function_exists('mime_content_type'))
		{
			$this->file_type = @mime_content_type($file['tmp_name']);
			if (strlen($this->file_type) > 0) // It's possible that mime_content_type() returns FALSE or an empty string
			{
				return;
			}
		}

		$this->file_type = $file['type'];
	}

	public	function file_allowed_type($file,$type)
	{
	  if($file['name']!="")
	  {
			$exts = explode(',', $type);				

			//is $type array? run self recursively

			/*if (count($exts) > 1)
			{

				foreach ($exts as $v)

				{

					$rc = $this->file_allowed_type($file,$v);

					if ($rc === TRUE)

					{

						return TRUE;

					}

				}

			}*/

			$this->_mimes =& get_mimes();

			//is type a group type? image, application, word_document, code, zip .... -> load proper array

			$ext_groups						= array();
			$ext_groups['image']            = array('jpg','jpeg','gif','png');
			$ext_groups['document']         = array('rtf','doc','docx','pdf','txt');
			$ext_groups['document_image']         = array('rtf','doc','docx','pdf','txt','jpg','jpeg','gif','png');
			$ext_groups['media']            = array('mpg','mpeg','swf','avi','flv','mov','mp4','wmv','mpg','mpeg4','3GP');
			$ext_groups['compressed']		= array('zip', 'gzip', 'tar', 'gz');
			$ext_groups['resume_document']    = array('rtf','doc','docx','txt'); 
			$ext_groups['audio']            = array('mp3');
			$ext_groups['excel_document']   = array('xls','xlsx');
			$ext_groups['pdf_bank_file'] 	= array('rtf','doc','docx','pdf','txt');
			$ext_groups['video'] 			= array('mp4','webm','ogg');
			$ext_groups['audio'] 			= array('flac','wav','mp3');
			$ext_groups['audio_video'] = array('flac','wav','mp3','mp4','webm','ogg');
			$ext_groups['pdf_image']         = array('pdf','jpg','jpeg','png');

			$ext_category_group = ['audio'=>array('audio'),'video'=>array('video'),'audio_video'=>array('video','audio')];

			$matched_ext_category_group = array();
			if (count($exts) >= 1)
			{
				$cp_exts = $exts;
				$exts = array();
				foreach ($cp_exts as $v)
				{
					if (array_key_exists($v, $ext_groups))
					{
						$exts = array_merge($exts,$ext_groups[$v]);
						if(isset($ext_category_group[$v]) && !empty($ext_category_group[$v])){
							array_push($matched_ext_category_group,$v);
						}
					}
					else
					{
						$exts[] = $v;
					}
				}
				array_unique($exts);
			}
			$file_ext = pathinfo($file['name'],PATHINFO_EXTENSION);  

			$file_ext = strtolower($file_ext);
			
			$this->_file_mime_type($file);
		
			$exts_allowed=implode(" | ",$exts);
			//trace($this->file_type);die;

			if ( ! in_array($file_ext, $exts))
			{
				$this->CI->form_validation->set_message('file_allowed_type', "File should be ". $exts_allowed);				

				return FALSE;

			}
			else
			{
				/*
				$is_grp_header_matched = 0;
				foreach($matched_ext_category_group as $grp_val){
					foreach($ext_category_group[$grp_val] as $ext_type_val){
						switch($ext_type_val){
							case 'video':
								$ret = $this->is_video_file($file['tmp_name']);
								if($ret){
									$is_grp_header_matched = 1;
									break;	
								}
							break;
							case 'audio':
								$ret = $this->is_audio_file($file['tmp_name']);
								if($ret){
									$is_grp_header_matched = 1;
									break;	
								}
							break;
						}
					}
				}

				if(!$is_grp_header_matched){
					$this->CI->form_validation->set_message('file_allowed_type', "File is either corrupted or invalid");	
					return FALSE;
				}
				*/
				if (in_array($file_ext, array('gif', 'jpg', 'jpeg', 'jpe', 'png'), TRUE) && @getimagesize(($file['tmp_name'])) === FALSE)
				{
					$this->CI->form_validation->set_message('file_allowed_type', "File should be ". $exts_allowed);	
					return FALSE;
				}
				if (isset($this->_mimes[$file_ext]))
				{
					$valid_ext =  is_array($this->_mimes[$file_ext])
						? in_array($this->file_type, $this->_mimes[$file_ext], TRUE)
						: ($this->_mimes[$file_ext] === $this->file_type);
					if(!$valid_ext){
						$this->CI->form_validation->set_message('file_allowed_type', "File is invalid");
						return FALSE;
					}
				}
				return TRUE;
			}

		}
	}

	



	

	

	public function file_size_max($file,$size_opts)
	{
		//echo $filesz = $_FILES[$file]['size'];	
		
		$filesz        = $file['size'];
		
		$param_size = explode("~",$size_opts);
		$max_size = $param_size['0'];

		$unit = !array_key_exists('1',$param_size) ? 'KB' : strtoupper(trim($param_size[1]));
		
		switch($unit)
		{
			case 'KB':
				$file_sz =  ceil($filesz/1024);
				$max_size = ceil($max_size/1024);
			break;
			case 'MB':
				$file_sz =  ceil($filesz/(1024*1024));
				$max_size = ceil($max_size/(1024*1024));
			break;
			case 'GB':
				$file_sz =  ceil($filesz/(1024*1024*1024));
				$max_size = ceil($max_size/(1024*1024*1024));
			break;
			default:
				$file_sz =  ceil($filesz/1024);
				$max_size = ceil($max_size/1024);
				$unit = "KB";
		}  
		   
		if($file_sz>$max_size)
		{		
			$this->CI->form_validation->set_message('file_size_max', $file['name']." is too big. (max allowed is $max_size $unit)");			       
			return FALSE;
		}
		return TRUE;
	}



	public function check_dimension($file_name,$dimen)

	{

		if (function_exists('getimagesize'))

		{

			//$file_name_tmp = $_FILES[$file_name]['tmp_name'];

			

			$file_name_tmp = $file_name['tmp_name'];			

			$dim = explode('x',$dimen,2);

			$d = @getimagesize($file_name_tmp);

			if( ( $d[0] > $dim[0] ) || ( $d[1] > $dim[1] ))

			{

				

			 $this->CI->form_validation->set_message('check_dimension', "File dimension  is too big. (max allowed dimension is $dimen )");				

			 return FALSE;

				

			}else

			{

				return TRUE;

			}

		}

	}

	



	//--------------------------------------------------------------------



	/**

	 * Checks that the entered string is one of the values entered as the second parameter.

	 * Please separate the allowed file types with a comma.

	 *

	 * @access public

	 *

	 * @param string $str      String field name to validate

	 * @param string $options String allowed values

	 *

	 * @return bool If files are in the allowed type array then TRUE else FALSE

	 */

	public function one_of($str, $options = NULL)

	{

		if (!$options)

		{

			log_message('debug', 'form_validation method one_of was called without any possible values.');

			return FALSE;

		}



		log_message('debug', 'form_validation one_of options:'.$options);



		$possible_values = explode(',', $options);



		if (!in_array($str, $possible_values))

		{

			$this->CI->form_validation->set_message('one_of', '%s must contain one of the available selections.');

			return FALSE;

		}



		return TRUE;



	}//end one_of()



		

	

	public function valid_url($str)

	{				

		if(preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $str))

		

			return true;

			

		else

		{

			return false;

		}

	}      

 

	

	public function exclude_text($str,$str2)

	{		

		if(trim(strtolower($str))!=trim(strtolower($str2)))

		

			return true;

			

		else

		{

			return false;

		}

	}

	

	

	/* 

	

	$this->form_validation->set_rules('comments','Comments','trim|required|valid_text[Comments]'); 	

	

	*/

	

	public function valid_text($str,$str1)

	{

		

		if($str==$str1)

		{

			$this->CI->form_validation->set_message('valid_text', 'The %s field must contain some other value(instead of the given text).');			

			return FALSE;

		}

		else

		{

			return TRUE;

		}

	}

	

	

	/* 	

	  $this->form_validation->set_rules('verification_code','Word Verification','trim|required|valid_captcha_code');

	

	

	*/

	

	public function valid_captcha_code($verification_code,$namesapce)
	{
		
		$this->CI->load->library('securimage_library');

		//trace($this->CI->session->userdata('securimage_code_value'));
		//echo "===".$verification_code;exit;

		$namesapce = $namesapce == '' ? 'default' : $namesapce;
		
		if ($this->CI->securimage_library->check($verification_code,$namesapce) === TRUE)
		{
			return TRUE;  
	
		}else
		{			
			$this->CI->form_validation->set_message('valid_captcha_code', '%s mismatch,please enter a valid verification code.');			
			return FALSE;
		}
	}

	      

	

	public function required_stripped($str)

	{

		$str=trim(strip_tags($str));

		$str = preg_replace("~((&|&amp;)nbsp;)+~","",$str);

		$str = trim($str);
		if($str==''){
			$this->CI->form_validation->set_message('required_stripped', '%s required');		
			return false;

		}else{
			return true;
		}



	}

	

	public function is_valid_amount($str)

	{

		if ( ! preg_match('/^[0-9]*(\.)?[0-9]+$/', $str))

		{

			return FALSE;

		}

		return TRUE;

	}

	

	

	public function decimal($str)

	{

		return (bool) preg_match('/^[\+]?[0-9]+\.[0-9]+$/', $str);

	}

	

	public function alpha($str)

	{

		

		return ( ! preg_match("/^([a-zA-Z ])+$/", $str)) ? FALSE : TRUE;

		

	}



	public function notnumeric($str)

	{

		

		return ( preg_match("/^([a-zA-Z ])+$/", $str)) ? TRUE : FALSE;

		

	}	

	

	public function valid_past_date($datetime)

	{		

		if($datetime=="0000-00-00" or $datetime=="0000-00-00 00:00:00")

		return FALSE;



		$timestamp=strtotime($datetime);

		

		$time_diff=time()-$timestamp;

			

		if($time_diff<=0)

		{

			return FALSE;

		}

		else 

		{

			return TRUE;

		}

	} 

	

	public function valid_future_date($datetime)

	{

		

		if($datetime=="0000-00-00" or $datetime=="0000-00-00 00:00:00")

		return FALSE;



		$timestamp=strtotime($datetime);

		

		$time_diff=time()-$timestamp;

			

		if($time_diff>=0)

		{

			return FALSE;

		}

		else 

		{

			return TRUE;

		}

	} 

	

	public function valid_age($datetime,$age=20)

	{

		

		if($datetime=="0000-00-00" or $datetime=="0000-00-00 00:00:00")

		return FALSE;

		

		$timestamp=strtotime($datetime);

		

		$time_diff=time()-$timestamp;

		

		$time_diff=round($time_diff/(60*60*24*365),2);

			

		if($time_diff>$age or $time_diff<5)

		{

			return FALSE;

		}

		else 

		{

			return TRUE;  

		}

	} 

	

	

	
public function valid_admin_password($str)
	{

		// get the password strength settings from the database ex : 1a3!567A

		$min_length	= $this->CI->config->item('auth.password_min_length');

		$use_nums   = $this->CI->config->item('auth.password_force_numbers');

		$use_syms   = $this->CI->config->item('auth.password_force_symbols');

		$use_mixed  = $this->CI->config->item('auth.password_force_mixed_case');



		// Check length

		if (strlen($str) < $min_length)

		{

			$this->CI->form_validation->set_message('valid_admin_password', '%s should be minimum 8 characters in length, and include at least 1 uppercase character, 1 numeric and 1 special character (!@#$&_..etc)');

			return FALSE;

		}



		// Check numbers

		if ($use_nums)

		{

			if (0 === preg_match('/[0-9]/', $str))

			{

				$this->CI->form_validation->set_message('valid_admin_password', '%s should be minimum 8 characters in length, and include at least 1 uppercase character, 1 numeric and 1 special character (!@#$&_..etc)');

				return FALSE;

			}

		}



		// Check Symbols

		if ($use_syms)

		{

			if (0 === preg_match('/[!@#$%^&*()._]/', $str))

			{

				$this->CI->form_validation->set_message('valid_admin_password', '%s should be minimum 8 characters in length, and include at least 1 uppercase character, 1 numeric and 1 special character (!@#$&_..etc)');

				return FALSE;

			}

		}



		// Mixed Case?

		if ($use_mixed)

		{

			if (0 === preg_match('/[A-Z]/', $str))

			{

				$this->CI->form_validation->set_message('valid_admin_password', '%s should be minimum 8 characters in length, and include at least 1 uppercase character, 1 numeric and 1 special character (!@#$&_..etc)');

				return FALSE;

			}



			if (0 === preg_match('/[a-z]/', $str))

			{

				$this->CI->form_validation->set_message('valid_admin_password', '%s should be minimum 8 characters in length, and include at least 1 uppercase character, 1 numeric and 1 special character (!@#$&_..etc)');

				return FALSE;

			}

		}



		return TRUE;



	}

	public function validate_date($date,$fmt='')
	{
			if($date==''){
					return TRUE;
			}
			$format = $fmt=='' ? 'Y-m-d' : $fmt;
			$d = DateTime::createFromFormat($format, $date);
			// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
			return $d && $d->format($format) === $date;
	}

	public function validate_gst_no($str)
	{
		if( ! preg_match("/^(?!([A-Za-z]+|[0-9]+)$)[0-9A-Za-z]+$/", $str)){
			$this->CI->form_validation->set_message('validate_gst_no', 'GST No. must be a mix of numbers & alphabets.');
			return FALSE;
		}
		return TRUE;
	}

	public function validate_cin_no($str)
	{
		if( ! preg_match("/^(?!([A-Za-z]+|[0-9]+)$)[0-9A-Za-z]+$/", $str)){
			$this->CI->form_validation->set_message('validate_cin_no', 'CIN No. must be a mix of numbers & alphabets.');
			return FALSE;
		}
		return TRUE;
	}

	public function is_video_file($file_name)
	{
		$getID3 = new getID3;
		$fileInfo = $getID3->analyze($file_name);
		//trace($fileInfo);die;
		return isset($fileInfo['video']);
	}
	public function is_audio_file($file_name)
	{
		$getID3 = new getID3;
		$fileInfo = $getID3->analyze($file_name);
		return isset($fileInfo['audio']);
	}

	public function setDummyFile($file,$tmp_file_name=''){
		$countfiles = count($_FILES[$file]['name']);
		for($ix=0;$ix<$countfiles;$ix++){
			$tmp_field_name = ($tmp_file_name!='' ? $tmp_file_name : "photos_").$ix;
			$_FILES[$tmp_field_name]['name'] = $_FILES[$file]['name'][$ix];
			$_FILES[$tmp_field_name]['type'] = $_FILES[$file]['type'][$ix];
			$_FILES[$tmp_field_name]['tmp_name'] = $_FILES[$file]['tmp_name'][$ix];
			$_FILES[$tmp_field_name]['error'] = $_FILES[$file]['error'][$ix];
			$_FILES[$tmp_field_name]['size'] = $_FILES[$file]['size'][$ix];
		}
	}
	



}//end class



//--------------------------------------------------------------------

// Helper Functions for Form Validation LIbrary

//--------------------------------------------------------------------



	/**

	 * Check if the form has an error

	 *

	 * @access public

	 *

	 * @param string $field Name of the field

	 *

	 * @return bool

	 */

	function form_has_error($field=null)

	{



		if (FALSE === ($OBJ =& _get_validation_object()))

		{

			return FALSE;

		}



		$return = $OBJ->has_error($field);



		return $return;

	}//end form_has_error()
	
   



//--------------------------------------------------------------------





/* Author :  http://net.tutsplus.com/tutorials/php/6-codeigniter-hacks-for-the-masters/ */

/* End of file : ./libraries/MY_Form_validation.php */