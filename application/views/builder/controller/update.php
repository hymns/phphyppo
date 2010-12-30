	/**
	 * update
	 *
	 * update existing {controller}
	 *
	 * @access public
	 * @return none
	 */
	public function update()
	{
		// load input library
		$this->load->library('input');
		
		// get input data & filter it
		$input = $this->input->post('data', true);
		
		// data exists
		if ($input !== false)
		{
			// get specific update
			$update = $this->input->post('update', true);
			
			// update data on database
			if ($this->{controller}->update($input, $update))
				redirect('/{controller}/index');
		}
		
		// get specific {tablename} id
		${tablename}_id = (int) $this->uri->segment('2');
		
		// get {controller} data from database
		$content = $this->{controller}->_data(${tablename}_id);
		
		// bind {controller} data to form & display
		$this->view->display('{controller}/update', $content);
	}

