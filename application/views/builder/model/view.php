	// view data
	public function view(${tablename}_id)
	{
		// get only one specific data
		$this->db->where('{primary}', ${tablename}_id);
		
		// from table {tablename}
		return $this->db->find('{tablename}');
	}

