<?php

defined('BASEPATH') OR exit('No direct script access allowed');



/*

| -------------------------------------------------------------------------

| URI ROUTING

| -------------------------------------------------------------------------

| This file lets you re-map URI requests to specific controller functions.

|

| Typically there is a one-to-one relationship between a URL string

| and its corresponding controller class/method. The segments in a

| URL normally follow this pattern:

|

|	example.com/class/method/id/

|

| In some instances, however, you may want to remap this relationship

| so that a different class/function is called than the one

| corresponding to the URL.

|

| Please see the user guide for complete details:

|

|	http://codeigniter.com/user_guide/general/routing.html

|

| -------------------------------------------------------------------------

| RESERVED ROUTES

| -------------------------------------------------------------------------

|

| There are three reserved routes:

|

|	$route['default_controller'] = 'welcome';

|

| This route indicates which controller class should be loaded if the

| URI contains no data. In the above example, the "welcome" class

| would be loaded.

|

|	$route['404_override'] = 'errors/page_missing';

|

| This route will tell the Router which controller/method to use if those

| provided in the URL cannot be matched to a valid route.

|

|	$route['translate_uri_dashes'] = FALSE;

|

| This is not exactly a route, but allows you to automatically route

| controller and method names that contain dashes. '-' isn't a valid

| class or method name character, so it requires translation.

| When you set this option to TRUE, it will replace ALL dashes in the

| controller and method URI segments.

|

| Examples:	my-controller/index	-> my_controller/index

|		my-controller/my-method	-> my_controller/my_method

*/



//$route['sitepanel/(:any)']		= "sitepanel/$1";

$handle = opendir(FCPATH.'modules');

if ($handle)

{



	while ( false !== ($module = readdir($handle)) )



	{



		// make sure we don't map silly dirs like .svn, or . or ..



		



		if (substr($module, 0, 1) != ".")



		{

			



			if ( file_exists(FCPATH.'modules/'.$module.'/'.$module.'_routes.php') )



			{



				//echo FCPATH.'modules/'.$module.'/'.$module.'_routes.php'."<br>";



				//require_once(FCPATH.'modules/'.$module.'/'.$module.'_routes.php');



			}



				



		}



	}



}

// $route['default_controller'] = "home";

$route['default_controller'] 	= 'user/login';
$route['404_override']			= 'pages/error_404';
$route['seller-register'] 		= 'users/seller_register';
$route['logout'] 				= 'user/logout';
$route['forgot-password'] 		= 'user/forgotten_password';
$route['register'] 				= 'user/register';
$route['login'] 				= 'user/login';
$route['login-place-order'] 	= 'user/login_place_order';
$route['advanced-search'] 		= 'tour_packages/advanced_search';

////// End CMS Page Routing ///////
$route['translate_uri_dashes'] = FALSE;
