<?php
/**
 * {controller}.php
 *
 * {controller} application controller
 *
 * @package	phpHyppo
 * @author	phpHyppo Application Builder
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
		// load uri library
		$this->load->library('uri');
		
		// load {controller} model alias as {controller}
		$this->load->model('{controller}_model', '{controller}');		
	}
	
{controller_action}
}

/* End of {controller}.php */
/* Location: /application/controllers/{controller}.php */
?>