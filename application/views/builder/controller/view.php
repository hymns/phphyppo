	/**
	 * view
	 *
	 * show / view details {controller}
	 *
	 * @access public
	 * @return none
	 */
	public function view(${tablename}_id)
	{
{acl_check}		// get {controller} data from database
		$content = $this->{controller}->view(${tablename}_id);
		
		// bind {controller} content to template
		$data['content'] = $this->view->fetch('{controller}/view', $content);

		// bind content to template
		$this->view->display('example_layout', $data);				
	}

