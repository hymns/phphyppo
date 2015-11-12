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
 * AppController
 *
 * Class that manage to handle application controller
 *
 * @package			phpHyppo
 * @subpackage		Core Engine
 * @author			Muhammad Hamizi Jaminan
 */
class AppController
{
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// set the class instance
		registry::instance( $this );

		// instantiate loader engine
		$this->load = new AppLoader();

		// instantiate viewer engine
		$this->view = new AppViewer();
	}

	/**
	 * index
	 *
	 * the default controller method
	 *
	 * @access	public
	 */
	public function index()
	{

	}
}

/* End of AppController.php */
/* Location: core/classes/AppController.php */