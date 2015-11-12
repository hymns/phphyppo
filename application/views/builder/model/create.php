	/**
	 * create
	 *
	 * create / add new data
	 *
	 * @access public
	 * @params array $input
	 * @return integer
	 */	
	public function create($input)	
	{
		// insert new data to table {tablename}
		return $this->db->insert('{tablename}', $input);
	}