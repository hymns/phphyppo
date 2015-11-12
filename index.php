<?php
/**
 * phpHyppo
 *
 * An open source MVC application framework for PHP 5.1+
 *
 * @package			phpHyppo
 * @author			Muhammad Hamizi Jaminan <hymns@time.net.my>
 * @copyright		Copyright (c) 2008 - 2014, Green Apple Software.
 * @license			LGPL, see included license file
 * @link			http://www.phphyppo.org
 * @since			Version 8.02
 */

/* Uncomment this when you get blank page on development mode */
ini_set('display_errors', true); 

/* PHP error reporting level, if different from server default */
error_reporting(E_ALL);

/* If you want set default timezone, uncomment and set here */
//date_default_timezone_set('Asia/Kuala_Lumpur');

/* if the /core/ directory is not inside the root directory, uncomment and set here */
//define('BASEDIR', '/path/to/phphyppo/');

/* if the /application/ directory is not inside the root directory, uncomment and set here */
//define('APPDIR', '/path/to/application/');

/* directory separator constant */
if ( ! defined('DS') )
    define('DS', DIRECTORY_SEPARATOR);

/* set the default base directory constant */
if ( ! defined('BASEDIR') )
    define('BASEDIR', dirname(__FILE__) . DS);

/* set the default application directory constant */
if ( ! defined('APPDIR') )
    define('APPDIR', BASEDIR . 'application' . DS);

/* call the framework bootable engine */
require_once BASEDIR . 'core' . DS . 'bootstrap.php';

/* End of index.php */
/* Location: index.php */