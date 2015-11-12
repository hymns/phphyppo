<?php
/**
 * hello.php
 *
 * demo application controller
 *
 * @package		Sample Application
 * @author		Muhammad Hamizi Jaminan
 */

class Hello_Controller extends AppController
{
	function index()
	{
		$this->view->display('hello_view');
	}

	function oracle()
	{
		$db = $this->load->database('oracle_db');
		
		$test = $db->query_all('select tablespace_name, table_name from user_tables');
		print_r($test);
	}
}

/* End of hello.php */
/* Location: application/controllers/hello.php */