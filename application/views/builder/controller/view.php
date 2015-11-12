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
		
		// bind {controller} content to template & display
		$this->view->display('{controller}/view', $content);
	}

