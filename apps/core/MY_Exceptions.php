<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

#[AllowDynamicProperties]
class MY_Exceptions extends CI_Exceptions
{
    public function __construct()
    {
        parent::__construct();
        log_message('debug', 'MY_Exceptions Class Initialized');
    }
    
    public function show_404($page = '', $log_error = TRUE)
    {
        $CI =& get_instance();
        
        // Try to get config from CI instance first
        $base_url = '';
        $error_uri = '';
        
        if (isset($CI->config)) {
            $base_url = $CI->config->item('base_url');
            $error_uri = $CI->config->item('url_404');
        }
        
        // Fallback to get_config()
        if (empty($base_url) || empty($error_uri)) {
            $config =& get_config();
            if (empty($base_url) && isset($config['base_url'])) {
                $base_url = $config['base_url'];
            }
            if (empty($error_uri) && isset($config['url_404'])) {
                $error_uri = $config['url_404'];
            }
        }
        
        // Log the 404 error
        if ($log_error) {
            log_message('error', '404 Page Not Found --> ' . $page);
            log_message('error', 'Request URI: ' . $_SERVER['REQUEST_URI']);
        }
        
        // If no custom 404 URL is set, show default 404
        if (empty($error_uri)) {
            echo $this->show_error('404', 'The page you requested was not found.', 'error_404', 404);
            exit;
        }
        
        // Handle custom 404 redirect with cURL
        $full_url = rtrim($base_url, '/') . '/' . ltrim($error_uri, '/');
        
        // Use cURL to fetch the custom 404 page
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        // Pass session cookie if available
        if (session_id()) {
            curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . session_id());
        }
        
        // Execute request
        $content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch) || $http_code >= 400) {
            log_message('error', 'Failed to load custom 404 page: ' . curl_error($ch));
            echo $this->show_error('404', 'The page you requested was not found.', 'error_404', 404);
        } else {
            // Set proper 404 header
            header("HTTP/1.0 404 Not Found");
            echo $content;
        }
        
        curl_close($ch);
        exit;
    }
}