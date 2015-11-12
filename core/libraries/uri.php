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
if (!defined('BASEDIR'))
	exit;

/**
 * URI
 *
 * URI plugin extractor
 *
 * @package			phpHyppo
 * @subpackage		Shared Library
 * @author			Muhammad Hamizi Jaminan
 */

/**
 * Example Usage:
 * URI path example: http://localhost/blog/update/7/username/hymns/category/framework
 *
 * // Load from controller
 * $this->load->library('uri');
 *
 * //
 * // Example 1
 * // - Get second segment value from uri path
 * //
 * echo $this->uri->segment(2);
 *
 * output: 7
 *
 * //
 * // Example 2
 * // - Get key & val associative array, starting from third segment
 * //
 * $uri = $this->uri->to_assoc(3);
 * print_r($uri);
 *
 * output:
 *        Array(
 *            [username] => hymns
 *            [category] => framework
 *        )
 *
 * //
 * // Example 3
 * // - Assign url to an indexed array, starting from third segment
 * //
 * $uri = $this->uri->to_array(3);
 * print_r($uri);
 *
 * output:
 *        Array(
 *            [0] => hymns
 *            [1] => category
 *            [2] => framework
 *        )
 *
 */

class URI
{
 	/**
	 * $path
	 *
	 * vars for uri path assignment
	 *
	 * @access	public
	 */
	var $path = null;

 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// grab uri segment
		if ( $uri_segment = empty($_SERVER[CONF_URI_PROTOCOL]) ? null : $_SERVER[CONF_URI_PROTOCOL] )
		{
			// extract uri segment
			$this->path = explode('/', $uri_segment);
			$this->path = array_slice($this->path, 2);
		}
	}

 	/**
	 * segment
	 *
	 * get path by segment
	 *
	 * @access	public
	 * @return	string
	 */
	public function segment($index)
	{
		if ( ! empty($this->path[$index-1]) )
			return $this->path[$index-1];
		else
			return false;
	}

 	/**
	 * to_assoc
	 *
	 * set uri path to associate id
	 *
	 * @access	public
     * @param  integer $index
	 * @return	array
	 */
	public function to_assoc($index)
	{
		$assoc = array();

		for ( $i = sizeof($this->path), $x = $index-1; $x < $i; $x += 2 )
		{
			$key = $this->path[$x];
			$assoc[$key] = isset($this->path[$x+1]) ? $this->path[$x+1] : null;
		}

		return $assoc;
	}

 	/**
	 *  to_array
	 *
	 * set uri path to array
	 *
	 * @access	public
     * @param  integer $index
	 * @return	array
	 */
	public function to_array($index = 0)
	{
		if ( is_array($this->path) )
			return array_slice($this->path, $index);
		else
			return false;
	}
 }

/* End of file uri.php */
/* Location: core/libraries/uri.php */