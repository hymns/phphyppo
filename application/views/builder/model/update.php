	/**
	 * update
	 *
	 * update or change data on database
	 *
	 * @access public
	 * @return integer
	 */
	public function update($input)
	{
		// update only specific data
		$this->db->where('{primary}', $input['{primary}']);
		unset($input['{primary}']);
		
		// update data on table {tablename}
		return $this->db->update('{tablename}', $input);
	}

