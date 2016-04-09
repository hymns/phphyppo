<?php
/**
 * phpHyppo
 *
 * An open source MVC application framework for PHP 5.1+
 *
 * @package			phpHyppo
 * @author			Muhammad Hamizi Jaminan <hello@swirly.my>
 * @copyright		Copyright (c) 2015, Swirly Technology
 * @license			LGPL, see included license file
 * @link			http://www.swirly.my
 */

/* no direct access */
if ( ! defined('BASEDIR'))
	exit;

/**
 * Access Control List
 *
 * library for user access control
 * 
 * @package			phpHyppo
 * @subpackage		Application Library
 * @author			Muhammad Hamizi Jaminan
 */
class ACL
{
	/**
	 * form filter variable
	 *
	 * @access private
	 */
	public $filter = ['username', 'password'];

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
	 * authorize()
	 *
	 * make validation for user access
	 *
	 * <code>
	 * 		$auth = ['username' => 'admin', 'password' => 'secret'];
	 * 	 
	 *		$this->load->library('acl');
	 *		
	 *		if ( ! $this->acl->authorize($auth) )
	 *			redirect('/user/denied');
	 * </code>
	 *
	 * @access public
	 * @param array 	$auth 		Array of user info, accept array key username & password
	 * @param bool 		$encrypt 	Pass value 'true' when you want using encryption. Default is 'false'
	 * @param string 	$algorithm 	The type of algorithm. Default is 'sha1'
	 * @param string	$field 		Field for encryption field. Default is 'password'
	 * @return bool
	 */
	public function authorize($auth, $encrypt = false, $algorithm = 'sha1', $field = 'password')
	{
		// only accept filter field
		$auth = array_intersect_key($auth, array_flip($this->filter));

		// no username & password supply
		if ( empty($auth) OR count($auth) != 2 )
			return false;
			
		// check encryption
		if ( $encrypt == true )
			$auth[$field] = $algorithm($auth[$field]);

		// make user validation from database
		$profile = $this->_validateUser($auth);
		
		// profile user not available / match
		if ($profile === false)
			return false;
		
		// get roles
		$roles = $this->_getUserRoles($profile['group_id']);

		// prepair session data
		$session = [
						'user_id' 	=> $profile['user_id'],
						'username' 	=> $profile['username'],
						'fullname' 	=> $profile['first_name'] . ' ' . $profile['last_name'],
						'logged_in' => true,
						'roles' 	=> $roles,
						'lastlogin' => ! empty($profile['lastlogin']) ? $profile['lastlogin'] : 'n/a'
					];
		
		// set session data
		$this->instance->session->set_userdata($session);
		
        // update login info
		$this->_updateLogin($profile['user_id']);
		
		// authorize
		return true;
	}

	/**
	 * revoke()
	 *
	 * logging out user and redirect
	 *
	 * <code>
	 *		$this->load->library('acl');
	 *		$this->acl->revoke('/access/login');
	 * </code>
	 * 
	 * @access public
	 * @param string $location
	 * @return bool
	 */
	public function revoke($location = null)
	{
		// clear user session
		$this->instance->session->session_purge();

		// redirect to user location
		if ( ! is_null($location) )
			redirect($location);
	}

	/**
	 * is_login()
	 *
	 * check either user is logged in
	 * 
	 * <code>
	 *		$this->load->library('acl');
	 *
	 *		if ( ! $this->acl->is_login() )
	 *			redirect('/access/login');
	 * </code>
	 * 
	 * @access public
	 * @return bool
	 */
	public function is_login()
	{
		return $this->instance->session->userdata('logged_in') != false;
	}

	/**
	 * is_allowed()
	 *
	 * check current user permission
	 *
	 * <code>
	 *		$this->load->library('acl');
	 *
	 *		if ( ! $this->acl->is_allowed('admin', 'update') )
	 *			redirect('/user/no_permission');
	 * </code>
	 * 
	 * @access public
	 * @param string 	$module 	Registered module or controller
	 * @param string 	$role 		The role allowed for access. For wildcard access, set to asterisk '*'
	 * @return bool
	 */
	public function is_allowed($module, $role)
	{
		// recheck login
		if ( ! $this->is_login() )
			return false;

		// get role session
		$roles = $this->instance->session->userdata('roles');
		
		// check module
		if ( in_array($module, array_keys($roles)) != false )
		{
			// wildcard access
			if ( $role == '*' )
				return true;

			// wildcard role
			elseif ( in_array('*', array_values($roles[$module])) != false)
				return true;

			// specific
			else
				return in_array($role, $roles[$module]) != false;
		}

		return false;
	}

	/**
	 * initiate()
	 *
	 * Initiated library to build ACL schema
	 * 
	 * <code>
	 *		$this->load->library('acl');
	 *  	$this->acl->initiate()
	 * </code>
	 * 
	 * @access public
	 * @return bool
	 */
	public function initiate()
	{
		// load database
		$this->db = $this->instance->load->database();

		// create table users if not exits
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

		// create table groups if not exits
		$this->db->query("CREATE TABLE IF NOT EXISTS `groups` (
							  `group_id` int(11) NOT NULL AUTO_INCREMENT,
							  `group_name` varchar(40) NOT NULL,
							  PRIMARY KEY (`group_id`)
							) ENGINE=MyISAM
						");

		// create table roles if not exits
		$this->db->query("CREATE TABLE IF NOT EXISTS `roles` (
							  `role_id` int(11) NOT NULL AUTO_INCREMENT,
							  `group_id` int(11) NOT NULL,
							  `module` varchar(40) NOT NULL,
							  `role` varchar(40) NOT NULL,
							  PRIMARY KEY (`role_id`),
							  KEY `group_id` (`group_id`)
							) ENGINE=MyISAM
						");

		// set default user
		if ( ! $user = $this->db->where('user_id', 1)->find('users') )
			$this->db->insert('users', [
											'user_id' => 1, 
											'group_id' => 1, 
											'username' => 'admin', 
											'password' => 'admin',
											'first_name' => 'system',
											'last_name' => 'admin'
										]
								);

		// set default group name
		if ( ! $group = $this->db->where('group_id', 1)->find('groups') )
			$this->db->insert('groups', [
											'group_id' => 1, 
											'group_name' => 'admin'
										]
								);
	}

	/**
	 * _validateUser()
	 *
	 * Verify authentication from database information
	 *
	 * @access private
	 * @param array 	$data 	Set of array for validation
	 * @return array
	 */
	private function _validateUser($data)
	{
		// load database
		$this->db = $this->instance->load->database();
			
		// return record set
		return $this->db->select('user_id, group_id, first_name, last_name, username, lastlogin')
						->where($data)
						->find('users');
	}

	/**
	 * _getUserRoles()
	 *
	 * get user roles
	 *
	 * @access private
	 * @param array 	$group_id 	ID for specific group roles
	 * @return array
	 */
	private function _getUserRoles($group_id)
	{
		// load database
		$this->db = $this->instance->load->database();

		// get record
		$records = $this->db->select('module, role')
							->where('group_id', $group_id)
							->find_all('roles');

		// prepare for roles
		$module = '';
		$roles = [];

		foreach ($records as $record) 
		{
			if ($record['module'] != $module)
				$module = $record['module'];

			$roles[$module][] = $record['role'];
		}

		// return roles
		return $roles;
	}

	/**
	 * _updateLogin()
	 *
	 * update last login info
	 *
	 * @access private
	 * @param array $data
	 * @return array
	 */
	private function _updateLogin($user_id)
	{
		// set last login
		$data = [ 'lastlogin' => date('Y-m-d H:i:s') ];		

		// return result
		return $this->db->where('user_id', $user_id)
						->update('users', $data);
	}	
}

/* End of file acl.php */
/* Location:  application/libraries/acl.php */