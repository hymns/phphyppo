	// add new data
	public function create($input)
	{
		// insert new data to table {tablename}
		return $this->db->insert('{tablename}', $input);
	}

