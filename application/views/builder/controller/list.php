	// list all data
	public function index()
	{	
		// get data from database
		$content['{tablename}s'] = $this->{controller}->lists();
		
		// bind {controller} content to template & display
		$this->view->display('{controller}/list', $content);
	}

