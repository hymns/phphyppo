	/**
	 * delete
	 *
	 * remove or delete existing {controller}
	 *
	 * @access public
	 * @return none
	 */
	public function delete()
	{
		// get specific {tablename} id
		${tablename}_id = (int) $this->uri->segment('2');
		
		// remove {controller} from database
		if ($this->{controller}->remove(${tablename}_id))
			redirect('/{controller}/index');
	}

