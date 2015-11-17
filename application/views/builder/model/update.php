	/**
	 * update
	 *
	 * update or change data on database
	 *
	 * @access public	 
	 * @param array $input 	 
	 * @param array $update 
	 * @return integer
	 */	
	public function update($input, $update)
	{
		// update only specific data		
		$this->db->where($update);
		
		// update data on table {tablename}
		return $this->db->update('{tablename}', $input);
	}

