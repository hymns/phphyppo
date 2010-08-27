	// create / add new {controller}
	public function create()
	{
		// load input library
		$this->load->library('input');
		
		// get input data & filter it
		$input = $this->input->post('data', true);
		
		// input data exist
		if ($input !== false)
		{
			// insert new data to database
			if ($this->{controller}->create($input))
				redirect('/{controller}/index');
		}
		
		// display form
		$this->view->display('{controller}/create');
	}

