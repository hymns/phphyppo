<?php
/**
 * hello.php
 *
 * demo application controller
 *
 * @package	Application
 * @author		Muhammad Hamizi Jaminan
 */

class Hello_Controller extends AppController
{
	function index()
	{
		$this->view->display('hello_view');
	}
}

/* End of hello.php */
/* Location: application/controllers/hello.php */
?>