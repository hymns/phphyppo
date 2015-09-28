		// load authentication library
		$this->load->library('authentication', 'auth');

@@@@@		// check authentication
		if (!$this->auth->is_logged_in())
			redirect('/users/login');

