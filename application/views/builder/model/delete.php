	/**
	 * remove
	 *
	 * delete / remove existing data
	 *
	 * @access public
	 * @param int ${tablename}_id
	 * @return integer
	 */
	public function remove(${tablename}_id)
	{
		// remove only specific {tablename}		
		$this->db->where('{primary}', ${tablename}_id);				
		
		// from table {tablename}
		return $this->db->delete('{tablename}');
	}

