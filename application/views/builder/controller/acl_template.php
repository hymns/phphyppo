		// load authentication library
		$this->load->library('acl');

@@@@@		// check access role
		if ( ! $this->acl->is_allowed('{controller}', '{action}') )
			redirect('/session/login');

