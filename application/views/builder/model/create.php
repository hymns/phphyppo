	/**
	 * create / add new data
	 *
	 * @access public
	 * @param array $input data set to insert on db
	 * @return integer
	 */	
	public function create($input)	
	{
		return $this->db->insert('{tablename}', $input);
	}

