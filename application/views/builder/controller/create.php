	/**
	 * create
	 *
	 * create / add new {controller}
	 *
	 * @access public
	 * @return void
	 */
	public function create()
	{	
{acl_check}		// load input library
		$this->load->library('input');
		
		// get input data & filter it
		$input = $this->input->post('data', true);
		
		// input data exist
		if ( $input !== false )
		{
			// insert new data to database
			if ( $this->{controller}->create($input) )
				redirect('/{controller}/index');
		}
		
		// capture form
		$data['content'] = $this->view->fetch('{controller}/create');

		// bind content to template
		$this->view->display('example_layout', $data);		
	}

