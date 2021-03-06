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
 * AppViewer
 *
 * Class that manage to handle application viewer
 *
 * @package			phpHyppo
 * @subpackage		Core Engine
 * @author			Muhammad Hamizi Jaminan
 */

class AppViewer
{
 	/**
	 * $vars
	 *
	 * vars for view file assignment
	 *
	 * @access	public
	 */
	public $vars = array();

 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
	
	}

	/**
	 * assign
	 *
	 * assign view variables
	 *
	 * @access	public
	 * @param	mixed $key key of assignment, or value to assign
	 * @param	mixed $value value of assignment
	 */
	public function assign($key, $value = null)
	{
		// single assign
		if ( isset( $value ) )
			$this->vars[$key] = $value;

		// array assign
		else
		{
			if ( is_array( $key ) )
			{
				foreach( $key as $k => $v )
				{
					if ( is_int( $k ) )
						$this->vars[] = $v;
					else
						$this->vars[$k] = $v;
				}
			}
		}
	}
     
	/**
	 * display
	 *
	 * display a view file
	 *
	 * @access	public
	 * @param	string $filename the name of the view file
	 * @param	string $vars
	 * @return	boolean
	 */
	public function display( $filename, $vars = null )
	{
		// viewer viewer extension lists
		$extensions = array( 'php', 'html', 'tpl' );

		// loop over extension lists
		foreach( $extensions as $ext )
		{
			$filepath = APPDIR . 'views' . DS . $filename . '.' . $ext;

			// grab viewer file path
			if ( file_exists( $filepath ) )
				break;
		}

		// return output
		return $this->_view( $filepath, $vars );
	}

	/**
	 * debug
	 *
	 * display a application error
	 *
	 * @access	private
	 * @param	string $filename
	 * @param	string $vars
	 * @return	boolean
	 */
	public function debug( $filename, $vars = null )
	{
		$filepath = BASEDIR . 'core' . DS . 'views' . DS . $filename . '.php';
		return $this->_view( $filepath, $vars );
	}

    /**
     * capture
     *
     * alias of fetch
     *
     * @access	public
     * @param	string $filename
     * @param	string $vars
     * @return	string contents of view
     */
    public function capture( $filename, $vars = null )
    {
		return $this->fetch( $filename, $vars );
	}

	/**
	 * fetch
	 *
	 * return the contents of a view file
	 *
	 * @access	public
	 * @param	string $filename
	 * @param	string $vars
	 * @return	string contents of view
	 */
	public function fetch( $filename, $vars = null )
	{
		ob_start();
		$this->display( $filename, $vars );
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * _view
	 *
	 * display a view file
	 *
	 * @access	private
	 * @param	string $filepath
	 * @param	array $vars
	 */
	private function _view( $filepath, $vars = null )
	{
		// check file path
		if ( ! file_exists( $filepath ) )
			throw new Exception( $filepath, 300 );

		// bring view vars into view scope
		extract( $this->vars );

		// extract supply vars
		if ( isset( $vars ) && is_array( $vars ) )
			extract( $vars );

		// include viewer file
		include $filepath;
	}
	
	/**
	 * __get
	 *
	 * get value assign variables
	 *
	 * @access	public
	 * @param	mixed $key key of assignment, or value to assign
	 * @param	mixed $value value of assignment
	 */
	public function __get( $key )
	{
		return array_key_exists( $key, $this->vars ) ? $this->vars[$key] : null;
	}

	/**
	 * __set
	 *
	 * assign view variables
	 *
	 * @access	public
	 * @param	mixed $key key of assignment, or value to assign
	 * @param	mixed $value value of assignment
	 */
	public function __set( $key, $value )
	{
		$this->assign( $key, $value );
	}	
}

/* End of file AppViewer.php */
/* Location: core/classes/AppViewer.php */