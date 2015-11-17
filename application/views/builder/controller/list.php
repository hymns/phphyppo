	/**
	 * index
	 *
	 * listing data {controller}
	 *
	 * @access public
	 * @return none
	 */
	public function index()
	{	
{acl_check}		// get data from database
		$content['{tablename}'] = $this->{controller}->lists();
		
		// capture {controller} content to template
		$data['content'] = $this->view->fetch('{controller}/list', $content);

		// bind content to template
		$this->view->display('example_layout', $data);
	}

