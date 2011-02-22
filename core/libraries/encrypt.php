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
 * encrypt.php
 *
 * Encryption Class
 *
 * @package		phpHyppo
 * @subpackage	Shared Library
 * @author			Muhammad Hamizi Jaminan
 */

/**
 * Note: This library is mod & taken from CI Library
 * for compatibility for this framework release.
 * all credits belong to CodeIgniter Author.
 */
class Encrypt
{
	var $registry;
	var $encryption_key	    = '';
	var $_hash_type				= 'sha1';

	/**
	 * Constructor
	 *
	 * Class constructor
	 *
	 */
	function __construct()
	{
	}

	/**
	 * Fetch the encryption key
	 *
	 * Returns it as MD5 in order to have an exact-length 128 bit key.
	 *
	 * @access	public
	 * @param	string $key
	 * @return	string
	 */
	public function get_key($key = '')
	{
		if ($key == '')
		{
			if ($this->encryption_key != '')
				return $this->encryption_key;
			
			// no key? exit
			if (CONF_ENCRYPTION_KEY === FALSE)
				throw('In order to use the encryption class requires that you set an encryption key in your config file.');
		}

		return md5($key);
	}

	/**
	 * Set the encryption key
	 *
	 * @access	public
	 * @param	string $key
	 * @return	void
	 */
	public function set_key($key = '')
	{
		$this->encryption_key = $key;
	}

	/**
	 * Encode
	 *
	 * Encodes the message string using bitwise XOR encoding.
	 * The key is combined with a random hash, and then it
	 * too gets converted using XOR.
	 *
	 * @access	public
	 * @param	string $string	the string to encode
	 * @param	string $key the key
	 * @return	string
	 */
	public function encode($string, $key = '')
	{
		$key = $this->get_key($key);
		$enc = $this->_xor_encode($string, $key);

		return base64_encode($enc);
	}

	/**
	 * Decode
	 *
	 * Reverses the above process
	 *
	 * @access	public
	 * @param	string $string
	 * @param	string $key
	 * @return	string
	 */
	public function decode($string, $key = '')
	{
		$key = $this->get_key($key);

		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string))
			return FALSE;

		$dec = base64_decode($string);

		return $this->_xor_decode($dec, $key);
	}

	/**
	 * XOR Encode
	 *
	 * Takes a plain-text string and key as input and generates an
	 * encoded bit-string using XOR
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	private function _xor_encode($string, $key)
	{
		$rand = '';
		while (strlen($rand) < 32)
			$rand .= mt_rand(0, mt_getrandmax());

		$rand = $this->hash($rand);

		$enc = '';
		for ($i = 0; $i < strlen($string); $i++)
			$enc .= substr($rand, ($i % strlen($rand)), 1).(substr($rand, ($i % strlen($rand)), 1) ^ substr($string, $i, 1));

		return $this->_xor_merge($enc, $key);
	}

	/**
	 * XOR Decode
	 *
	 * Takes an encoded string and key as input and generates the
	 * plain-text original message
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	private function _xor_decode($string, $key)
	{
		$string = $this->_xor_merge($string, $key);

		$dec = '';
		for ($i = 0; $i < strlen($string); $i++)
			$dec .= (substr($string, $i++, 1) ^ substr($string, $i, 1));

		return $dec;
	}

	/**
	 * XOR key + string Combiner
	 *
	 * Takes a string and key as input and computes the difference using XOR
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	private function _xor_merge($string, $key)
	{
		$hash = $this->hash($key);
		$str = '';
		for ($i = 0; $i < strlen($string); $i++)
			$str .= substr($string, $i, 1) ^ substr($hash, ($i % strlen($hash)), 1);

		return $str;
	}

	/**
	 * Set the Hash type
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function set_hash($type = 'md5')
	{
		$this->_hash_type = ($type != 'md5' AND $type != 'sha1') ? 'md5' : $type;
	}

	/**
	 * Hash encode a string
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function hash($str)
	{
		return ($this->_hash_type == 'md5') ? md5($str) : sha1($str);
	}

}

/* End of file encrypt.php */
/* Location: core/libraries/encrypt.php */
?>