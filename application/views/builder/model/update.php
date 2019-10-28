	/**
	 * update or change data on database
	 *
	 * @access public	 
	 * @param array $input 	new data for updating
	 * @param array $update condition for update
	 * @return integer
	 */	
	public function update($input, $update)
	{
		return $this->db->where($update)
						->update('{tablename}', $input);
	}

