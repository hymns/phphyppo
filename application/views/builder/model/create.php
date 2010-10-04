	/**
	 * index
	 *
	 * create / add new data
	 *
	 * @access public
	 * @return integer
	 */
	public function create($input)
	{
		// insert new data to table {tablename}
		return $this->db->insert('{tablename}', $input);
	}

