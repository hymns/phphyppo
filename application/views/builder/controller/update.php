	/**
	 * update
	 *
	 * update existing {controller}
	 *
	 * @access public
	 * @return none
	 */
	public function update(${tablename}_id)
	{
{acl_check}		// load input library
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
		
		// get {controller} data from database
		$content = $this->{controller}->_data(${tablename}_id);
		
		// bind {controller} data to template
		$data['content'] = $this->view->fetch('{controller}/update', $content);

		// bind content to template
		$this->view->display('example_layout', $data);		
	}

