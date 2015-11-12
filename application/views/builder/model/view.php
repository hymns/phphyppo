	/**
	 * view
	 *
	 * view / show data from table
	 *
	 * @access public	 
	 * @param int ${tablename}_id
	 * @return array
	 */
	public function view(${tablename}_id)	
	{
		// get data details
		return $this->_data(${tablename}_id);
	}