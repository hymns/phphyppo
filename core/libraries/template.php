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
 * template.php
 *
 * Template Engine Class
 *
 * @package			phpHyppo
 * @subpackage		Shared Library
 * @author			Muhammad Hamizi Jaminan
 */
class Template
{
	var $delim_l = '{';
	var $delim_r = '}';
	var $object;

 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	function __construct()
	{

	}

	/**
	 *  Display a template
	 *
	 * Parses & display pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function display($template, $data, $return = false)
	{
		$template = $this->_fetch($template);

		if ($template == '')
		{
			return FALSE;
		}

		foreach ($data as $key => $val)
		{
			if (is_array($val))
				$template = $this->_parse_pair($key, $val, $template);
			else
				$template = $this->_parse_single($key, (string) $val, $template);
		}

		if ($return == false)
			echo $template;
		else
			return $template;
	}

	/**
	 *  Set the left/right variable delimiters
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function set_delimiters($l = '{', $r = '}')
	{
		$this->delim_l = $l;
		$this->delim_r = $r;
	}

	/**
	 *  Parse a single key/value
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	private function _parse_single($key, $val, $string)
	{
		return str_replace($this->delim_l.$key.$this->delim_r, $val, $string);
	}

	/**
	 *  Parse a tag pair
	 *
	 * Parses tag pairs:  {some_tag} string... {/some_tag}
	 *
	 * @access	private
	 * @param	string
	 * @param	array
	 * @param	string
	 * @return	string
	 */
	private function _parse_pair($variable, $data, $string)
	{
		if (($match = $this->_match_pair($string, $variable)) === false)
			return $string;

		$str = '';
		foreach ($data as $row)
		{
			$temp = $match['1'];
			foreach ($row as $key => $val)
			{
				if (!is_array($val))
					$temp = $this->_parse_single($key, $val, $temp);
				else
					$temp = $this->_parse_pair($key, $val, $temp);
			}

			$str .= $temp;
		}

		return str_replace($match['0'], $str, $string);
	}

	/**
	 *  Matches a variable pair
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	mixed
	 */
	private function _match_pair($string, $variable)
	{
		if (!preg_match("|".$this->delim_l . $variable . $this->delim_r."(.+?)".$this->delim_l . '/' . $variable . $this->delim_r."|s", $string, $match))
			return false;

		return $match;
	}

	/**
	 * fetch
	 *
	 * return the contents of a view file
	 *
	 * @access	private
	 * @param	string $filename
	 * @return	string contents of view
	 */
	private function _fetch($filename)
	{
		// viewer extension lists
		$extensions = array( 'php', 'html', 'tpl' );

		// loop over extension lists
		foreach( $extensions as $ext )
		{
			$filepath = APPDIR . 'views' . DS . $filename . '.' . $ext;

			// grab viewer file path
			if ( file_exists( $filepath ) )
				break;
		}

		// check file path
		if ( ! file_exists( $filepath ) )
			return null;

		else
		{
			ob_start();
			include($filepath);
			$results = ob_get_contents();
			ob_end_clean();

			return $results;
		}
	}
}

/* End of template.php */
/* Location: core/libraries/template.php */