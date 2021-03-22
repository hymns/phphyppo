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

/* no direct access */
if ( ! defined('BASEDIR') )
	exit;

/* define SQL actions */
if ( ! defined('SQL_NONE') )
	define('SQL_NONE', 0);

if ( ! defined('SQL_ONE') )
	define('SQL_ONE', 1);

if ( ! defined('SQL_ALL') )
	define('SQL_ALL', 2);

/**
 * ActiveRecord
 *
 * PDO database access
 * compile PHP with --enable-pdo (default with PHP 5.1+)
 * currently this library only support for mysql, postgres, sqlite & oracle
 *
 * @package			phpHyppo
 * @subpackage		Core DB Plugin
 * @author			Muhammad Hamizi Jaminan
 * @version			1.14.11
 */

class ActiveRecord
{
 	/**
	 * $pdo
	 *
	 * the PDO object handle
	 *
	 * @access	public
	 */
	var $pdo = null;

 	/**
	 * $db_name
	 *
	 * the database name
	 *
	 * @access	public
	 */
	var $db_name;

 	/**
	 * $db_type
	 *
	 * the database type
	 *
	 * @access	public
	 */
	var $db_type;

 	/**
	 * $db_prefix
	 *
	 * the table prefix
	 *
	 * @access	public
	 */
	var $db_prefix;

 	/**
	 * $db_namespace
	 *
	 * the table namespace
	 *
	 * @access	public
	 */
	var $db_namespace = 'public';
	
 	/**
	 * $result
	 *
	 * the query result handle
	 *
	 * @access	public
	 */
	var $result = null;
	
 	/**
	 * $fetch_mode
	 *
	 * the results fetch mode
	 *
	 * @access	public
	 */
	var $fetch_mode = PDO::FETCH_ASSOC;

 	/**
	 * $query_params
	 *
	 * @access	public
	 */
	var $query_params = array( 'select' => '*' );

 	/**
	 * $last_query
	 *
	 * @access	public
	 */
	var $last_query = null;

	/**
	 * $no_sequence
	 *
	 * @access public
	 */
	var $no_sequence = false;
	
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
	public function __construct( $pool )
	{
		// check pdo library first
		if ( ! class_exists( 'PDO', false ) )
			throw new Exception('PHP PDO package is required.', 200);
		
		// load database configuration
		include APPDIR . 'configs' . DS . 'database.php';
		
		// check database configuration
		if ( empty( $config ) )
			throw new Exception('Database definitions required.', 201);
		
		// set default character set
		if ( empty( $config['db_charset'] ) )
			$config['db_charset'] = 'UTF-8';
		
		// try to instantiate PDO object and database connection
		try
		{
			// oracle db
			if ( $config[$pool]['db_type'] == "oci" )
			{	
				// transparent network substrate
				if ( isset($config[$pool]['db_tns']) )
					$this->pdo = new PDO(
								$config[$pool]['db_type'] . ':dbname=' . $config[$pool]['db_tns'],
								$config[$pool]['db_user'],
								$config[$pool]['db_pass']
							);

				// standard connection
				else
					$this->pdo = new PDO(
								$config[$pool]['db_type'] . ':dbname=' . $config[$pool]['db_host'] .'/' . 
								$config[$pool]['db_name'],
								$config[$pool]['db_user'],
								$config[$pool]['db_pass']
							);
			}

			// other else
			else
			{
				$this->pdo = new PDO(
								$config[$pool]['db_type'] . ':host=' . $config[$pool]['db_host'] . ';dbname=' . $config[$pool]['db_name'],
								$config[$pool]['db_user'],
								$config[$pool]['db_pass']
							);
			}
			
			// exec character set
			if ( empty( $config['db_charset'] ) )			
				$this->pdo->exec('SET CHARACTER SET ' . $config['db_charset']);

			// exec session init
			if ( $config['db_mode_ofgb'] )
				$this->pdo->exec("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		}

		catch ( PDOException $e )
		{
			throw new Exception( sprintf( 'Can\'t connect to PDO database %s. Error: %s', $config[$pool]['db_type'], $e->getMessage() ), 202 );
		}
		
		// persistent connection (mysql persistent connection)
		if ( $this->db_type == 'mysql' )
			$this->pdo->setAttribute( PDO::ATTR_PERSISTENT, !empty($config['db_persistent'] ) ? true : false ); 
		
		// make PDO handle errors with exceptions
		$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		
		// set public vars for configs
		foreach( $config[$pool] as $key => $value )
			if ( in_array( $key, array( 'db_name', 'db_type', 'db_prefix', 'db_namespace' )) )
				$this->$key = $value;
		
		// tidy up resource
		unset( $config );
	}

	/**
	 * find
	 *
	 * get single result from database
	 *
	 * @access	public
	 * @param	string $tablename table name
	 * @param	string $columns table field name
	 */
	public function find( $tablename, $columns = null )
	{
		if ( empty( $tablename ) )
		{
			throw new Exception( 'Unable to select, table name required.', 210 );
			return false;
		}
		
		// check if specific is supply rather then all fields
		if ( ! empty( $columns ) )
			$this->select( $columns );
		
		// prepair for query assemble
		$this->from( $tablename );
		
		// return result
		return $this->query_one();
	}

	/**
	 * find_all
	 *
	 * get multiple result from database
	 *
	 * @access	public
	 * @param	string $tablename table name
	 * @param	string $columns table field name
	 */
	public function find_all( $tablename, $columns = null )
	{
		if ( empty( $tablename ) )
		{
			throw new Exception( 'Unable to select, table name required.', 210 );
			return false;
		}
		
		// prepair for query assemble
		$this->from( $tablename );
		
		// check if specific is supply rather then all fields
		if ( ! empty( $columns ) )
			$this->select( $columns );
		
		// return result
		return $this->query_all();
	}

	/**
	 * distinct
	 *
	 * get distinct record set from database
	 *
	 * @access	public
	 * @param	string $tablename table name
	 * @param	string $column_name table field name to distinct
	 */
	public function distinct( $tablename, $column_name = null )
	{
		if ( empty( $tablename ) )
		{
			throw new Exception( 'Unable to select distinct, table name required.', 210 );
			return false;
		}

		if ( ! empty( $column_name ) )
		{
			throw new Exception( 'Unable to select distinct, table name required.', 211 );
			return false;
		}

		$this->select( sprintf( 'DISTINCT %s', $column_name ) );
		$this->from( $tablename );		
		
		// return result
		return $this->query_all();
	}
	
	/**
	 * record_count
	 *
	 * get total record set from database
	 *
	 * @access	public
	 * @param	string $tablename table name
	 * @param	string $column_name table field name to count
	 */
	public function record_count( $tablename, $column_name = '*' )
	{
		if ( empty( $tablename ) )
		{
			throw new Exception( 'Unable to count record, table name required.', 210 );
			return false;
		}

		// patch for existing apps
		$old_version_compatible = explode( ' ', $tablename );
		
		// patch for existing apps
		if ( sizeof( $old_version_compatible ) > 1 )
		{
			$record = $this->_query( sprintf( 'SELECT COUNT(%s) AS total FROM %s', $column_name, $tablename ), null, SQL_ONE, null );
		}
		else
		{		
			if ( ! empty( $column_name ) )
				$this->select( sprintf( 'COUNT(%s) AS total', $column_name ) );
		
			$this->from( $tablename );		
			$record = $this->query_one();
		}
				
		// return result
		return $record['total'];
	}
	
	/**
	 * insert
	 *
	 * update records
	 *
	 * @access	public
	 * @param	string $tablename name of table
	 * @param	array  $columns array of new data
	 * @return	integer
	 */
	public function insert( $tablename, $columns )
	{
		if ( empty( $tablename ) )
		{
			throw new Exception( 'Unable to insert, table name required.', 210 );
			return false;
		}
		
		if ( empty( $columns ) || ! is_array($columns) )
		{
			throw new Exception( 'Unable to insert, at least one column required', 211 );
			return false;
		}
		
		// get column names & values
		$column_names = array_keys( $columns );
		$column_values = array_values( $columns );
		
		// insert statement
		$query = array( sprintf( 'INSERT INTO %s (%s) VALUES', $this->db_prefix . $tablename, implode( ',', $column_names ) ) );
		
		// join the fields values
		$query[] = '(' . rtrim( str_repeat( '?, ', sizeof( $column_names ) ), ', ') . ')';
		
		// merge the query array
		$query = implode( ' ', $query );
		
		// make query
		$this->_query( $query, $column_values );
		
		// return record id
		return $this->last_insert_id( $tablename );
	}

	/**
	 * update
	 *
	 * update records
	 *
	 * @access	public
	 * @param	string $tablename name of table
	 * @param	array  $columns array of new data
	 * @return	integer
	 */
	public function update( $tablename, $columns )
	{
		if ( empty( $tablename ) )
		{
			throw new Exception( 'Unable to update, table name required', 210 );
			return false;
		}
		
		if ( empty( $columns ) || ! is_array( $columns ) )
		{
			throw new Exception( 'Unable to update, at least one column required', 211 );
			return false;
		}
		
		// update statement
		$query = array( sprintf( 'UPDATE %s SET', $this->db_prefix . $tablename ) );
		
		// get column names & values
		$column_names = array_keys( $columns );
		$column_values = array_values( $columns );
		
		// join field array
		$query[] = implode( '=?, ', $column_names ) . '=?';
		
		// assemble where clause
		if ( $this->_assemble_where( $where_string, $where_params ) )
		{
			$query[] = $where_string;
			$column_values = array_merge( $column_values, $where_params );
		}
		
		// merge the query array
		$query = implode( ' ', $query );
		
		// reset default value
		$this->query_params = array( 'select' => '*' );
		
		// make query
		$this->_query( $query, $column_values );
		
		// return result
		return $this->affected_rows();
	}

	/**
	 * replace
	 *
	 * replace records for mysql
	 *
	 * @access	public
	 * @param	string $tablename name of table
	 * @param	array  $columns array of new data
	 * @return	integer
	 */
	public function replace( $tablename, $columns )
	{
		if ( $this->db_type != 'mysql')
			return false;
		
		if ( empty( $tablename ) )
		{
			throw new Exception( 'Unable to replace, table name required.', 210 );
			return false;
		}
		
		if ( empty( $columns ) || ! is_array($columns) )
		{
			throw new Exception( 'Unable to replace, at least one column required', 211 );
			return false;
		}
		
		// get column values
		$column_values = array_values( $columns );
		
		// insert statement
		$query = array( sprintf( 'REPLACE INTO %s VALUES', $this->db_prefix . $tablename ) );
		
		// join the fields values
		$query[] = '(' . rtrim( str_repeat( '?, ', sizeof( $column_values ) ), ', ') . ')';
		
		// merge the query array
		$query = implode( ' ', $query );
		
		// make query
		$this->_query( $query, $column_values );
		
		// return record id
		return $this->affected_rows();
	}

	/**
	 * delete
	 *
	 * delete records
	 *
	 * @access	public
	 * @param	string $tablename name of table
	 * @return	integer $id
	 */
	public function delete( $tablename )
	{
		if ( empty( $tablename ) )
		{
			throw new Exception( 'Unable to delete, table name required', 210 );
			return false;
		}
		
		// delete statement
		$query = array( sprintf( 'DELETE FROM %s', $this->db_prefix . $tablename ) );
		$params = array( );
		
		// assemble where clause
		if ( $this->_assemble_where( $where_string, $where_params ) )
		{
			$query[] = $where_string;
			$params = array_merge( $params, $where_params );
		}
		
		// merge the query array
		$query = implode( ' ', $query );
		
		// reset default value
		$this->query_params = array( 'select' => '*' );
		
		// make query
		$this->_query( $query, $params );
		
		// return result
		return $this->affected_rows();
	}

	/**
	 * select
	 *
	 * set the  active record select column names
	 *
	 * @access	public
	 * @param	string $column_names name of columns
	 */
	public function select( $column_names )
	{
		$this->query_params['select'] = $column_names;
		return $this;
	}

	/**
	 * from
	 *
	 * set the active record from table name
	 *
	 * @access	public
	 * @param	string $table_name
	 */
	public function from( $table_name )
	{
		$this->query_params['from'] = $this->db_prefix . $table_name;
		return $this;
	}

	/**
	 * where
	 *
	 * set the  active record where clause
	 *
	 * @access	public
	 * @param	mixed $clause
	 * @param	string $args
	 */
	public function where( $clause, $args = null )
	{
		if ( empty( $clause ) )
			throw new Exception( 'WHERE clause cannot be empty', 212 );
		
		if ( ! is_array( $clause ) )
			$clause = array($clause => $args);
		
		foreach( $clause as $field => $value )
		{
			$column_name = $field;
			$column_value = $value;
			
			if ( ! preg_match( '![=<>]!', $column_name ) AND strpos( $column_name, 'LIKE' ) === false )
				$column_name .= '=';
			
			if ( strpos( $column_name, '?' ) === false )
				$column_name .= '?';
			
			$this->_where( $column_name, (array) $column_value, 'AND' );
		}

		return $this;
	}

	/**
	 * or_where
	 *
	 * set the  active record or_where clause
	 *
	 * @access	public
	 * @param	mixed $clause
	 * @param	string $args
	 */
	public function or_where( $clause, $args )
	{
		if ( empty( $clause ) )
			throw new Exception( 'WHERE clause cannot be empty', 212 );
		
		if ( ! is_array( $clause ) )
			$clause = array( $clause => $args );
		
		foreach( $clause as $field => $value )
		{
			$column_name = $field;
			$column_value = $value;
			
			if ( ! preg_match( '![=<>]!', $column_name ) AND strpos( $column_name, 'LIKE' ) === false )
				$column_name .= '=';
			
			if ( strpos( $column_name, '?' ) === false )
				$column_name .= '?';
			
			$this->_where( $column_name, (array) $column_value, 'OR' );
		}

		return $this;
	}

	/**
	 * _where
	 *
	 * set the active record where clause
	 *
	 * @access	private
	 * @param	string $clause
	 * @param	array $args
	 * @param	string $prefix (optional)
	 */
	private function _where( $clause, $args = array(), $prefix = 'AND' )
	{
		// sanity check
		if ( empty( $clause ) )
			return false;
		
		// verify the question mark '?'  is match number of arguments
		if ( ( $count = substr_count( $clause, '?' ) ) && ( sizeof( $args ) != $count ) )
			throw new Exception( sprintf( 'Number of where clause args dont match number of ? : "%s"', $clause ), 213 );
		
		if ( ! isset( $this->query_params['where'] ) )
			$this->query_params['where'] = array();
		
		return $this->query_params['where'][] = array( 'clause' => $clause, 'args' => $args, 'prefix' => $prefix );
	}

	/**
	 * like
	 *
	 * set an active record LIKE clause
	 * 	WHERE ...some conditions... AND `age` LIKE 'abc%'
	 *
	 * @access	public
	 * @param	string $column_name
	 * @param	array $column_value
	 * @param	string $wildcard (optional) - first, both, last
	 */
	public function like( $column_name, $column_value, $wildcard = 'last' )
	{
   	    $this->_like( $column_name, $column_value, 'AND', $wildcard );

   	    return $this;
	}

	/**
	 * or_like
	 *
	 * set an active record LIKE clause
	 * 	WHERE ...some conditions... OR `age` LIKE 'abc%'
	 *
	 * @access	public
	 * @param	string $column_name
	 * @param	array $column_value
	 * @param	string $wildcard (optional)
	 */
	public function or_like( $column_name, $column_value, $wildcard = 'last' )
	{
	    $this->_like( $column_name, $column_value, 'OR', $wildcard );

	    return $this;
	}

	/**
	 * _like
	 *
	 * set an active record LIKE clause
	 * 	WHERE ...some conditions... OR `age` LIKE 'abc%'
	 *
	 * @access	private
	 * @param	string $column_name
	 * @param	array $column_value
	 * @param	string $prefix (optional)
	 * @param	string $wildcard (optional)
	 */
	private function _like( $column_name, $column_value, $prefix = 'AND', $wildcard = 'last')
	{
		$clause = sprintf( '%s LIKE ?', $column_name );
		
		switch( $wildcard )
		{
			case 'both':
			case 'BOTH':
				$this->_where( $clause, (array) ( '%' . $column_value . '%' ), $prefix );
			break;
			
			case 'first':
			case 'FIRST':
				$this->_where( $clause, (array) ( '%' . $column_value ), $prefix );
			break;
			
			default:
				$this->_where( $clause, (array) ( $column_value . '%' ), $prefix );
			break;
		}
	}

	/**
	 * is_null
	 *
	 * set an active record IS NULL clause
	 * 	WHERE ...some conditions... AND `phone` IS NULL
	 *
	 * @access	public
	 * @param	string $column_name
	 * @param   bool $is_not
	 */
	public function is_null( $column_name, $is_not = false)
	{
   	    $this->_is_null( $column_name, 'AND', $is_not );

   	    return $this;
	}

	/**
	 * or_is_null
	 *
	 * set an active record IS NULL clause
	 * 	WHERE ...some conditions... OR `phone` IS NULL
	 *
	 * @access	public
	 * @param	string $column_name
	 * @param   bool $is_not	 
	*/
	public function or_is_null( $column_name, $is_not = false)
	{
	    $this->_is_null( $column_name, 'OR', $is_not );

	    return $this;
	}

	/**
	 * _is_null
	 *
	 * set an active record IS NULL clause
	 * 	WHERE ...some conditions... OR `phone` IS NULL
	 *
	 * @access	private
	 * @param	string $column_name
	 * @param   bool $is_not
	 */
	private function _is_null( $column_name, $prefix = 'AND', $is_not = false)
	{
		$clause = ! $is_not ? sprintf( '%s IS NULL', $column_name ) : sprintf( '%s IS NOT NULL', $column_name );
		
		$this->_where( $clause, (array) (null), $prefix );
	}

	/**
	 * between
	 *
	 * set an active record BETWEEN clause
	 * 	WHERE ...some conditions... AND `age` BETWEEN 18 AND 25
	 *
	 * @access	public
	 * @param	string $column_name
	 * @param	array $value_range
	 * @param	string $prefix (optional)
	 */
	public function between( $column_name, $value_range, $prefix = 'AND' )
	{
		if ( sizeof( $value_range ) != 2 )
			throw new Exception( 'BETWEEN clause must have two range value', 214 );
		
		if ( $value_range[0] == $value_range[1] )
			throw new Exception( 'BETWEEN clause cannot have same range value', 215 );
		
		$clause = sprintf( '%s BETWEEN ? AND ?', $column_name );
		
		$this->_where( $clause, $value_range, $prefix );

		return $this;
	}

	/**
	 * in
	 *
	 * set an active record IN clause
	 *		WHERE ...some condition... AND `user_id` IN ('12','16','18')
	 *
	 * @access	public
	 * @param	string $column_name
	 * @param	mixed $elements
	 * @param	bool $list (optional)
	 */
	public function in( $column_name, $elements, $list = false )
	{
		$this->_in( $column_name, $elements, $list, 'AND' );

		return $this;
	}

	/**
	 * or_in
	 *
	 * set an active record OR IN clause
	 *		WHERE ...some condition... OR `user_id` IN ('12','16','18')
	 *
	 * @access	public
	 * @param	string $column_name
	 * @param	mixed $elements
	 * @param	bool $list (optional)
	 */
	public function or_in( $column_name, $elements, $list = false )
	{
		$this->_in( $column_name, $elements, $list, 'OR' );

		return $this;
	}

	/**
	 * _in
	 *
	 * set an active record IN clause
	 *
	 * @access	private
	 * @param	string $column_name
	 * @param	mixed $elements
	 * @param	bool $list (optional)
	 * @param	string $prefix (optional)
	 */
	private function _in( $column_name, $elements, $list = false, $prefix = 'AND' )
	{
		if ( ! $list )
		{
			if ( ! is_array( $elements ) )
				$elements = explode( ',', $elements );

			// quote elements for query
			foreach( $elements as $key => $element )
				$elements[$key] = $this->pdo->quote( $element );
			
			$clause = sprintf( '%s IN (%s)', $column_name, implode( ',', $elements ));
		}
		else
			$clause = sprintf( '%s IN (%s)', $column_name, $elements );
		
		$this->_where( $clause, array(), $prefix );
	}

	/**
	 * not_in
	 *
	 * set an active record IN clause
	 *		WHERE ...some condition... AND `user_id` NOT IN ('12','16','18')
	 *
	 * @access	public
	 * @param	string $column_name
	 * @param	mixed $elements
	 * @param	bool $list (optional)
	 */
	public function not_in( $column_name, $elements, $list = false )
	{
		$this->_not_in( $column_name, $elements, $list, 'AND' );

		return $this;
	}

	/**
	 * or_not_in
	 *
	 * set an active record OR IN clause
	 *		WHERE ...some condition... OR `user_id` NOT IN ('12','16','18')
	 *
	 * @access	public
	 * @param	string $column_name
	 * @param	mixed $elements
	 * @param	bool $list (optional)
	 */
	public function or_not_in( $column_name, $elements, $list = false )
	{
		$this->_not_in( $column_name, $elements, $list, 'OR' );

		return $this;
	}

	/**
	 * _not_in
	 *
	 * set an active record IN clause
	 *
	 * @access	private
	 * @param	string $column_name
	 * @param	mixed $elements
	 * @param	bool $list (optional)
	 * @param	string $prefix (optional)
	 */
	private function _not_in( $column_name, $elements, $list = false, $prefix = 'AND' )
	{
		if ( ! $list )
		{
			if ( ! is_array( $elements ) )
				$elements = explode( ',', $elements );

			// quote elements for query
			foreach( $elements as $key => $element )
				$elements[$key] = $this->pdo->quote( $element );
			
			$clause = sprintf( '%s NOT IN (%s)', $column_name, implode( ',', $elements ));
		}
		else
			$clause = sprintf( '%s NOT IN (%s)', $column_name, $elements );
		
		$this->_where( $clause, array(), $prefix );
	}

	/**
	 * join
	 *
	 * set the active record join clause
	 *  WHERE ...some conditions... INNER JOIN tablename ON ...join conditions...
	 *
	 * @access	public
	 * @param	string $join_table
	 * @param	string $join_on
	 * @param	string $join_type (optional)  LEFT/RIGHT INNER OR LEFT/RIGHT OUTER
	 */
	public function join( $join_table, $join_on, $join_type = null )
	{
		$join_condition = explode( '=', $join_on );
		
		// check join field
		if ( sizeof( $join_condition ) == 0 )
			throw new Exception( 'JOIN require table field and foreign key condition', 217 );
			
		$clause = 'JOIN ' . $this->db_prefix . $join_table . ' ON ' . $this->db_prefix . trim( $join_condition[0] ) . ' = ' . $this->db_prefix . trim( $join_condition[1] );
		
		if ( ! empty( $join_type ) )
			$clause = $join_type . ' ' . $clause;
		
		if ( ! isset( $this->query_params['join'] ) )
			$this->query_params['join'] = array();
		
		$this->query_params['join'][] = $clause;

		return $this;
	}

	/**
	 * group_by
	 *
	 * set the active record group by clause
	 *		WHERE ...some condition... GROUP BY `user_id`
	 *
	 * @access	public
	 * @param	string $clause
	 */
	public function group_by( $clause )
	{
		$this->_set_clause( 'group_by', $clause );

		return $this;
	}

	/**
	 * order_by
	 *
	 * set the active record order by clause
	 *		WHERE ...some condition... ORDER BY `user_id`
	 *
	 * @access	public
	 * @param	string $clause
	 */
	public function order_by( $clause )
	{
		if ( $this->db_type == 'pgsql' )
			$clause = str_replace('RAND()', 'RANDOM()', $clause);
			
		$this->_set_clause( 'order_by', $clause );

		return $this;
	}

	/**
	 * limit
	 *
	 * set the active record limit clause
	 *		WHERE ...some condition... LIMIT 5, 50
	 *
	 * @access	public
	 * @param	int $limit
	 * @param	int $offset (optional)
	 */
	public function limit( $limit, $offset = 0 )
	{
		if ( ! empty( $offset ) )
		{
			// postgres
			if ( $this->db_type == 'pgsql' )
				$this->_set_clause( 'limit', sprintf( '%d OFFSET %d', (int) $limit, (int) $offset ) );
			
			// mysql
			else
				$this->_set_clause( 'limit', sprintf( '%d, %d', (int) $offset, (int) $limit ) );
		}
		else
		{
			if ( $this->db_type == 'oci' ) 
				$this->_set_clause( 'ROWNUM <= ', sprintf( '%d', (int) $limit ) );
			
			else 
				$this->_set_clause( 'limit', sprintf( '%d', (int) $limit ) );
		}

		return $this;
	}

	/**
	 * query
	 *
	 * execute a database query
	 *
	 * @access	public
	 * @param	string $query query statement
	 * @param	array $params an array of query params
	 * @param	int $fetch_mode the fetch formatting mode
	 */
	public function query( $query = null, $params = null, $fetch_mode = null )
	{
		if ( ! isset( $query ) )
			$query = $this->_assemble_query( $params, $fetch_mode );
		
		return $this->_query( $query, $params, SQL_NONE, $fetch_mode );
	}

	/**
	 * query_one
	 *
	 * execute a database query, return one record
	 *
	 * @access	public
	 * @param	string $query query statement
	 * @param	array $params an array of query params
	 * @param	int $fetch_mode the fetch formatting mode
	 */
	public function query_one( $query = null, $params = null, $fetch_mode = null )
	{
		if ( ! isset( $query ) )
		{
			$this->limit(1);
			$query = $this->_assemble_query( $params, $fetch_mode );
		}
		
		return $this->_query( $query, $params, SQL_ONE, $fetch_mode );
	}

	/**
	 * query_all
	 *
	 * execute a database query, return all records
	 *
	 * @access	public
	 * @param	string $query query statement
	 * @param	array $params an array of query params
	 * @param	int $fetch_mode the fetch formatting mode
	 */
	public function query_all( $query = null, $params = null, $fetch_mode = null )
	{
		if ( ! isset( $query ) )
			$query = $this->_assemble_query( $params, $fetch_mode );
		
		return $this->_query( $query, $params, SQL_ALL, $fetch_mode );
	}

	/**
	 * next
	 *
	 * go to next record in result set
	 *
	 * @access	public
	 * @param	int $fetch_mode the fetch formatting mode
	 */
	public function next( $fetch_mode = null )
	{
		if ( isset( $fetch_mode ) )
			$this->result->setFetchMode( $fetch_mode );
		
		return $this->result->fetch();
	}

	/**
	 * last_insert_id
	 *
	 * get last insert id from previous query
	 *
	 * @access	public
	 * @param	int $tablename
	 * @return	int $seq_table
	 */
	public function last_insert_id( $tablename )
	{
		// defined db type
		switch( $this->db_type )
		{
			// mssql
			case 'dblib' :
				$result = $this->pdo->query( 'SELECT SCOPE_IDENTITY()' );
				return (int) $result->fetchColumn();			
			break;
			
			// postgres
			case 'pgsql' :
				if ( $this->no_sequence )
					return $this->pdo->lastInsertId();
				
				$sequence_table = $this->db_prefix . $tablename . '_id_seq';
				return $this->pdo->lastInsertId( $sequence_table );
			break;
			
			// oracle
			case 'oci' :
				return 0;
			break;
			
			// mysql
			default:
				return $this->pdo->lastInsertId();
			break;
		}
	}

	/**
	 * num_rows
	 *
	 * get number of returned rows from previous select
	 *
	 * @access	public
	 * @return	int $id
	 */
	public function num_rows()
	{
		return sizeof( $this->result->fetchAll() );
	}

	/**
	 * affected_rows
	 *
	 * get number of affected rows from previous insert/update/delete
	 *
	 * @access	public
	 * @return	int $id
	 */
	public function affected_rows()
	{
		return $this->result->rowCount();
	}

	/**
	 * last_query
	 *
	 * return last query executed
	 *
	 * @access	public
	 * @return	string
	 */
	public function last_query()
	{
		return $this->last_query;
	}

	/**
	 * begin_transaction
	 *
	 * Initiates a transaction 
	 *
	 * @access	public
	 * @return	boll
	 */
	public function begin_transaction()
	{
		return $this->pdo->beginTransaction();
	}

	/**
	 * commit
	 *
	 * Commits a transaction 
	 *
	 * @access	public
	 * @return	bool
	 */
	public function commit()
	{
		return $this->pdo->commit();
	}

	/**
	 * rollback
	 *
	 * Rolls back a transaction 
	 *
	 * @access	public
	 * @return	bool
	 */
	public function rollback()
	{
		return $this->pdo->rollBack();
	}

	/**
	 * _assemble_where
	 *
	 * assemble where query
	 *
	 * @access	private
	 * @param	string $where
	 * @param	array $params
	 * @return	string
	 */
	private function _assemble_where( &$where, &$params )
	{
		if ( ! empty( $this->query_params['where'] ) )
		{
			$where_init = false;
			$where_parts = array();
			$params = array();
			
			foreach( $this->query_params['where'] as $key )
			{
				$prefix = ! $where_init ? 'WHERE' : $key['prefix'];
				$where_parts[] = $prefix . ' ' . $key['clause'];
				$params = array_merge( $params, (array) $key['args'] );
				$where_init = true;
			}
			
			$where = implode( ' ', $where_parts );
			
			return true;
		}
		
		return false;
	}

	/**
	 * _assemble_query
	 *
	 * get an active record query
	 *
	 * @access	private
	 * @param	array $params
	 * @param	string $fetch_mode the PDO fetch mode
	 * @return	string
	 */
	private function _assemble_query( &$params, $fetch_mode = null )
	{
		// verify target table
		if ( empty($this->query_params['from']) )
		{
			throw new Exception( 'Unable to query(), no tablename defined', 210 );
			return false;
		}
		
		// prepair default data assemble
		$query = array();
		$query[] = 'SELECT ' . $this->query_params['select'];
		$query[] = 'FROM ' . $this->query_params['from'];
		
		// assemble JOIN clause
		if ( ! empty( $this->query_params['join'] ) )
			foreach( $this->query_params['join'] as $join )
				$query[] = $join;
		
		// assemble WHERE clause
		if ( $where = $this->_assemble_where( $where_string, $params ) )
			$query[] = $where_string;
		
		// assemble GROUP BY clause
		if ( ! empty( $this->query_params['group_by'] ) )
			$query[] = 'GROUP BY ' . $this->query_params['group_by']['clause'];
		
		// assemble ORDER BY clause
		if ( ! empty( $this->query_params['order_by'] ) )
			$query[] = 'ORDER BY ' . $this->query_params['order_by']['clause'];
		
		// assemble LIMIT clause
		if ( ! empty( $this->query_params['limit'] ) )
		{
			if ( $this->db_type == 'oci' ) 
				$query[] = 'ROWNUM = ' . $this->query_params['limit']['clause'];
			else 
				$query[] = 'LIMIT ' . $this->query_params['limit']['clause'];
		}
		
		// merge the query array
		$query_string = implode( ' ', $query );

		// set last query
		$this->last_query = $query_string;
		$this->last_params = $this->query_params;
		
		// reset default value
		$this->query_params = array( 'select' => '*' );
		
		// return assemble query
		return $query_string;
	}

	/**
	 * procedure_query
	 *
	 * store procedure query method
	 *
	 * @access	public
	 * @param	string $query the query string
	 * @param	array $params an array of query params
	 * @param	int $return_type none/all/init
	 * @param	int $fetch_mode the fetch formatting mode
	 */
	public function procedure_query( $query, $params = array(), $return_type = SQL_ONE, $fetch_mode = null )
	{
		// if no fetch mode, use default
		if ( ! isset( $fetch_mode ) )
			$fetch_mode = PDO::FETCH_ASSOC;
		
		// prepare the query
		try
		{
			$this->result = $this->pdo->prepare( $query );
		}
		catch ( PDOException $e )
		{
			throw new Exception( sprintf( 'PDO Error: %s Query: %s', $e->getMessage(), $query), 216 );
			return false;
		}
		
		// execute with parameters
		try		
		{
			// bind parameter
			if ( ! empty($params) )
			{
				$i = 1;

				foreach ($params as $value) 
				{					
					if ( is_integer($value) )
						$this->result->bindParam($i, $value, PDO::PARAM_INT);
					else
						$this->result->bindParam($i, $value, PDO::PARAM_STR);

					$i++;
				}
			}

			// executed
			$this->result->execute();
		}
		catch ( PDOException $e )
		{
			throw new Exception( sprintf( 'PDO Error: %s Query: %s', $e->getMessage(), $query), 216 );
			return false;
		}
		
		// set result fetch mode
		$this->result->setFetchMode( $fetch_mode );
		
		switch ( $return_type )
		{
			case SQL_ONE:
				return $this->result->fetch();
			break;
			
			case SQL_ALL:
				return $this->result->fetchAll();
			break;
			
			case SQL_NONE:
			default:
				return true;
			break;
		}
	}

	/**
	 * _query
	 *
	 * internal query method
	 *
	 * @access	private
	 * @param	string $query the query string
	 * @param	array $params an array of query params
	 * @param	int $return_type none/all/init
	 * @param	int $fetch_mode the fetch formatting mode
	 */
	private function _query( $query, $params = null, $return_type = SQL_NONE, $fetch_mode = null )
	{
		// if no fetch mode, use default
		if ( ! isset( $fetch_mode ) )
			$fetch_mode = PDO::FETCH_ASSOC;
		
		// prepare the query
		try
		{
			$this->result = $this->pdo->prepare( $query );
		}
		catch ( PDOException $e )
		{
			throw new Exception( sprintf( 'PDO Error: %s Query: %s', $e->getMessage(), $query), 216 );
			return false;
		}
		
		// execute with parameters
		try
		{
			$this->result->execute( $params );
		}
		catch ( PDOException $e )
		{
			throw new Exception( sprintf( 'PDO Error: %s Query: %s', $e->getMessage(), $query), 216 );
			return false;
		}
		
		// set result fetch mode
		$this->result->setFetchMode( $fetch_mode );
		
		switch ( $return_type )
		{
			case SQL_ONE:
				return $this->result->fetch();
			break;
			
			case SQL_ALL:
				return $this->result->fetchAll();
			break;
			
			case SQL_NONE:
			default:
				return true;
			break;
		}
	}

	/**
	 * _set_clause
	 *
	 * set an active record clause
	 *
	 * @access	private
	 * @param	string $type
	 * @param	string $clause
	 * @param	array $args
	 */
	private function _set_clause( $type, $clause, $args = array() )
	{
		// sanity check
		if ( empty( $type ) || empty( $clause ) )
			return false;
		
		$this->query_params[$type] = array( 'clause' => $clause );
		
		if ( isset( $args ) )
			$this->query_params[$type]['args'] = $args;
	}

	/**
	 * class destructor
	 *
	 * @access	public
	 */
	public function __destruct()
	{
		$this->pdo = null;
	}
}

/* End of db.ActiveRecord.php */
/* Location: core/plugins/db.ActiveRecord.php */