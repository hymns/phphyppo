	/**
	 * list all data from {tablename}
	 *
	 * @access public
	 * @return array
	 */	
	public function lists()
	{
		return $this->db->find_all('{tablename}');
	}

