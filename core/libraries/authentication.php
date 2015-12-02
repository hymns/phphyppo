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
 */

/* no direct access */
if (!defined('BASEDIR'))
	exit;

/**
 * Authentication
 *
 * library for user authentication
 *
 * @package			phpHyppo
 * @subpackage		Application Library
 * @author			Muhammad Hamizi Jaminan
 */
class Authentication
{
	/**
	 * instance variable
	 *
	 * @access private
	 */
	private $instance;

	/**
	 * database variable
	 *
	 * @access private
	 */
	private $db;

	/**
	 * form filter variable
	 *
	 * @access private
	 */
	private $filter = array('username', 'password');

 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// load up instance
		$this->instance = get_instance();
		
		// load up session library
		$this->instance->load->library('session');
	}

	/**
	 * has_permissions()
	 *
	 * check current logged in user role
	 *
	 * usage:
	 *					$this->load->library('authentication');
	 *
	 *					if (!$this->authentication->has_permissions(2))
	 *						redirect('/user/no_permission');
	 *
	 * @access public
	 * @param integer $role
	 * @param bool $strict
	 * @return bool
	 */
	public function has_permissions($role, $strict = false)
	{
		if ($strict)
			return $this->instance->session->userdata('logged_role') == $role ? true : false;
		else
			return $this->instance->session->userdata('logged_role') >= $role ? true : false;
	}

	/**
	 * is_logged_in()
	 *
	 * check whether user is logged in
	 *
	 * usage:
	 *					$this->load->library('authentication');
	 *
	 *					if (!$this->authentication->is_logged_in())
	 *						redirect('/user/login');
	 *
	 * @access public
	 * @return bool
	 */
	public function is_logged_in()
	{
		return $this->instance->session->userdata('logged_in') != false;
	}

	/**
	 * logout()
	 *
	 * logging out user authentication
	 *
	 * usage:
	 *					$this->load->library('authentication');
	 *
	 *					$this->authentication->logout();
	 *					redirect('/user/login');
	 *
	 * @access public
	 * @return bool
	 */
	public function logout()
	{
		// clean user session
		$this->instance->session->session_purge();
	}

	/**
	 * validate()
	 *
	 * verify user authentication
	 *
	 * usage:
	 *					$this->load->library('authentication');
	 *
	 *					if ($this->authentication->validate())
	 *						redirect('/user/home');
	 *
	 * @access public
	 * @param array $input
	 * @param string $provider
	 * @return bool
	 */
	public function validate($input)
	{
		// clean up
		$input = array_intersect_key($input, array_flip($this->filter));

		// no username & password supply
		if ( empty($input) || sizeof($input) != 2 )
			return false;
			
		// authentication check on database
		$user = $this->_login_check($input);
		
		// invalid login
		if ($user === false)
			return false;
		
		// prepair session data
		$session = array(
						'user_id' => $user['user_id'],
						'username' => $user['username'],
						'fullname' => $user['first_name'] . ' ' . $user['last_name'],
						'logged_in' => true,
						'logged_role' => $user['group_id'],
						'last_logged_in' => !empty($user['lastlogin']) ? $user['lastlogin'] : 'never'
					);
		
		// set session data
		$this->instance->session->set_userdata($session);
		
		// prepair login data
		$data = array(
                    'lastlogin' => date('Y-m-d H:i:s'),
                    'user_id' => $user['user_id']
                );
		
        // update login info
		$this->_login_update($data);
		
		// return result
		return true;
	}
	
	/**
	 * _login_check()
	 *
	 * verify authentication from database
	 *
	 * @access private
	 * @param array $data
	 * @return array
	 */
	private function _login_check($data)
	{
		// load database
		$this->db = $this->instance->load->database();
	
		// create table if not exits
		$this->db->query("CREATE TABLE IF NOT EXISTS `users` (
							  `user_id` int(11) NOT NULL AUTO_INCREMENT,
							  `group_id` int(11) NOT NULL,
							  `username` varchar(40) NOT NULL,
							  `password` varchar(40) NOT NULL,
							  `first_name` varchar(50) NOT NULL,
							  `last_name` varchar(50) NOT NULL,
							  `lastlogin` datetime NOT NULL,
							  PRIMARY KEY (`user_id`),
							  UNIQUE KEY `username` (`username`),
							  KEY `group_id` (`group_id`)
							) ENGINE=MyISAM
						");
		
		// get assiociate field
		$this->db->select('user_id, group_id, first_name, last_name, username, lastlogin');
		
		// set conditions
		$this->db->where($data);

		// return record set
		return $this->db->find('users');
	}
	
	/**
	 * _login_update()
	 *
	 * update last login info
	 *
	 * @access private
	 * @param array $data
	 * @return array
	 */
	private function _login_update($data)
	{
		// set conditions
		$this->db->where('user_id', $data['user_id']);
		
		// remove user_id
		array_pop($data);
		
		// return result
		return $this->db->update('users', $data);
	}	
}

/* End of file authentication.php */
/* Location:  application/libraries/authentication.php */