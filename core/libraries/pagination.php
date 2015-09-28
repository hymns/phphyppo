<?php
/**
 * phpHyppo
 *
 * An open source MVC application framework for PHP 5.1+
 *
 * @package			phpHyppo
 * @author			Muhammad Hamizi Jaminan <hymns@time.net.my>
 * @copyright		Copyright (c) 2008 - 2014, Green Apple Software.
 * @license			LGPL, see included license file
 * @link			http://www.phphyppo.org
 * @since			Version 8.06
 */

/* no direct access */
if (!defined('BASEDIR'))
	exit;

/**
 * pagination.php
 *
 * Class for pagination
 *
 * @package			phpHyppo
 * @subpackage		Shared Library
 * @author			Muhammad Hamizi Jaminan
 */

/**
 * Example Usage:
 *
 * $this->load->library('pagination', 'paging');
 *
 * $config['base_url'] = 'http://www.yourdomain.com/article/view/10/page/';
 * $config['uri_segment'] = 5;
 * $config['total_rows'] = 100;
 * $config['per_page'] = 10;
 *
 * $this->paging->initialize($config);
 *
 * echo $this->paging->create_links();
 */

class Pagination
{
	/**
	 * $base_url
	 *
	 * Variable for page we are linking to
	 *
	 * @access public
	 */
	var $base_url				= '';

	/**
	 * $uri_segment
	 *
	 * Segment of URI to get value records
	 *
	 * @access public
	 */
	var $uri_segment	    	= 3;

	/**
	 * $total_rows
	 *
	 * Total number of items (database result)
	 *
	 * @access public
	 */
	var $total_rows  			= '';

	/**
	 * $per_page
	 *
	 * Max number of items you want shown per page
	 *
	 * @access public
	 */
	var $per_page	 			= 15;

	/**
	 * $num_links
	 *
	 * Number of "digit" links to show before / after the currently viewed page
	 *
	 * @access public
	 */
	var $num_links		    	=  10;

	/**
	 * $cur_page
	 *
	 * The current page being viewed
	 *
	 * @access public
	 */
	var $cur_page	 			=  0;

	/**
	 *
	 * Misc variable for custom pagination output
	 *
	 * @access public
	 */
	var $first_link   			= '&lt;&lt; First';
	var $next_link				= 'Next &gt;';
	var $prev_link				= '&lt; Previous';
	var $last_link				= 'Last &gt;&gt;';
	var $full_tag_open  		= '';
	var $full_tag_close	   		= '';
	var $first_tag_open	   		= '';
	var $first_tag_close    	= '&nbsp;';
	var $last_tag_open	    	= '&nbsp;';
	var $last_tag_close	   		= '';
	var $cur_tag_open		    = '&nbsp;<b>';
	var $cur_tag_close		    = '</b>';
	var $next_tag_open	    	= '&nbsp;';
	var $next_tag_close	    	= '&nbsp;';
	var $prev_tag_open	    	= '&nbsp;';
	var $prev_tag_close	    	= '';
	var $num_tag_open	    	= '&nbsp;';
	var $num_tag_close	    	= '';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
	function __construct()
	{

	}

	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
	public function initialize($params = array())
	{
		if (count($params) > 0)
			foreach ($params as $key => $val)
				if (isset($this->$key))
					$this->$key = $val;
	}

	/**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @return	string
	 */
	public function create_links()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 || $this->per_page == 0)
		   return null;

		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
			return null;

		// Create URI registry
		$registry =& get_instance();
		$registry->load->library('uri');

		// Determine the current page number.
		if ($registry->uri->segment($this->uri_segment) != 0)
		{
			$this->cur_page = $registry->uri->segment($this->uri_segment);

			// Prep the current page - no funny business!
			$this->cur_page = (int) $this->cur_page;
		}

		$this->num_links = (int) $this->num_links;

		if ($this->num_links < 1)
			trigger_error('Your number of links must be a positive number.');

		if ( ! is_numeric($this->cur_page))
			$this->cur_page = 0;

		/*
		Is the page number beyond the result range?
		If so we show the last page
		*/
		if ($this->cur_page > $this->total_rows)
			$this->cur_page = ($num_pages - 1) * $this->per_page;

		$uri_page_number = $this->cur_page;
		$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

		/*
		Calculate the start and end numbers. These determine
		which number to start and end the digit links with
		*/
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		/*
		Is pagination being used over GET or POST?  If get, add a per_page query
		string. If post, add a trailing slash to the base URL if needed
		*/
		$this->base_url = rtrim($this->base_url, '/') .'/';

  		// And here we go...
		$output = '';

		// Render the "First" link
		if  ($this->cur_page > ($this->num_links + 1))
			$output .= $this->first_tag_open.'<a href="'.$this->base_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;

		// Render the "previous" link
		if  ($this->cur_page != 1)
		{
			$i = $uri_page_number - $this->per_page;
			if ($i == 0) $i = '';
			$output .= $this->prev_tag_open.'<a href="'.$this->base_url.$i.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		}

		// Write the digit links
		for ($loop = $start -1; $loop <= $end; $loop++)
		{
			$i = ($loop * $this->per_page) - $this->per_page;

			if ($i >= 0)
			{
				// Current page
				if ($this->cur_page == $loop)
					$output .= $this->cur_tag_open.$loop.$this->cur_tag_close;
				else
				{
					$n = ($i == 0) ? '' : $i;
					$output .= $this->num_tag_open.'<a href="'.$this->base_url.$n.'">'.$loop.'</a>'.$this->num_tag_close;
				}
			}
		}

		// Render the "next" link
		if ($this->cur_page < $num_pages)
			$output .= $this->next_tag_open.'<a href="'.$this->base_url.($this->cur_page * $this->per_page).'">'.$this->next_link.'</a>'.$this->next_tag_close;

		// Render the "Last" link
		if (($this->cur_page + $this->num_links) < $num_pages)
		{
			$i = (($num_pages * $this->per_page) - $this->per_page);
			$output .= $this->last_tag_open.'<a href="'.$this->base_url.$i.'">'.$this->last_link.'</a>'.$this->last_tag_close;
		}

		/*
		Kill double slashes.  Note: Sometimes we can end up with a double slash
		in the penultimate link so we'll kill all double slashes.
		*/
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;

		return $output;
	}
}

/* End of pagination.php */
/* Location: core/libraries/pagination.php */