	/**
	 * delete / remove existing data
	 *
	 * @access public
	 * @param int $id the id of specific selected data
	 * @return integer
	 */
	public function remove($id)
	{
		return $this->db->where('{primary}', $id)
						->delete('{tablename}');
	}
