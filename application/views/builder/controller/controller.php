<?php
/**
 * {controller}.php
 *
 * {controller} application controller
 *
 * @package		phpHyppo
 * @author		phpHyppo Application Builder
 * @license		LGPL, see included license file
 * @link		http://www.phphyppo.org
 * @since		Version 8.02
 */
 
class {controller_class}_Controller extends AppController
{
	/**
	 * beforeFilter
	 *
	 * load library and database before event execution
	 *
	 * @access	public
	 */
	public function beforeFilter()
	{
{acl_load}		// load uri library
		$this->load->library('uri');
		
		// load {controller} model alias as {controller}
		$this->load->model('{controller}_model', '{controller}');		
	}
	
{controller_action}
}

/* End of {controller}.php */
/* Location: /application/controllers/{controller}.php */