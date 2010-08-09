<?php
/**
 * blog.php
 *
 * blog application controller
 *
 * @package	phpHyppo
 * @author	phpHyppo Application Builder
 */
 
class Blog_Controller extends AppController
{
	// loading up the require library & model
	public function beforeFilter()
	{
		// load uri library
		$this->load->library('uri');
		
		// load blog model alias as blog
		$this->load->model('blog_model', 'blog');		
	}
	
	// list all data
	public function index()
	{	
		// get data from database
		$content['pages'] = $this->blog->lists();
		
		// bind blog content to template & display
		$this->view->display('blog_list', $content);
	}

	// create / add new blog
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
			if ($this->blog->create($input))
				redirect('/blog/index');
		}
		
		// display form
		$this->view->display('blog_create');
	}

	// view detail blog
	public function view()
	{
		// get the specific page id
		$page_id = (int) $this->uri->segment('2');
		
		// get blog data from database
		$content = $this->blog->view($page_id);
		
		// bind blog content to template & display
		$this->view->display('blog_view', $content);
	}

	// update existing blog
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
			if ($this->blog->update($input))
				redirect('/blog/index');
		}
		
		// get specific page id
		$page_id = (int) $this->uri->segment('2');
		
		// get blog data from database
		$content = $this->blog->view($page_id);
		
		// bind blog data to form & display
		$this->view->display('blog_update', $content);
	}

	// remove existing blog 
	public function delete()
	{
		// get specific page id
		$page_id = (int) $this->uri->segment('2');
		
		// remove blog from database
		if ($this->blog->remove($page_id))
			redirect('/blog/index');
	}


}

/* End of blog.php */
/* Location: /application/controllers/blog.php */
?>