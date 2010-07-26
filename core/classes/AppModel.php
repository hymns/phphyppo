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
 * AppModel
 *
 * Class that manage to handle application model
 *
 * @package	    phpHyppo
 * @subpackage	Core Engine
 * @author			Muhammad Hamizi Jaminan
 */

class AppModel
{
 	/**
	 * $db
	 *
	 * the database object instance
	 *
	 * @access	public
	 */
	var $db = null;

 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// load database library
		$this->db = registry::instance()->load->database();
	}
}

/* End of file AppModel.php */
/* Location: core/classes/AppModel.php */
?>