	// list {tablename}
	public function lists()
	{
		// get all data from table {tablename}
		return $this->db->find_all('{tablename}');
	}

