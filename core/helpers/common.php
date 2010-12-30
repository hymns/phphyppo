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
 * @since			Version 8.06
 */

/* no direct access */
if (!defined('BASEDIR'))
	exit;

/**
 * Common Function
 *
 * @package		phpHyppo
 * @subpackage	Core User Defined Function
 * @author			Muhammad Hamizi Jaminan
 */

/**
 * CustomException
 *
 * the internal error handler class callback function
 *
 * @access public
 */
class CustomException extends Exception 
{ 
	public static function errorHandlerCallback($error_code, $error_msg, $error_file, $error_line, $context) 
	{
		$exception = new self($error_msg, $error_code);
		$exception->line = $error_line;
		$exception->file = $error_file;
		throw $exception;
	}
} 

/**
 * custom_exception_handler
 *
 * the internal exception handler function
 *
 * @access public
 */
if (!function_exists('custom_exception_handler'))
{
	function custom_exception_handler($exception)
	{
		// production mode
		if (CONF_ERROR_DEBUG)
		{
			include_once BASEDIR . 'core' . DS . 'views' . DS . 'error_splash.php';
			exit(1);
		}
		
		// development mode
		else
		{
			// capture exception error
			$error_code 	= $exception->getCode();
			$error_file 		= $exception->getFile();
			$error_line 		= $exception->getLine();
			$error_msg 	= $exception->getMessage();
			
			// development error code
			switch($error_code)
			{
				//--------------------------------------------------------
				//       controller error 
				//--------------------------------------------------------
				
				// controller file missing
				case 100:
					$filename = 'controller';
					$controller = $error_msg;
				break;
				
				// controller class missing
				case 101:
					$filename = 'controller';
					$controller = $error_msg;
				break;
				
				// controller method missing
				case 102:
					$filename = 'function';
					$tmp = explode(':', $error_msg);
					$controller =  $tmp[0];
					$method = $tmp[1];
				break;
				
				//--------------------------------------------------------
				//       model error 
				//--------------------------------------------------------				
				
				// pdo not available
				case 200:
					$filename = 'database';
				break;
				
				// database setting not load
				case 201:
					$filename = 'database';
				break;
				
				// pdo connection error
				case 202:
					$filename = 'database';
				break;
				
				// model plugin file missing
				case 203:
					$filename = 'plugin';
					$plugin = $error_msg;
				break;
				
				// model plugin class missing
				case 204:
					$filename = 'plugin';
					$plugin = $error_msg;
				break;
				
				// model name empty
				case 205:
					$filename = 'model';
					$model = $error_msg;
				break;
				
				// model alias name error format
				case 206:
					$filename = 'debug';
				break; 
				
				// model alias reserved 
				case 207:
					$filename = 'debug';
				break;
				
				// model file missing
				case 208:
					$filename = 'model';
					$model = $error_msg;
				break;
				
				// model class missing
				case 209:
					$filename = 'model';
					$model = $error_msg;
				break;
				
				// tablename missing
				case 210:
					$filename = 'model';
				break;
				
				// column missing
				case 211:
					$filename = 'model';
				break;
				
				// where clause missing
				case 212:
					$filename = 'model';
				break;
				
				// where clause value not match
				case 213:
					$filename = 'model';
				break;
				
				// where between input range
				case 214:
					$filename = 'model';
				break;
				
				// where between same value
				case 215:
					$filename = 'model';
				break;
				
				// query string error
				case 216:
					$filename = 'sql';
				break;
				
				//--------------------------------------------------------
				//       viewer error 
				//--------------------------------------------------------
				
				// viewer file missing
				case 300:
					$filename = 'view';
				break;
				
				//--------------------------------------------------------
				//       library error 
				//--------------------------------------------------------
				
				// library name missing
				case 400:
					$filename = 'library';
					$library = $error_msg;
				break;
				
				// library name invalid format
				case 401:
					$filename = 'debug';
				break;
				
				// library alias reserved
				case 402:
					$filename = 'debug';
				break;
				
				// library file missing
				case 403:
					$filename = 'library';
					$library = $error_msg;
				break;
				
				// library class not exists
				case 404:
					$filename = 'library';
					$library = $error_msg;
				break;
				
				//--------------------------------------------------------
				//       helper error 
				//--------------------------------------------------------
				
				// helper filename invalid format
				case 500:
					$filename = 'debug';
				break;
				
				// helper file missing
				case 501:
					$filename = 'debug';
				break;
				
				//--------------------------------------------------------
				//       programming error 
				//--------------------------------------------------------
				default:
					$filename = 'debug';
				break;
			}
			
			// show error
			include_once BASEDIR . 'core' . DS . 'views' . DS . 'error_'. $filename .'.php';			
		}
		
		// don't execute PHP internal error handler
		return true;
	}
}

/**
 * cache_flush
 *
 * delete all cache from system
 *
 * @access public
 * @return bool
 */
if (!function_exists('cache_flush'))
{
	function cache_flush()
	{
		// flush all cache
		return apc_clear_cache('user');
	}
}

/**
 * cache_read
 *
 * read cache from system
 *
 * @access public
 * @params string $key
 * @return mixed
 */
if (!function_exists('cache_read'))
{
	function cache_read()
	{
        // get cache
		$cache = apc_fetch(md5(CONTROLLER_NAME . CONTROLLER_EVENT));
		
		// check the existing cache
		if ($cache !== false)
		{
			// print out cache
			echo $cache;
			exit;
		}
	}
}


/**
 * cache_write
 *
 * write cache data to system
 *
 * @access public
 * @params string $key
 * @params bool $value
 * @params int $duration
 * @return none
 */
if (!function_exists('cache_write'))
{
	function cache_write($value, $duration=300)
	{
		// no submission cache
		if ($_POST)
			return false;
		
		// save cache to memory
		return apc_store(md5(CONTROLLER_NAME . CONTROLLER_EVENT), $value, $duration);
	}
}


/**
 * redirect
 *
 * forward current page to routing page
 *
 * @access public
 * @params string $url
 * @params bool $local
 * @return none
 */
if (!function_exists('redirect'))
{
	function redirect($url, $local=true)
	{
		// check redirect url
		if (empty($url))
			return false;
		
        // define target
        $url = ($local) ? CONF_BASE_URL . $url : $url;
		
		// forward user location address
	    header("Location: " . $url);		
		exit;
	}
}

/**
 * strip_slashes
 *
 * clean up slashes
 *
 * @access public
 * @params mixed $str
 * @return mixed
 */
if (!function_exists('strip_slashes'))
{
	function strip_slashes($str)
	{
		if (is_array($str))
			foreach ($str as $key => $val)
				$str[$key] = strip_slashes($val);
		else
			$str = stripslashes($str);
		
		return $str;
	}
}

/**
 * randomizer
 *
 * make random character
 *
 * @access public
 * @params integer $max
 * @params string $type
 * @return string
 */
if (!function_exists('randomizer'))
{
	function randomizer($max=10, $type = 'both')
	{
		// key list
		switch($type)
		{
			case 'int':
			$key = array(0,1,2,3,4,5,6,7,8,9);
			break;
			
			case 'chr':
			$key = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
			break;
			
			case 'both':
			$key = array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');	
			break;
		}
		
		// default data
		$result = '';
		
		// generate key
		for($i=1; $i<=$max; $i++)
		{
			$random  = rand(0, count($key)-1);
			$result .= $key[$random];
		}
		
		// re-check string lens
		if (strlen($result) != $max)
			$result = randomizer($max, $type);
			
		// return data
		return $result;
	}
}

/*
 * Auto defined config variables
 */
foreach($config as $key => $val)
	define('CONF_' . strtoupper($key), $val);

/* Defined base path - Jasdy Syarman */
define('CONF_BASE_PATH', str_replace($_SERVER['DOCUMENT_ROOT'], NULL, dirname($_SERVER['SCRIPT_FILENAME'])));

/* End of common.php */
/* Location: core/helpers/common.php */
?>