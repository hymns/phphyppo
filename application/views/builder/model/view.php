	/**
	 * show selected detail data
	 *
	 * @access public
	 * @param int $id  the id of specific selected data
	 * @return array
	 */
	public function view($id)
	{		
		$this->db->where('{primary}', $id)
				 ->find('{tablename}');
	}

