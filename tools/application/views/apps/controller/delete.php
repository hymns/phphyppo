	// remove existing {controller} 
	public function delete()
	{
		// get specific {tablename} id
		${tablename}_id = (int) $this->uri->segment('2');
		
		// remove {controller} from database
		if ($this->{controller}->remove(${tablename}_id))
			redirect('/{controller}/index');
	}

