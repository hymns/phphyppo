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

/**
 * database.php
 *
 * application database configuration
 *
 * @package	    	phpHyppo
 * @subpackage		Application Configuration
 * @author			Muhammad Hamizi Jaminan
 */

/* database engine plugin */
$config['db_plugin'] 	= 'ActiveRecord';

/* database connection persistence */
$config['db_persistent'] = false;

/* database encoding character set */
$config['db_use_charset'] = true;
$config['db_charset'] 	= 'UTF-8';
$config['db_mode_ofgb'] = false;

/* database default load */
$config['db_default'] = 'development';

/* database development information */
$config['development']['db_host'] 		= 'localhost';
$config['development']['db_type'] 		= 'mysql';  /* (mysql, pgsql, sqlite, dblib) */
$config['development']['db_namespace'] 	= ''; 		/* pgsql only (default: public) */
$config['development']['db_name'] 		= 'demo';
$config['development']['db_user'] 		= 'demo';
$config['development']['db_pass'] 		= 'demo';
$config['development']['db_prefix'] 	= '';

/* database production information */
$config['production']['db_host'] 		= 'localhost';
$config['production']['db_type'] 		= 'mysql'; 	/* (mysql, pgsql, sqlite, dblib) */
$config['production']['db_namespace'] 	= ''; 		/* pgsql only (default: public) */
$config['production']['db_name'] 		= 'demo';
$config['production']['db_user'] 		= 'demo';
$config['production']['db_pass'] 		= 'demo';
$config['production']['db_prefix'] 		= '';

/* database oracle information */
$config['oracle_db']['db_host'] 		= '10.0.0.10:1521';
$config['oracle_db']['db_type'] 		= 'oci';
$config['oracle_db']['db_name'] 		= '';
$config['oracle_db']['db_tns']			= '(DESCRIPTION =
										    (ADDRESS = 
										      (PROTOCOL = TCP)
										      (HOST = 10.0.0.10)
										      (PORT = 1521)
										    )
										    (CONNECT_DATA = 
										      (SERVER = DEDICATED)
										      (SERVICE_NAME = demo)
										    )
										  )';
$config['oracle_db']['db_user'] 		= 'demo';
$config['oracle_db']['db_pass'] 		= 'demo';
$config['oracle_db']['db_prefix'] 		= '';
