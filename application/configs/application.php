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

/**
 * application.php
 *
 * application configuration
 *
 * @package	    	phpHyppo
 * @subpackage		Application Configuration
 * @author			Muhammad Hamizi Jaminan
 */

/**
 * Base URL
 *
 * Application URL without trailing slash
 *
 * @access public
 * @value string
 */
$config['base_url'] = 'http://localhost/phphyppo';

/**
 * Error Debug
 *
 * Output for error debugging, development mode is 0 and production
 * mode is 1. Default value is 0
 *
 * @access public
 * @value integer
 */
$config['error_debug'] = 0;


/**
 * URI Protocol
 *
 * Variable name of server global to request URI.
 * Available value for this uri protocol is REQUEST_URI, PATH_INFO or ORIG_PATH_INFO
 *
 * @access public
 * @value string
 */
$config['uri_protocol'] = 'REQUEST_URI';


/**
 * Encryption Key
 *
 * The key for encryption library for encoding and decoding purpose
 * and also can be use as your salt
 *
 * @access public
 * @value string
 */
$config['encryption_key'] = 'dffcf53f4a40dcf270b621a631ec7117';


/**
 * Cache Engine
 *
 * To enable APC cache engine, set the enable_cache value 
 * in seconds and set false to disable APC cache engine
 *
 * @access public
 * @value integer
 */
 $config['enable_cache'] = false;


/**
 * User application configuration
 *
 * Please add your application vars configuration or constant
 * under this section
 */
