	/**
	 * view
	 *
	 * get detail data
	 *
	 * @access private
	 * @params int ${tablename}_id
	 * @return array
	 */
	public function view(${tablename}_id)
	{		
		// get only one specific data		
		$this->db->where('{primary}', ${tablename}_id);
		
		// from table {tablename}
		return $this->db->find('{tablename}');	
	}

