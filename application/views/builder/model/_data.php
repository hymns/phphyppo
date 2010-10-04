	/**
	 * _data
	 *
	 * get detail data
	 *
	 * @access private
	 * @return array
	 */
	public function _data(${tablename}_id)
	{
		// get only one specific data
		$this->db->where('{primary}', ${tablename}_id);
		
		// from table {tablename}
		return $this->db->find('{tablename}');
	}

