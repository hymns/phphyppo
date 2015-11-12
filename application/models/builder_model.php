<?php
/**
 * builder_model.php
 *
 * application builder model
 *
 * @package	    	phpHyppo
 * @subpackage		phpHyppo Tools Application
 * @author			Muhammad Hamizi Jaminan
 */
class Builder_Model extends AppModel
{
	/**
	 * show_table
	 *
	 * get table list from database
	 *
	 * @access	public
	 * @return array
	 */
	public function show_table()
	{
		if ($this->db->db_type == 'pgsql')
			return $this->db->query_all('SELECT tablename FROM pg_tables WHERE tablename !~ \'^pg_+\' AND tablename !~ \'^sql_+\'');
		else
			return $this->db->query_all('SHOW TABLES');
	}
	
	/**
	 * get_config
	 *
	 * get public database configuration
	 *
	 * @access	public
	 * @param bool $tablename
	 * @return array
	 */
	public function get_config($tablename=false)
	{
		// get column name
		if ($tablename)
			$config['column_name'] = $this->db->db_type == 'mysql' ? 'Tables_in_' . $this->db->db_name : 'tablename';
		
		// standard config
		$config['db_prefix'] = $this->db->db_prefix;
		$config['db_type'] = $this->db->db_type;
		
		// return configuration
		return $config;
	}
	
	/**
	 * desc_table
	 *
	 * get table structure information
	 *
	 * @access public
	 * @param string $tablename
	 * @return array
	 */
	public function desc_table($tablename)
	{
		// pgsql database
		if ($this->db->db_type == 'pgsql')
			return $this->db->query_all('SELECT f.attname AS field, f.attnotnull AS null, pg_catalog.format_type(f.atttypid, f.atttypmod) AS type, 
										CASE WHEN p.contype = \'p\' THEN \'true\' ELSE \'false\' END AS primary, CASE WHEN f.atthasdef = \'t\' 
										THEN d.adsrc END AS extra FROM pg_attribute f JOIN pg_class c ON c.oid = f.attrelid JOIN pg_type t 
										ON t.oid = f.atttypid LEFT JOIN pg_attrdef d ON d.adrelid = c.oid AND d.adnum = f.attnum 
										LEFT JOIN pg_namespace n ON n.oid = c.relnamespace LEFT JOIN pg_constraint p ON p.conrelid = c.oid 
										AND f.attnum = ANY ( p.conkey ) LEFT JOIN pg_class AS g ON p.confrelid = g.oid WHERE c.relkind = \'r\'::char 
										AND n.nspname = \'public\' AND c.relname = \'' . $this->db->db_prefix . $tablename . '\' AND f.attnum > 0 
										ORDER BY f.attnum');
		
		// mysql database
		else
			return $this->db->query_all('DESC ' . $this->db->db_prefix . $tablename);
	}
}

/* End of builder_model.php */
/* Location: /application/models/builder_model.php */