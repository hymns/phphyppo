	/**
	 * delete
	 *
	 * remove or delete existing {controller}
	 *
	 * @access public
	 * @return none
	 */
	public function delete(${tablename}_id)
	{
{acl_check}		// remove {controller} from database
		if ($this->{controller}->remove(${tablename}_id))
			redirect('/{controller}/index');
	}

