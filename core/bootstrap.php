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

/* no direct access */
if ( ! defined('BASEDIR') )
	exit;

/**
 * bootstrap.php
 *
 * framework bootable / startup engine
 *
 * @package			phpHyppo
 * @subpackage		Front Controller
 * @author			Muhammad Hamizi Jaminan
 */

// define framework version
if ( ! defined('VERSION') )
	define('VERSION', '15.11');

/*
 * ------------------------------------------------------
 *  Load the application configuration
 * ------------------------------------------------------
 */
include_once( APPDIR . 'configs' . DS . 'application.php' );

/*
 * ------------------------------------------------------
 *  Load the application autoload configuration
 * ------------------------------------------------------
 */
include_once( APPDIR . 'configs' . DS . 'autoload.php' );

/*
 * ------------------------------------------------------
 *  Load the application routing configuration
 * ------------------------------------------------------
 */
include_once( APPDIR . 'configs' . DS . 'routes.php' );

/*
 * ------------------------------------------------------
 *  Load the registry class
 * ------------------------------------------------------
 */
include_once( BASEDIR . 'core' . DS . 'classes' . DS . 'AppRegistry.php' );

/*
 * ------------------------------------------------------
 *  Load the core user defined functions
 * ------------------------------------------------------
 */
include_once( BASEDIR . 'core' . DS . 'helpers' . DS . 'common.php' );

/*
 * ------------------------------------------------------
 *  Define a custom error handler
 * ------------------------------------------------------
 */
set_error_handler( array( 'CustomException', 'errorHandlerCallback' ) );
set_exception_handler( 'custom_exception_handler' );

/**
 *  __autoload
 *
 * PHP magic function to easy load the core classes
 *
 * @access	public
 * @param	string $classname
 * @return	boolean
 */
function __autoload( $classname )
{
	// core & controller data
	$core_class = array( 'AppController', 'AppLoader', 'AppModel', 'AppViewer' );
	$controller = str_replace( array( '_Controller', '_controller' ), NULL, $classname );
	
	// path to core file	
	if ( in_array( $classname, $core_class ) )
		$filepath = BASEDIR . 'core' . DS . 'classes' . DS . $classname . '.php';
		
	// path to controller file
	else
		$filepath = APPDIR . 'controllers' . DS . $controller . '.php';	

	// load the core class file
	if ( file_exists( $filepath ) )
		return require_once( $filepath );

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
$uri_segment = !empty( $_SERVER[CONF_URI_PROTOCOL] ) ? explode( '/', $_SERVER[CONF_URI_PROTOCOL] ) : null;

// get controller & method
list( , $controller_name, $controller_event) = $uri_segment;

// grab controller name
$controller_name = !empty( $controller_name ) ? preg_replace( '!\W!', '', $controller_name ) : $route['default_controller'];

// grab controller method
$controller_event = !empty( $controller_event ) ? preg_replace( '!\W!', '', $controller_event ) : 'index';

// grab user params
$controller_params = array_slice($uri_segment, 3);

/*
 * ------------------------------------------------------
 *  Routing (mapping) the controller
 * ------------------------------------------------------
 */
if ( ! empty( $route[$controller_name] ) )
{
	// extract routing (mapping)
	$route_segment = explode( '/', $route[$controller_name] );

	// get controller & method
	list( , $route_controller_name, $route_controller_event) = $route_segment;

	// re-map to original controller & event
	$controller_name = !empty( $route_controller_name ) ? $route_controller_name : $controller_name;
	$controller_event = !empty( $route_controller_event ) ? $route_controller_event : $controller_event;

	// re-map user params
	$controller_params = array_slice($uri_segment, 2);	
}

// define controller & method widely
define( 'CONTROLLER_NAME', $controller_name );
define( 'CONTROLLER_EVENT', $controller_event );
define( 'URI_SEGMENT', !empty( $_SERVER[CONF_URI_PROTOCOL] ) ? $_SERVER[CONF_URI_PROTOCOL] : null );

/*
 * ------------------------------------------------------
 *  Load the application controller
 * ------------------------------------------------------
 */

// controller class name
$controller_class = $controller_name . '_Controller';

// validate the controller is loaded
if ( ! class_exists( $controller_class ) )
    throw new Exception( $controller_name, 101 );

/*
 * ------------------------------------------------------
 *  Instantiate the registry on controller
 * ------------------------------------------------------
 */
$registry = new $controller_class( true );


// validate controller event is exists
if ( ! is_callable( array( $registry, $controller_event ) ) )
    throw new Exception( $controller_name . ':' . $controller_event, 102 );


/*
 * ------------------------------------------------------
 *  Is there a "auto load" helper?
 * ------------------------------------------------------
 */
if ( ! empty( $autoload['helpers'] ) )
{
	foreach( $autoload['helpers'] as $helper )
		$registry->load->helper( $helper );
}


/*
 * ------------------------------------------------------
 *  Is there a "auto load" library?
 * ------------------------------------------------------
 */
if ( ! empty( $autoload['libraries'] ) )
{
	foreach( $autoload['libraries'] as $library )
	{
		if ( is_array( $library ) )
			$registry->load->library( $library[0], $library[1] );
		else
			$registry->load->library( $library );
	}
}


/*
 * ------------------------------------------------------
 *  Is there a "auto load" model?
 * ------------------------------------------------------
 */
if ( ! empty($autoload['models']) )
{
	foreach( $autoload['models'] as $model )
	{
		if ( is_array( $model ) )
			$registry->load->model( $model[0], $model[1] );
		else
			$registry->load->model( $model );
	}
}


/*
 * ------------------------------------------------------
 *  Is there a "beforeFilter" function?
 * ------------------------------------------------------
 */
if ( is_callable( array( $registry, 'beforeFilter' ) ) )
	call_user_func( array( $registry, 'beforeFilter' ) );


/*
 * ------------------------------------------------------
 *  Instantiate the controller and call requested event
 * ------------------------------------------------------
 */
call_user_func_array( array( $registry, $controller_event ), $controller_params );


/*
 * ------------------------------------------------------
 *  Is there a "afterFilter" function?
 * ------------------------------------------------------
 */
if ( is_callable( array( $registry, 'afterFilter' ) ) )
	call_user_func( array( $registry, 'afterFilter' ) );


/* End of bootstrap.php */
/* Location: core/bootstrap.php */