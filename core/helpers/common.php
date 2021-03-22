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
 * @since			Version 8.06
 */

/* no direct access */
if ( ! defined('BASEDIR') )
	exit;

/**
 * Common Function
 *
 * @package			phpHyppo
 * @subpackage		Core User Defined Function
 * @author			Muhammad Hamizi Jaminan
 */

class ErrorsAreAwesome
{
    private $errorConstants = array(
        1       => 'Error',
        2       => 'Warning',
        4       => 'Parse error',
        8       => 'Notice',
        16      => 'Core Error',
        32      => 'Core Warning',
        256     => 'User Error',
        512     => 'User Warning',
        1024    => 'User Notice',
        2048    => 'Strict',
        4096    => 'Recoverable Error',
        8192    => 'Deprecated',
        16384   => 'User Deprecated',
        32767   => 'All'
    );

    public function __construct()
    {
        set_error_handler(array($this, 'errorHandler'));
        set_exception_handler(array($this, 'exceptionHandler'));
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $errString = (array_key_exists($errno, $this->errorConstants)) ? $this->errorConstants[$errno] : $errno;

        //echo 'HYPPO ' . $errString.': '.$errstr;
        //error_log($errString.' ['.$errno.']: '.$errstr.' in '.$errfile.' on line '.$errline);
        $d = file($errfile);
        $a = "<?php\n\n";
        for ($i=$errline-10; $i < $errline+10 ; $i++) 
        { 
        	$a .= $i . ')' . $d[$i];
        }

        echo xhtml_highlight($a . "\n?>");exit;
    }

    public function exceptionHandler($exception)
    {
        $message = $exception->getMessage().' [code: '.$exception->getCode().'] trace: ' . $exception->getTraceAsString();
        echo $message;
    }    
}

//$e = new ErrorsAreAwesome();

function xhtml_highlight($str) {
    $str = highlight_string($str, true);
    $str = str_replace(array('<font ', '</font>'), array('<span ', '</span>'), $str);
    return preg_replace('#color="(.*?)"#', 'style="color: \\1"', $str);
}

/**
 * CustomException
 *
 * the internal error handler class callback function
 *
 * @access public
 */
class CustomException extends Exception 
{ 
	public static function errorHandlerCallback( $error_code, $error_msg, $error_file, $error_line, $context ) 
	{	
		//echo 'Message: ' . $error_msg . ' Line: ' . $error_line . ' Filename: ' . $error_file;	

		if ( ! preg_match('/AUTH=PLAIN/', $error_msg) AND ! preg_match('/token/', $error_msg) AND ! preg_match('/ldap_bind/', $error_msg) )
		{			
			$exception = new self( $error_msg, $error_code );
			$exception->line = $error_line;
			$exception->file = $error_file;
			custom_exception_handler( $exception );
		}
		
		return true;
	}

} 

/**
 * custom_exception_handler
 *
 * the internal exception handler function
 *
 * @access public
 */
if ( ! function_exists( 'custom_exception_handler' ) )
{
	function custom_exception_handler( $exception )
	{
		// server protocol
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

		// production mode
		if ( CONF_ERROR_DEBUG )
		{
			if ( ! headers_sent() )
			{
				header( $protocol . ' Internal Server Error' );
				header( 'X-Powered-By: phpHyppo/' . VERSION );			
			}
			
			include_once dirname(dirname(__FILE__)) . DS . 'views' . DS . 'error_splash.php';

			return true;
		}

		// development mode
		else
		{
			// capture exception error
			$error_code 	= $exception->getCode();
			$error_file 	= $exception->getFile();
			$error_line 	= $exception->getLine();
			$error_msg 		= $exception->getMessage();
			$error_trace    = $exception->getTraceAsString();
			
			// send header error
			if ( ! headers_sent() )
			{
				header( $protocol . ' ' . in_array( $error_code, array( 100, 101, 203, 205, 208, 209, 300, 400, 403, 501 ) ) ? '404 Not Found' : '500 Internal Server Error' );
				header( 'X-Powered-By: phpHyppo/' . VERSION );			
			}
			
			// development error code
			switch( $error_code )
			{
				//--------------------------------------------------------
				//       controller error
				//--------------------------------------------------------

				// controller file / class missing
				case 100:
				case 101:
					$filename = 'controller';
					$controller = $error_msg;
				break;

				// controller method missing
				case 102:
					$filename = 'function';
					$tmp = explode( ':', $error_msg );
					$controller =  $tmp[0];
					$method = $tmp[1];
				break;

				//--------------------------------------------------------
				//       model error
				//--------------------------------------------------------

				// pdo not available
				case 200:

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
				
				// model plugin class missing
				case 204:
					$filename = 'plugin';
					$plugin = $error_msg;
				break;

				// model alias name error / reserved format
				case 206:
				case 207:
					$filename = 'debug';
				break;

				// model name missing
				case 205:

				// model file missing
				case 208: 
				
				// model class missing				
				case 209:
					$filename = 'model';
					$model = $error_msg;
				break;

				// tablename missing
				case 210:

				// column missing
				case 211:

				// where clause missing
				case 212:
				
				// where clause value not match
				case 213:

				// where between input range
				case 214:

				// where between same value
				case 215:
					$filename = 'model';
					$model = $error_msg;					
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

				// library file missing
				case 403:

				// library class not exists
				case 404:
					$filename = 'library';
					$library = $error_msg;
				break;

				// library name invalid format
				case 401:
				
				// library alias reserved
				case 402:
					$filename = 'debug';
				break;

				//--------------------------------------------------------
				//       helper error
				//--------------------------------------------------------

				// helper filename invalid format
				case 500:

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
			if ($error_code !== 8)
				include dirname(dirname(__FILE__)) . DS . 'views' . DS . 'error_' . $filename . '.php';
		}

		// don't execute PHP internal error handler
		return true;
	}
}


/**
 * redirect
 *
 * forward current page to routing page
 *
 * @access public
 * @param string $url
 * @param bool $local
 * @return none
 */
if ( ! function_exists( 'redirect' ) )
{
	function redirect( $url, $local = true )
	{
		// check redirect url
		if ( empty( $url ) )
			return false;

        // define target
    	$url = $local ? CONF_BASE_PATH . $url : $url;

    	// ajax request
    	if ( is_ajax() )
    	{
    		echo "<script language=\"javascript\">
    				window.location = '{$url}';
    			  </script>";
    		exit;
    	}
    	else
    	{
			// forward user location address
			header( 'Location: ' . $url );
			exit;
		}
	}
}

/**
 * strip_slashes
 *
 * clean up slashes
 *
 * @access public
 * @param mixed $str
 * @return mixed
 */
if ( ! function_exists( 'strip_slashes' ) )
{
	function strip_slashes( $str )
	{
		if ( is_array( $str ) )
			foreach ( $str as $key => $val )
				$str[$key] = strip_slashes( $val );
		else
			$str = stripslashes( $str );

		return $str;
	}
}

/**
 * randomizer
 *
 * make random character
 *
 * @access public
 * @param integer $max
 * @param string $type
 * @return string
 */
if ( ! function_exists( 'randomizer' ) )
{
	function randomizer( $max=10, $type = 'both' )
	{
		// key list
		switch( $type )
		{
			case 'int':
			$key = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 );
			break;

			case 'chr':
			$key = array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' );
			break;

			case 'both':
			default:
			$key = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
			break;
		}

		// default data
		$result = '';

		// generate key
		for( $i = 1; $i <= $max; $i++ )
		{
			$random  = rand( 0, count( $key ) - 1 );
			$result .= $key[$random];
		}

		// re-check string lens
		if ( strlen( $result ) != $max )
			$result = randomizer( $max, $type );

		// return data
		return $result;
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
if ( ! function_exists( 'cache_flush' ) )
{
	function cache_flush()
	{
		// flush all cache
		return apc_clear_cache( 'user' );
	}
}

/**
 * cache_read
 *
 * read cache from system
 *
 * @access public
 * @param string $key
 * @return mixed
 */
if ( ! function_exists( 'cache_read' ) )
{
	function cache_read()
	{
	        // get cache
		$cache = apc_fetch(md5(URI_SEGMENT));

		// check the existing cache
		if ( $cache !== false )
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
 * @param string $key
 * @param bool $value
 * @param int $duration
 * @return none
 */
if ( ! function_exists( 'cache_write' ) )
{
	function cache_write( $value, $duration = 300 )
	{
		// no submission cache
		if ( $_POST )
			return false;

		// save cache to memory
		return apc_store( md5( URI_SEGMENT ), $value, $duration );
	}
}

/**
 * array_mpop
 *
 * pop array with iterate value
 *
 * @access public
 * @param array $array
 * @param int $iterate
 * @return array
 */
if ( ! function_exists( 'array_mpop' ) )
{ 
	function array_mpop($array, $iterate)
	{
		if ( ! is_array($array) AND is_int($iterate) )
			return false;
	
		while( ($iterate--) != false )
			array_pop($array);
		
		return $array;
	} 
}

/**
 * upload_array
 *
 * rearrange upload array
 *
 * @access public
 * @param array $array
 * @return array
 */
if ( ! function_exists( 'upload_array' ) )
{ 
	function upload_array( $array )
	{
		$new = array();
		
		if ( empty( $array ) )
			return $new;
		
		foreach( $array as $key => $all )
			foreach( $all as $i => $val )
				$new[$i][$key] = $val;   
		
		return $new;
	}
}

/**
 * is_ajax
 *
 * check either the request using ajax or not
 *
 * @access public
 * @return bool
 */
if ( ! function_exists( 'is_ajax' ) )
{ 
	function is_ajax( )
	{
		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) AND strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) 
			return true;
		else
			return false;
	}
}


/**
 * to_camel
 *
 * convert text to camel case
 *
 * @access public
 * @param  string $str
 * @param  string $glue
 * @return string
 */
if ( ! function_exists( 'to_camel') )
{
	function to_camel( $str, $glue = '_' ) 
	{
		// Split string in words.
		$words = explode($glue, strtolower($str));

		$return = '';

		foreach ($words as $word) 
			$return .= ucfirst(trim($word));

		return $return;
	}	
}

/**
 * from_camel
 *
 * convert camelcase to glue
 *
 * @access public
 * @param  string $str
 * @param  string $glue
 * @return string
 */
if ( ! function_exists( 'from_camel') )
{
	function from_camel( $str, $glue = '_' ) 
	{
		$str = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $glue . '$0', $str));
    	return strtolower($str);
	}	
}

/*
 * Auto defined config variables
 */
foreach( $config as $key => $val )
	define('CONF_' . strtoupper( $key ), $val );

/* Defined base path - Jasdy Syarman */
define('CONF_BASE_PATH', str_replace( $_SERVER['DOCUMENT_ROOT'], NULL, dirname( $_SERVER['SCRIPT_FILENAME'] ) ) );

/* End of common.php */
/* Location: core/helpers/common.php */