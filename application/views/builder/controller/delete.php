	/**
	 * remove or delete existing {controller}
	 *
	 * @access public
	 * @param int $id selected id to delete
	 * @return void
	 */
	public function delete($id)
	{
{acl_check}		// remove {controller} from database
		$this->{controller}->remove($id);
		redirect('/{controller}/index');
	}
