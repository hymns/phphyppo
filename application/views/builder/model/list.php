	/**
	 * lists
	 *
	 * lists data from {tablename}
	 *
	 * @access public
	 * @return array
	 */	
	public function lists()
	{
		// get all data from table {tablename}
		return $this->db->find_all('{tablename}');
	}

