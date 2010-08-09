<?php
/**
 * phpHyppo
 *
 * An open source MVC application framework for PHP 5.1+
 *
 * @package		phpHyppo
 * @author			Muhammad Hamizi Jaminan, hymns [at] time [dot] net [dot] my
 * @copyright		Copyright (c) 2008 - 2010, Green Apple Software.
 * @license			LGPL, see included license file
 * @link				http://www.phphyppo.com
 * @since			Version 8.02
 */

/* PHP error reporting level, if different from server default */
error_reporting(E_ALL);

/* Uncomment this when you get blank page on development mode */
ini_set('display_errors', true); 

/* if the /core/ directory is not inside the root directory, uncomment and set here */
define('BASEDIR', '../');

/* if the /application/ directory is not inside the root directory, uncomment and set here */
define('APPDIR', './application/');

/* directory separator constant */
if (!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);

/* set the default base directory constant */
if (!defined('BASEDIR'))
	define('BASEDIR', dirname(__FILE__) . DS);

/* set the default application directory constant */
if (!defined('APPDIR'))
	define('APPDIR', BASEDIR . 'application' . DS);

/* call the framework bootable engine */
require_once BASEDIR . 'core' . DS . 'bootstrap.php';

/* End of index.php */
/* Location: index.php */
?>