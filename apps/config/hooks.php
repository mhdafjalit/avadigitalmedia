<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['post_controller_constructor'][] = array(
								'class'		=> 'App_hooks',
								'function'	=> 'check_site_status',
								'filename'	=> 'App_hooks.php',
								'filepath'	=> 'hooks',
								'params'	=> ''
							);

$hook['post_controller_constructor'][] = array(
    'class' => 'Language_Loader',
    'function' => 'initialize_language',
    'filename' => 'Language_Loader.php',
    'filepath' => 'hooks'
);

/*$hook['post_controller_constructor'][] = array(
								'class'		=> 'Subadmin_hooks',
								'function'	=> 'check_privileges',
								'filename'	=> 'Subadmin_hooks.php',
								'filepath'	=> 'hooks',
								'params'	=> ''
							);*/
