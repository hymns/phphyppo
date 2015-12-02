<?php
/**
 * session.php
 *
 * session controller function such as login, logout dashboard
 *
 * @package	    	phpHyppo
 * @subpackage		phpHyppo Application Builder
 * @author			Muhammad Hamizi Jaminan
 */
class Session_Controller extends AppController
{
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	function beforeFilter()
	{
		// load acl library
		$this->load->library('acl');
	}

	/**
	 * index
	 *
	 * show user dashboard
	 *
	 * @access public
	 * @return none
	 */
	public function index()
	{
		// check permission
		if ( ! $this->acl->is_login() )
			redirect('/session/login');

		// assign username from session data
		$session = $this->session->all_userdata();

		// display output
		$this->view->display('session/index', $session);
	}

	/**
	 * login
	 *
	 * show login form
	 *
	 * @access public
	 * @return none
	 */
	public function login()
	{
		// check existing session
		if ( $this->acl->is_login() )
			redirect('/session/index');
		
		// prepair update input
		$input = $this->input->post('data', true);
		
		// trigger post
		if ( $input !== false )
		{
			// login success 
			if ( $this->acl->authorize($input) )
				redirect('/session/index');
			
			// login failed
			else
				$this->view->assign('error', 'Invalid username or password');
		}
		
		// display output
		$this->view->display('session/login');
	}

	/**
	 * logout
	 *
	 * logout user from system
	 *
	 * @access public
	 * @return none
	 */
	public function logout()
	{
		// logging out user
		$this->acl->revoke('/session/login');
	}
}

/* End of session.php */
/* Location: /application/controller/session.php */