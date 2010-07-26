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
 * AppLoader
 *
 * Class that manage to load models, libraries and helpers
 *
 * @package	    phpHyppo
 * @subpackage	Core Engine
 * @author			Muhammad Hamizi Jaminan
 */

class AppLoader
{
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{

	}

	/**
	 * database
	 *
	 * load database plugin
	 *
	 * @access public
	 * @return object
	 */
	public function database()
	{
		static $db = null;
		
		// return existing handler
		if (isset($db) && !is_null($db))
			return $db;
		
		// load database configuration
		include APPDIR . 'configs' . DS . 'database.php';
		
		// validate database plugin
		if (!empty($config['db_plugin']))
		{
			// prepair database filename & path
			$filename = 'db.' . $config['db_plugin'] . '.php';
			$filepath = BASEDIR . 'core' . DS . 'plugins' . DS . $filename;
			
			// validate database plugin path on core plugins
			if (!file_exists($filepath))
				$filepath = APPDIR . 'plugins' . DS . $filename;
			
			// validate database plugin path on application plugins
			if (!file_exists($filepath))
				throw new Exception($config['db_plugin'], 203);
			
			// load database plugin
			require_once $filepath;
			
			// validate database plugin classname
			if (!class_exists($config['db_plugin']))
				throw new Exception($config['db_plugin'], 204);
			
			// assign the object instance as a property
			$db = new $config['db_plugin'];
			
			return $db;
		}
	}

	/**
	 * helper
	 *
	 * load a helper script
	 *
	 * @access	public
	 * @param	string $helper the helper script name
	 * @return	boolean
	 */
	public function helper($helper)
	{
		// check valid helper name
		if (!preg_match('!^[a-zA-Z][a-zA-Z_]+$!', $helper))
			throw new Exception('Helper name \'' . $helper . '\' is invalid format', 500);
		
		// set the file path
		$filepath = APPDIR . 'helpers' . DS . $helper . '.php';
		
		// applications helpers
		if (!file_exists($filepath))
			$filepath = BASEDIR . 'core' . DS . 'helpers' . DS . $helper . '.php';
		
		// core helpers
		if (!file_exists($filepath))
			throw new Exception($filename, 501);
		
		return require_once $filepath;
	}

	/**
	 * library
	 *
	 * load a library class
	 *
	 * @access	public
	 * @param	string $class_name the class name
	 * @param	string $alias_name the property name alias
	 * @param	string $filename the filename
	 * @return	boolean
	 */
	public function library($class_name, $alias_name=null, $filename=null)
	{
		// if no alias name,  use the class name
		if (!isset($alias_name))
			$alias_name = $class_name;
		
		// final check for library alias
		if (empty($alias_name))
			throw new Exception('Library name cannot be empty', 400);
		
		// check valid alias name
		if (!preg_match('!^[a-zA-Z][a-zA-Z_]+$!', $alias_name))
			throw new Exception('Library name \'' . $alias_name . '\' is an invalid format', 401);
		
		// check reserved alias
		if (method_exists($this, $alias_name))
			throw new Exception('Library name \'' . $alias_name . '\' is an invalid (reserved) name', 402);
		
		// library already loaded? skip
		if (isset($this->$alias_name))
			return true;
		
		// if no class exists, attempt to load library
		if (!class_exists($class_name))
		{
			// if no filename, use the class name
			if (!isset($filename))
				$filename = $class_name;
			
			$filepath = APPDIR . 'libraries' . DS . $filename . '.php';
			
			// applications libraries
			if (!file_exists($filepath))
				$filepath = BASEDIR . 'core' . DS . 'libraries' . DS . $filename . '.php';
			
			// core libraries
			if (!file_exists($filepath))
    			throw new Exception($class_name, 403);
			
			// load library engine
			require_once $filepath;
			
			if (!class_exists($class_name))
    			throw new Exception($class_name, 404);
		}
		
		// get instance of registry object
		$registry = registry::instance();
		
		// instantiate the object as a property
		$registry->$alias_name = new $class_name();
		
		return true;
	}

	/**
	 * model
	 *
	 * load a model object
	 *
	 * @access	public
	 * @param	string $model_name the name of the model class
	 * @param	string $alias_name the property name alias
	 * @param	string $filename the filename
	 * @return	boolean
	 */
	public function model($model_name, $alias_name=null, $filename=null)
	{
		// if no alias,  use the model name
		if (!isset($alias_name))
			$alias_name = $model_name;
		
		// final check for model alias
		if (empty($alias_name))
			throw new Exception('Model name cannot be empty', 205);
		
		// check valid alias name
		if (!preg_match('!^[a-zA-Z][a-zA-Z_]+$!', $alias_name))
			throw new Exception('Model name \'' . $alias_name . '\' is an invalid format', 206);
		
		// check reserved alias name
		if (method_exists($this, $alias_name))
			throw new Exception('Model name \'' . $alias_name . '\' is an invalid (reserved) name', 207);
		
		// model already loaded? skip
		if (isset($this->$alias_name))
			return true;
		
		// if no filename, use the lower-case model name
		if (!isset($filename))
            $filename = strtolower($model_name);
		
		// assign application model path
		$filepath = APPDIR . 'models' . DS . $filename . '.php';
		
		// check application model path
		if (!file_exists($filepath))
			throw new Exception($model_name, 208);
		
		// no problem, load application model class
		require_once $filepath;
		
		// class name must be the same as the model name
		if (!class_exists($model_name))
			throw new Exception($model_name, 209);
		
		// get instance of registry object
		$registry = registry::instance();
		
		// instantiate the object as a property
		$registry->$alias_name = new $model_name();
		
		return true;
	}

}

/* End of file AppLoader.php */
/* Location: core/classes/AppLoader.php */
?>