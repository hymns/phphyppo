	// view detail {controller}
	public function view()
	{
		// get the specific {tablename} id
		${tablename}_id = (int) $this->uri->segment('2');
		
		// get {controller} data from database
		$content = $this->{controller}->view(${tablename}_id);
		
		// bind {controller} content to template & display
		$this->view->display('{controller}_view', $content);
	}

