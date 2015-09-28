<?php
/**
 * user.php
 *
 * user controller function such as login, logout dashboard
 *
 * @package	    	phpHyppo
 * @subpackage		phpHyppo Application Builder
 * @author			Muhammad Hamizi Jaminan
 */
class Users_Controller extends AppController
{
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	function beforeFilter()
	{
		// load authentication library
		$this->load->library('authentication', 'auth');
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
		if (!$this->auth->is_logged_in())
			redirect('/user/login');

		// assign username from session data
		$session = $this->session->all_userdata();

		// display output
		$this->view->display('user/index', $session);
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
		if ($this->auth->is_logged_in())
			redirect('/users/index');
		
		// prepair update input
		$input = $this->input->post('data', true);
		
		// trigger post
		if ($input !== false)
		{
			// login success
			if ($this->auth->validate($input))
				redirect('/users/index');
			
			// login failed
			else
				$this->view->assign('error', 'Invalid username or password');
		}
		
		// display output
		$this->view->display('users/login');
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
		$this->auth->logout();
		
		// forward user login page
		redirect('/users/login');
	}
}

/* End of user.php */
/* Location: /application/controller/users.php */