<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {

    public function __construct()
    {
        parent::__construct(); // Ensure CI_Exceptions constructor is called
    }

    // Override any functions if needed
    public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        // Custom error handling logic
        return parent::show_error($heading, $message, $template, $status_code);
    }
}