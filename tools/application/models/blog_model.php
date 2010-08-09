<?php
/**
 * blog_model.php
 *
 * blog application model
 *
 * @package	phpHyppo
 * @author	phpHyppo Application Builder
 */
 
class Blog_Model extends AppModel
{
	// list page
	public function lists()
	{
		// get all data from table page
		return $this->db->find_all('page');
	}

	// add new data
	public function create($input)
	{
		// insert new data to table page
		return $this->db->insert('page', $input);
	}

	// view data
	public function view($page_id)
	{
		// get only one specific data
		$this->db->where('id', $page_id);
		
		// from table page
		return $this->db->find('page');
	}

	// update data
	public function update($input)
	{
		// update only specific data
		$this->db->where('id', $input['id']);
		unset($input['id']);
		
		// update data on table page
		return $this->db->update('page', $input);
	}

	// remove specific data
	public function remove($page_id)
	{
		// remove only specific blog
		$this->db->where('id', $page_id);
		
		// from table page
		return $this->db->delete('page');
	}


}

/* End of blog_model.php */
/* Location: /application/models/blog_model.php */
?>