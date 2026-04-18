<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DefaultController extends CI_Controller

{



 public function __construct()

{

	parent::__construct();

}

		

 public function landingpage()

 {

	 

 echo modules::run("home");

 }

} 