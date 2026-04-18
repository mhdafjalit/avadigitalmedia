<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');



/*

 * CKEditor helper for CodeIgniter

 * 

 * @author Samuel Sanchez <samuel.sanchez.work@gmail.com> - http://kromack.com/

 * @package CodeIgniter

 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/us/

 * @tutorial http://kromack.com/developpement-php/codeigniter/ckeditor-helper-for-codeigniter/

 * @see http://codeigniter.com/forums/viewthread/127374/

 * @version 2010-08-28

 * 

 */



/**

 * This function adds once the CKEditor's config vars

 * @author Samuel Sanchez 

 * @access private

 * @param array $data (default: array())

 * @return string

 */
 
if(!defined('CKEDITOR_INNER_FOLDER')){
	if ( version_compare( phpversion(), '7.1', '<' ) ){
		define('CKEDITOR_INNER_FOLDER',"ckeditor/5.6/");	
	}elseif ( version_compare( phpversion(), '8.0', '<' ) ){
		define('CKEDITOR_INNER_FOLDER',"ckeditor/7.0/");	
	}elseif ( version_compare( phpversion(), '8.1', '<' ) ){
		define('CKEDITOR_INNER_FOLDER',"ckeditor/8.0/");	
	}else{
		define('CKEDITOR_INNER_FOLDER',"ckeditor/8.1/");	
	}
	
}

function cke_initialize($data = array()) {

	$return = '';

	

	if(!defined('CI_CKEDITOR_HELPER_LOADED')) {
		define('CI_CKEDITOR_HELPER_LOADED', TRUE);

		$return =  '<script type="text/javascript" src="'.base_url().'apps/'.$data['path'] . '/ckeditor.js"></script>';

		$return .=	"<script type=\"text/javascript\">CKEDITOR_BASEPATH = '" . base_url().'apps/'.$data['path'] . "/';</script>";

	} 

	

	return $return;

	

}



/**

 * This function create JavaScript instances of CKEditor

 * @author Samuel Sanchez 

 * @access private

 * @param array $data (default: array())

 * @return string

 */

 function configurable($data = array()) {

	

		$return = "<script type=\"text/javascript\">

		CKEDITOR.config.toolbar =

		[

		[ 'Bold', 'Italic' ]

		];

		CKEDITOR.replace('page_desc', {width : '100%',height : '200px'});";

			

		$return .= '</script>';	

		

		return $return;

	

}



function cke_create_instance($data = array()) {

	

	$add='';

    $return = "<script type=\"text/javascript\">

     	CKEDITOR.replace('" . $data['id'] . "', {";

    

    		//Adding config values

				

				$array_config_key=array_keys($data['config']);

				$array_config_key_end=end($array_config_key);

				

    		if(isset($data['config'])) {

	    		foreach($data['config'] as $k=>$v) {

	    			//echo $k." $v<br>";

	    			// Support for extra config parameters

					if(($k=='toolbar' and $v=='specific') or $k=='toolbar_items')

					{$add='yes';continue;};

					

	    			if (is_array($v)) {

	    				$return .= $k . " : [";

	    				$return .= config_data($v);

	    				$return .= "]";

	    				

	    			}

	    			else {

	    				$return .= $k . " : '" . $v . "'";

	    			}

	    			if($k !== $array_config_key_end) {

							$return .= ",";

						}	

						  			

	    		} 

    		}

    

    $return .= '});';	

    if($add=='yes')

	{

		if(@$data['config']['toolbar_items']!='')

		$toolbar_items=$data['config']['toolbar_items'];

		else

		$toolbar_items="[ 'Bold', 'Italic' ]";

		$return .= "

		CKEDITOR.config.toolbar =

		[

			$toolbar_items

		];

		";



	};

	$return .= '</script>';	

    return $return;

	

}



/**

 * This function displays an instance of CKEditor inside a view

 * @author Samuel Sanchez 

 * @access public

 * @param array $data (default: array())

 * @return string

 */



function display_ckeditor($data = array(),$toolbar='')

{

	// Initialization

	

	

	$return = cke_initialize($data);

	

    // Creating a Ckeditor instance

	if($toolbar!='')

	{

		//

	}

    $return .= cke_create_instance($data);

	



    // Adding styles values

	/*$return .= "<script type=\"text/javascript\">";

	

	$return .= "CKEDITOR.config.uiColor ='#000'    ";

	

	$return .= "</script>";		*/



    return $return;

}



/**

 * config_data function.

 * This function look for extra config data

 *

 * @author ronan

 * @link http://kromack.com/developpement-php/codeigniter/ckeditor-helper-for-codeigniter/comment-page-5/#comment-545

 * @access public

 * @param array $data. (default: array())

 * @return String

 */

function config_data($data = array())

{

	$return = '';

	foreach ($data as $key)

	{

		if (is_array($key)) {

			$return .= "[";

			foreach ($key as $string) {

				$return .= "'" . $string . "'";

				if ($string != end(array_values($key))) $return .= ",";

			}

			$return .= "]";

		}

		else {

			$return .= "'".$key."'";

		}

		if ($key != end(array_values($data))) $return .= ",";



	}

	return $return;

}





if ( ! function_exists('set_ck_config'))

{ 

	function set_ck_config($config=array())

	{	

		if(!is_array($config) or empty($config))

		return false;

		

		$type			=	array_key_exists('type',$config)		?	$config['type']			:	'Full';

		$textarea_id	=	array_key_exists('textarea_id',$config)	?	$config['textarea_id']	:	'';

		$width			=	array_key_exists('width',$config)		?	$config['width']		:	"100%";

		$height			=	array_key_exists('height',$config)		?	$config['height']		:	'';

		$uiColor		=	array_key_exists('uiColor',$config)		?	$config['uiColor']		:	'';

		$toolbar		=	array_key_exists('toolbar',$config)		?	$config['toolbar']		:	"[ 'Bold', 'Italic' ],[ 'TextColor'],[ 'Font','FontSize' ]";



		if($height=='')

		$height='200px';

		$type=strtolower($type);

		

		if($uiColor=='')

		$uiColor="#E2E2E2";

		

		if($type=='basic')

		 {

				$arr  = array(					

				'id' 	  => 	$textarea_id,

				'path'	  =>	'../assets/developers/js/ckeditor/ckeditor',	

				

					'config' => array(

					'toolbar' 	=> 	"Basic", 	

					'width' 	=> 	"$width",	

					'height' 	=> 	"$height",

					'uiColor'	=>  "$uiColor",

					)			

			  );

			

			

		}

		else if(strtolower($type)=='specific')

		{

				$arr  = array(					

				'id' 	  => 	$textarea_id,

				'path'	  =>	'../assets/developers/js/ckeditor/ckeditor',	

				

					'config' => array(

					'toolbar' 			=> 	"specific", 	

					'toolbar_items' 	=> 	$toolbar, 	

					'uiColor'			=>  "$uiColor",

					'width' 			=> 	"$width",	

					'height' 			=> 	"$height",

					)			

			  );

		}

		else

		{

				if($height=='')

				$height='400px';

	

				$arr = array(	'id' 	=> 	$textarea_id,

				'path'	    =>	'../assets/developers/js/ckeditor/ckeditor',			

				'config'    => array(

				'toolbar' 	=> 	"Full", 

				'width' 	=> 	"$width",	

				'height' 	=> 	"$height",

				'uiColor'			=>  "$uiColor",

				'filebrowserBrowseUrl'	    => base_url().'assets/developers/js/'.CKEDITOR_INNER_FOLDER.'ckfinder/ckfinder.html',

				'filebrowserImageBrowseUrl' => base_url().'assets/developers/js/'.CKEDITOR_INNER_FOLDER.'ckfinder/ckfinder.html?Type=Images',

				'filebrowserFlashBrowseUrl' => base_url().'assets/developers/js/'.CKEDITOR_INNER_FOLDER.'ckfinder/ckfinder.html?Type=Flash',

				

				'filebrowserUploadUrl' => base_url().'assets/developers/js/'.CKEDITOR_INNER_FOLDER.'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',

				

				'filebrowserImageUploadUrl' => base_url().'assets/developers/js/'.CKEDITOR_INNER_FOLDER.'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',

				

				'filebrowserFlashUploadUrl' => base_url().'assets/developers/js/'.CKEDITOR_INNER_FOLDER.'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash' 

				)

				

			);

		

		}

	return $arr;

	}

}

/*



toolbar options



{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },

	{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },

	{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },

	{ name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 

        'HiddenField' ] },

	'/',

	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },

	{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',

	'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },

	{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },

	{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },

	'/',

	{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },

	{ name: 'colors', items : [ 'TextColor','BGColor' ] },

	{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }

*/