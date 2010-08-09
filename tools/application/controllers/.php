<?php
/**
 * .php
 *
 *  application controller
 *
 * @package	phpHyppo
 * @author	phpHyppo Application Builder
 */
 
class _Controller extends AppController
{
	// loading up the require library & model
	public function beforeFilter()
	{
		// load uri library
		$this->load->library('uri');
		
		// load  model alias as 
		$this->load->model('_model', '');		
	}
	
	// list all data
	public function index()
	{	
		// get data from database
		$content['pages'] = $this->->lists();
		
		// bind  content to template & display
		$this->view->display('_list', $content);
	}

	// create / add new 
	public function create()
	{
		// load input library
		$this->load->library('input');
		
		// get input data & filter it
		$input = $this->input->post('data', true);
		
		// input data exist
		if ($input !== false)
		{
			// insert new data to database
			if ($this->->create($input))
				redirect('//index');
		}
		
		// display form
		$this->view->display('_create');
	}

	// view detail 
	public function view()
	{
		// get the specific page id
		$page_id = (int) $this->uri->segment('2');
		
		// get  data from database
		$content = $this->->view($page_id);
		
		// bind  content to template & display
		$this->view->display('_view', $content);
	}

	// update existing 
	public function update()
	{
		// load input library
		$this->load->library('input');
		
		// get input data & filter it
		$input = $this->input->post('data', true);
		
		// data exists
		if ($input !== false)
		{
			// update data on database
			if ($this->->update($input))
				redirect('//index');
		}
		
		// get specific page id
		$page_id = (int) $this->uri->segment('2');
		
		// get  data from database
		$content = $this->->view($page_id);
		
		// bind  data to form & display
		$this->view->display('_update', $content);
	}

	// remove existing  
	public function delete()
	{
		// get specific page id
		$page_id = (int) $this->uri->segment('2');
		
		// remove  from database
		if ($this->->remove($page_id))
			redirect('//index');
	}


}

/* End of .php */
/* Location: /application/controllers/.php */
?>