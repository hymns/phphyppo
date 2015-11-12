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
 * @since			Version 9.07
 */

/**
 * NOTE: This library is mod & taken from CodeIgniter Library with 
 * permissions for compatibility for this framework release. all credits
 * belongs to CodeIgniter Author / Teams.
 */

/* no direct access */
if (!defined('BASEDIR'))
	exit;

/**
 * session.php
 *
 * Session Management Class
 *
 * @package			phpHyppo
 * @subpackage		Shared Library
 * @author			Muhammad Hamizi Jaminan
 */

class Session
{
	// session variable
	var $session_expiration				= 7200;
	var $session_time_to_update			= 300;
	var $session_match_ip				= true;
	var $session_match_useragent    	= true;
	var $session_encrypt_cookie		    = true;
	var $session_cookie_name		    = 'phphyppo';

	// cookie variable
	var $cookie_prefix					= '';
	var $cookie_path					= '/';
	var $cookie_domain					= '';
	var $encryption_key					= '';

	// local variable
	var $registry;
	var $userdata						= array();
	var $time_reference					= 'local';
	var $now;

	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */
	public function __construct()
	{
		// Set the super object to a local variable for use throughout the class
		$this->registry =& registry::instance();

		// Do we need use cookie? If so, load the input library
		$this->registry->load->library('input');

		// Do we need encryption? If so, load the encryption library
		if ($this->session_encrypt_cookie == true)
			$this->registry->load->library('encrypt');

		// Set the "now" time.  Can either be GMT or server time, based on the
		// config prefs.  We use this to set the "last activity" time
		$this->now = $this->_get_time();

		// Set the session length. If the session expiration is
		// set to zero we'll set the expiration two years from now.
		if ($this->session_expiration == 0)
			$this->session_expiration = (60*60*24*365*2);

		// Set the cookie name
		$this->session_cookie_name = $this->cookie_prefix . $this->session_cookie_name;

		// Run the Session routine. If a session doesn't exist we'll
		// create a new one.  If it does, we'll update it.
		if (!$this->session_read())
			$this->session_create();
		else
			$this->session_update();
	}

	/**
	 * Fetch the current session data if it exists
	 *
	 * @access	public
	 * @return	void
	 */
	public function session_read()
	{
		// Fetch the cookie
		$session = $this->registry->input->cookie($this->session_cookie_name);

		// No cookie? Goodbye cruel world!
		if ($session === false)
			return false;

		// Decrypt the cookie data
		if ($this->session_encrypt_cookie == true)
		{
			$session = $this->registry->encrypt->decode($session);
		}
		else
		{
			// encryption was not used, so we need to check the md5 hash
			$hash	 = substr($session, strlen($session)-32); // get last 32 chars
			$session = substr($session, 0, strlen($session)-32);

			// Does the md5 hash match?  This is to prevent manipulation of session data in userspace
			if ($hash !==  md5($session . $this->encryption_key))
			{
				$this->session_purge();
				return false;
			}
		}

		// Unserialize the session array
		$session = $this->_unserialize($session);

		// Is the session data we unserialized an array with the correct format?
		if (!is_array($session) || !isset($session['session_id']) || !isset($session['ip_address']) || !isset($session['user_agent']) || !isset($session['last_activity']))
		{
			$this->session_purge();
			return false;
		}

		// Is the session current?
		if (($session['last_activity'] + $this->session_expiration) < $this->now)
		{
			$this->session_purge();
			return false;
		}

		// Does the IP Match?
		if ($this->session_match_ip == true && $session['ip_address'] != $this->registry->input->ip_address())
		{
			$this->session_purge();
			return false;
		}

		// Does the User Agent Match?
		if ($this->session_match_useragent == true && trim($session['user_agent']) != trim(substr($this->registry->input->user_agent(), 0, 50)))
		{
			$this->session_purge();
			return false;
		}

		// Session is valid!
		$this->userdata = $session;
		unset($session);

		return true;
	}

	/**
	 * Write the session data
	 *
	 * @access	public
	 * @return	void
	 */
	public function session_write()
	{
		$this->_set_cookie();
		return;
	}

	/**
	 * Create a new session
	 *
	 * @access	public
	 * @return	void
	 */
	public function session_create()
	{
		$sessid = '';

		// generate session id
		while (strlen($sessid) < 32)
			$sessid .= mt_rand(0, mt_getrandmax());

		// To make the session ID even more secure we'll combine it with the user's IP
		$sessid .= $this->registry->input->ip_address();

		// assign session
		$this->userdata = array(
											'session_id'	=> md5(uniqid($sessid, true)),
											'ip_address'	=> $this->registry->input->ip_address(),
											'user_agent' 	=> substr($this->registry->input->user_agent(), 0, 50),
											'last_activity'	=> $this->now
										);

		// Write the cookie
		$this->_set_cookie();
	}

	/**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
	public function session_update()
	{
		// We only update the session every five minutes by default
		if (($this->userdata['last_activity'] + $this->session_time_to_update) >= $this->now)
			return;
		
		// Save the old session id so we know which record to
		// update in the database if we need it
		$old_sessid = $this->userdata['session_id'];
		
		// Generate new session id
		$new_sessid = '';
		while (strlen($new_sessid) < 32)
			$new_sessid .= mt_rand(0, mt_getrandmax());
		
		// To make the session ID even more secure we'll combine it with the user's IP
		$new_sessid .= $this->registry->input->ip_address();
		
		// Turn it into a hash
		$new_sessid = md5(uniqid($new_sessid, true));
		
		// Update the session data in the session data array
		$this->userdata['session_id'] = $new_sessid;
		$this->userdata['last_activity'] = $this->now;
		
		// _set_cookie() will handle this for us if we aren't using database sessions
		// by pushing all userdata to the cookie.
		$cookie_data = null;
		
		// Write the cookie
		$this->_set_cookie($cookie_data);
	}


	/**
	 * Destroy the current session
	 *
	 * @access	public
	 * @return	void
	 */
	public function session_purge()
	{
		// Kill the cookie
		setcookie(
					$this->session_cookie_name,
					addslashes(serialize(array())),
					($this->now - 31500000),
					$this->cookie_path,
					$this->cookie_domain,
					0
				);
	}

	/**
	 * Fetch a specific item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function userdata($item)
	{
		return (!isset($this->userdata[$item])) ? false : $this->userdata[$item];
	}

	/**
	 * Fetch all session data
	 *
	 * @access	public
	 * @return	mixed
	 */
	public function all_userdata()
	{
		return (!isset($this->userdata)) ? false : $this->userdata;
	}


	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	public function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
			$newdata = array($newdata => $newval);
		
		if (sizeof($newdata) > 0)
			foreach($newdata as $key => $val)
				$this->userdata[$key] = $val;
		
		$this->session_write();
	}

	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */
	public function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
			$newdata = array($newdata => '');
		
		if (sizeof($newdata) > 0)
			foreach ($newdata as $key => $val)
				unset($this->userdata[$key]);
		
		$this->session_write();
	}


	/**
	 * Get the "now" time
	 *
	 * @access	private
	 * @return	string
	 */
	private function _get_time()
	{
		if (strtolower($this->time_reference) == 'gmt')
		{
			$now = time();
			$time = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));
		}
		else
		{
			$time = time();
		}
		
		return $time;
	}

	/**
	 * Write the session cookie
	 *
	 * @access	private
	 * @return	void
	 */
	private function _set_cookie($cookie_data = null)
	{
		if (is_null($cookie_data))
			$cookie_data = $this->userdata;

		// Serialize the userdata for the cookie
		$cookie_data = $this->_serialize($cookie_data);

		// encrypt the cookies
		if ($this->session_encrypt_cookie == true)
			$cookie_data = $this->registry->encrypt->encode($cookie_data);

		// if encryption is not used, we provide an md5 hash to prevent userside tampering
		else
			$cookie_data = $cookie_data . md5($cookie_data . $this->encryption_key);

		// Set the cookie
		setcookie(
					$this->session_cookie_name,
					$cookie_data,
					$this->session_expiration + time(),
					$this->cookie_path,
					$this->cookie_domain,
					0
				);
	}

	/**
	 * Serialize an array
	 *
	 * This function first converts any slashes found in the array to a temporary
	 * marker, so when it gets unserialized the slashes will be preserved
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _serialize($data)
	{
		if (is_array($data))
			foreach ($data as $key => $val)
				$data[$key] = str_replace('\\', '{{slash}}', $val);

		else
			$data = str_replace('\\', '{{slash}}', $data);

		return serialize($data);
	}

	/**
	 * Unserialize
	 *
	 * This function unserializes a data string, then converts any
	 * temporary slash markers back to actual slashes
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	private function _unserialize($data)
	{
		$data = @unserialize(strip_slashes($data));

		if (is_array($data))
		{
			foreach ($data as $key => $val)
				$data[$key] = str_replace('{{slash}}', '\\', $val);
			
			return $data;
		}
		
		return str_replace('{{slash}}', '\\', $data);
	}

}

/* End of session.php */
/* Location: core/libraries/session.php */