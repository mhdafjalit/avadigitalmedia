<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * CodeIgniter index.php - Updated for PHP 8+
 */

// APPLICATION ENVIRONMENT
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

// PHP SETTINGS
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '60M');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '512M');

// ERROR REPORTING
switch (ENVIRONMENT) {
    case 'development':
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        break;

    case 'testing':
    case 'production':
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

// SYSTEM FOLDER
$system_path = 'codeigniter';

// APPLICATION FOLDER
$application_folder = 'apps';

// VIEW FOLDER
$view_folder = '';

// Resolve the system path
if (defined('STDIN')) {
    chdir(dirname(__FILE__));
}

if (($_temp = realpath($system_path)) !== FALSE) {
    $system_path = $_temp . '/';
} else {
    $system_path = rtrim($system_path, '/') . '/';
}

if (!is_dir($system_path)) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your system folder path does not appear to be set correctly: ' . pathinfo(__FILE__, PATHINFO_BASENAME);
    exit(3);
}

// MAIN PATH CONSTANTS
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', str_replace('\\', '/', $system_path));
define('FCPATH', dirname(__FILE__) . '/');
define('FCROOT', str_replace("\\", '/', FCPATH));

define('UPLOAD_DIR', FCROOT . "uploaded_files");
define('IMG_CACH_DIR', UPLOAD_DIR . "/thumb_cache");

define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

// APPLICATION PATH
if (is_dir($application_folder)) {

    if (($_temp = realpath($application_folder)) !== FALSE) {
        $application_folder = $_temp;
    }

    define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);

} else {

    if (!is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR)) {
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your application folder path does not appear to be set correctly: ' . SELF;
        exit(3);
    }

    define('APPPATH', BASEPATH . $application_folder . DIRECTORY_SEPARATOR);
}

// VIEW PATH
if (!is_dir($view_folder)) {

    if (!empty($view_folder) && is_dir(APPPATH . $view_folder . DIRECTORY_SEPARATOR)) {

        $view_folder = APPPATH . $view_folder;

    } elseif (!is_dir(APPPATH . 'views' . DIRECTORY_SEPARATOR)) {

        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your view folder path does not appear to be set correctly: ' . SELF;
        exit(3);

    } else {

        $view_folder = APPPATH . 'views';
    }
}

if (($_temp = realpath($view_folder)) !== FALSE) {

    $view_folder = $_temp . DIRECTORY_SEPARATOR;

} else {

    $view_folder = rtrim($view_folder, '/\\') . DIRECTORY_SEPARATOR;
}

define('VIEWPATH', $view_folder);

// LOAD CODEIGNITER BOOTSTRAP
require_once BASEPATH . 'core/CodeIgniter.php';