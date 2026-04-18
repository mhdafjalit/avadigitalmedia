<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_account extends Private_Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        // Redirect to members page
        redirect('members');
    }
}