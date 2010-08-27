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
	// loading up the require library & model
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