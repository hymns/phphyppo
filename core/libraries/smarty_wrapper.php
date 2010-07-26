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
 * @since			Version 9.07
 */

/* no direct access */
if (!defined('BASEDIR'))
	exit;

/* load smarty class file */
require_once '/var/smarty/libs/Smarty.class.php';

/**
 * smarty.php
 *
 * Smarty Wrapper Class
 *
 * @package		phpHyppo
 * @subpackage	Shared Library
 * @author			Muhammad Hamizi Jaminan
 */
class Smarty_Wrapper extends Smarty
{
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	function __construct()
	{
		// call parent class
		parent::Smarty();

		// smarty config
		$this->compile_dir = '/var/smarty/templates_c/';
		$this->config_dir = '/var/smarty/configs/';

		// use application cache folder
		$this->cache_dir = APPDIR . 'cache/';

		// use application view folder
		$this->template_dir = APPDIR . 'views/';
	}
}

?>