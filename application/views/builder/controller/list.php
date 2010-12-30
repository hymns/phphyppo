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
		// get data from database
		$content['{tablename}'] = $this->{controller}->lists();
		
		// bind {controller} content to template & display
		$this->view->display('{controller}/list', $content);
	}

