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
 * registry
 *
 * main framework object registry class
 *
 * @package	    	phpHyppo
 * @subpackage		Core Engine
 * @author			Muhammad Hamizi Jaminan
 */
class registry
{
	/**
	 * instance
	 *
	 * set & get the registry object instance
	 *
	 * @access	public
	 * @param	object $new_instance reference to new object instance
	 * @return	object $instance reference to object instance
	 */
	public static function &instance( $new_instance = null )
	{
		static $instance = null;

		if ( isset( $new_instance ) && is_object( $new_instance ) )
			$instance = $new_instance;

		return $instance;
	}
}

/**
 * get_instance
 *
 * set & get the registry object instance function
 *
 * @access public
 */
function &get_instance( $new_instance = null )
{
	return registry::instance( $new_instance );
}

/* End of file AppRegistry.php */
/* Location: core/classes/AppRegistry.php */