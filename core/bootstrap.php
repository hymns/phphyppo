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

/* no direct access */
if (!defined('BASEDIR'))
	exit;

/**
 * bootstrap.php
 *
 * framework bootable / startup engine
 *
 * @package		phpHyppo
 * @subpackage	Front Controller
 * @author			Muhammad Hamizi Jaminan
 */

// define framework version
if (!defined('VERSION'))
	define('VERSION', '10.07');

/*
 * ------------------------------------------------------
 *  Load the application configuration
 * ------------------------------------------------------
 */
include_once(APPDIR . 'configs' . DS . 'application.php');

/*
 * ------------------------------------------------------
 *  Load the application autoload configuration
 * ------------------------------------------------------
 */
include_once(APPDIR . 'configs' . DS . 'autoload.php');

/*
 * ------------------------------------------------------
 *  Load the application routing configuration
 * ------------------------------------------------------
 */
include_once(APPDIR . 'configs' . DS . 'routes.php');

/*
 * ------------------------------------------------------
 *  Load the registry class
 * ------------------------------------------------------
 */
include_once(BASEDIR . 'core' . DS . 'classes' . DS . 'AppRegistry.php');

/*
 * ------------------------------------------------------
 *  Load the core user defined functions
 * ------------------------------------------------------
 */
include_once(BASEDIR . 'core' . DS . 'helpers' . DS . 'common.php');

/*
 * ------------------------------------------------------
 *  Define a custom error handler
 * ------------------------------------------------------
 */
set_error_handler('custom_error_handler');
set_exception_handler('custom_exception_handler');

/**
 *  __autoload
 *
 * PHP magic function to easy load the core classes
 *
 * @access	public
 * @param	string $classname
 * @return	boolean
 */
function __autoload($classname)
{
	// path to core class file
    $filepath = BASEDIR . 'core' . DS . 'classes' . DS . $classname . '.php';

	// load the core class file
	if (file_exists($filepath))
		return require_once($filepath);

	// undefined core class path
	return false;
}


/*
 * ------------------------------------------------------
 *  URL path info processor
 * ------------------------------------------------------
 *
 * Grab url segment, split the controller & method name
 *
 */
$uri_segment = !empty($_SERVER[CONF_URI_PROTOCOL]) ? explode('/', $_SERVER[CONF_URI_PROTOCOL]) : null;

// grab controller name
$controller_name = !empty($uri_segment[1]) ? preg_replace('!\W!', '', $uri_segment[1]) : $route['default_controller'];

// grab controller method
$controller_event = !empty($uri_segment[2]) ? $uri_segment[2] : 'index';


/*
 * ------------------------------------------------------
 *  Routing (mapping) the controller
 * ------------------------------------------------------
 */
if (!empty($route[$controller_name]))
{
	// extract routing (mapping)
	$route_segment = explode('/', $route[$controller_name]);

	// re-map to original controller & event
	$controller_name = !empty($route_segment[1]) ? $route_segment[1] : $controller_name;
	$controller_event = !empty($route_segment[2]) ? $route_segment[2] : $controller_event;
}

// define controller & method widely
define('CONTROLLER_NAME', $controller_name);
define('CONTROLLER_EVENT', $controller_event);

// tidy up resource
unset($config, $route, $route_segment, $uri_segment);

/*
 * ------------------------------------------------------
 *  Load the application controller
 * ------------------------------------------------------
 */

// path to controller filename
$controller_path = APPDIR . 'controllers' . DS . $controller_name . '.php';

// validate the controller path
if (!file_exists($controller_path))
    throw new Exception($controller_name, 100);

// load the controller file
include_once($controller_path);

// controller class name
$controller_class = $controller_name . '_Controller';

// validate the controller is loaded
if (!class_exists($controller_class))
    throw new Exception($controller_name, 101);

/*
 * ------------------------------------------------------
 *  Instantiate the registry as controller
 * ------------------------------------------------------
 */
$registry = new $controller_class(true);


// validate controller event is exists
if (!is_callable(array($registry, $controller_event)))
    throw new Exception($controller_name . ':' . $controller_event, 102);


/*
 * ------------------------------------------------------
 *  Is there a "auto load" helper?
 * ------------------------------------------------------
 */
if (!empty($autoload['helpers']))
{
	foreach($autoload['helpers'] as $helper)
		$registry->load->helper($helper);
}


/*
 * ------------------------------------------------------
 *  Is there a "auto load" library?
 * ------------------------------------------------------
 */
if (!empty($autoload['libraries']))
{
	foreach($autoload['libraries'] as $library)
	{
		if (is_array($library))
			$registry->load->library($library[0], $library[1]);
		else
			$registry->load->library($library);
	}
}


/*
 * ------------------------------------------------------
 *  Is there a "auto load" model?
 * ------------------------------------------------------
 */
if (!empty($autoload['models']))
{
	foreach($autoload['models'] as $model)
	{
		if (is_array($model))
			$registry->load->model($model[0], $model[1]);
		else
			$registry->load->model($model);
	}
}


/*
 * ------------------------------------------------------
 *  Is there a "beforeFilter" function?
 * ------------------------------------------------------
 */
if (is_callable(array($registry, 'beforeFilter')))
	$registry->beforeFilter();


/*
 * ------------------------------------------------------
 *  Instantiate the controller and call requested event
 * ------------------------------------------------------
 */
$registry->$controller_event();


/*
 * ------------------------------------------------------
 *  Is there a "afterFilter" function?
 * ------------------------------------------------------
 */
if (is_callable(array($registry, 'afterFilter')))
	$registry->afterFilter();


/* End of bootstrap.php */
/* Location: core/bootstrap.php */
?>
