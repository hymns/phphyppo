<?php
/**
 * apps.php
 *
 * application builder controller
 *
 * @package	    phpHyppo
 * @subpackage	phpHyppo Tools Application
 * @author			Muhammad Hamizi Jaminan
 */
 
class Apps_Controller extends AppController
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
		$this->load->library('input');
		
		// load tools model alias as tools
		$this->load->model('apps_model', 'apps');		
	}
	
	/**
	 * index
	 *
	 * show table list from database
	 *
	 * @access	public
	 */
	public function index()
	{	
		// get db config
		$content = $this->apps->get_config(true);
		
		// directory permission
		$content['permission_controller'] = is_writeable(APPDIR . 'controllers') ? 'ok' : 'ko';
		$content['permission_model'] = is_writeable(APPDIR . 'models') ? 'ok' : 'ko';
		$content['permission_view'] = is_writeable(APPDIR . 'views') ? 'ok' : 'ko';
		
		// get table lists from database
		$content['tables'] = $this->apps->show_table();
		
		// bind content to template & display
		$this->view->display('apps/index', $content);
	}
	
	/**
	 * model
	 *
	 * process selected database for application builder
	 *
	 * @access	public
	 */
	public function model()
	{
		// get model name
		$tablename = $this->uri->segment(2);
		
		// get db config
		$content = $this->apps->get_config();
		
		// show table details
		$content['fields'] = $this->apps->desc_table($tablename);
		
		// bind content to template & display
		$this->view->assign('tablename', $tablename);
		$this->view->display('apps/model', $content);
	}
	
	/**
	 * deploy
	 *
	 * function to generate controller, model & viewer
	 *
	 * @access	public
	 */
	public function deploy()
	{
		// get controller name & function list
		$controller = $this->input->post('controller_name', true);
		$actions = $this->input->post('action', true);
		
		// get field & primary key
		$model['tablename'] = $this->input->post('tablename', true);
		$model['fieldnames'] = $this->input->post('fieldname', true);
		$model['primary'] = $this->input->post('primary', true);
		$model['type'] = $this->input->post('type', true);
		
		// check ai field
		$autoincrement = $this->input->post('autoincrement', true);
		$model['autoincrement'] = $autoincrement !== false ? $autoincrement : null;
		
		// generate controller
		$this->_generate_controller($controller, $actions, $model);
		
		// generate model
		$this->_generate_model($controller, $actions, $model);
		
		// generate view
		$this->_generate_view($controller, $actions, $model); 
	}
	
	/**
	 * _generate_controller
	 *
	 * function to generate controller file
	 *
	 * @access	private
	 * @param	string $controller
	 * @param 	array $actions
	 * @param	array $model
	 */
	private function _generate_controller($controller, $actions, $model)
	{
		// action lists data
		$controller_action = '';
		
		// loop over action
		foreach($actions as $action)
		{
			// populate template action
			$data = file_get_contents(APPDIR . 'views' . DS . 'apps' . DS . 'controller' . DS . $action . '.php');
			$data = str_replace('{controller}',  $controller, $data);
			$data = str_replace('{tablename}',  $model['tablename'], $data);
			
			// assign controller action
			$controller_action .= $data;
		}
		
		// populate template controller
		$data = file_get_contents(APPDIR . 'views' . DS . 'apps' . DS . 'controller' . DS . 'controller.php');
		$data = str_replace('{controller}',  $controller, $data);
		$data = str_replace('{controller_class}',  ucwords($controller), $data);
		$data = str_replace('{controller_action}',  $controller_action, $data);
		
		// write controller to application directory
		$this->_writeout(APPDIR . 'controllers' . DS . $controller . '.php', $data);		
		
		// show controller generated output
		echo '<pre>' .htmlentities($data). '</pre>';
	}

	/**
	 * _generate_model
	 *
	 * function to generate model file
	 *
	 * @access	private
	 * @param	string $controller
	 * @param 	array $actions
	 * @param	array $model
	 */
	private function _generate_model($controller, $actions, $model)
	{
		// action lists data
		$model_action = '';
		
		// loop over action
		foreach($actions as $action)
		{
			// populate template action
			$data = file_get_contents(APPDIR . 'views' . DS . 'apps' . DS . 'model' . DS . $action . '.php');
			$data = str_replace('{controller}',  $controller, $data);
			$data = str_replace('{tablename}',  $model['tablename'], $data);
			$data = str_replace('{primary}',  $model['primary'], $data);
			
			// assign model action
			$model_action .= $data;
		}
		
		// populate template model
		$data = file_get_contents(APPDIR . 'views' . DS . 'apps' . DS . 'model' . DS . 'model.php');
		$data = str_replace('{controller}',  $controller, $data);
		$data = str_replace('{controller_class}',  ucwords($controller), $data);
		$data = str_replace('{model_action}',  $model_action, $data);
		
		// write model to application directory
		$this->_writeout(APPDIR . 'models' . DS . $controller . '_model.php', $data);		
		
		// show model generate output
		echo '<pre>' .htmlentities($data). '</pre>';
	}

	/**
	 * _generate_view
	 *
	 * function to generate view files
	 *
	 * @access	private
	 * @param	string $controller
	 * @param 	array $actions
	 * @param	array $model
	 */
	private function _generate_view($controller, $actions, $model)
	{
		// list over action
		foreach($actions as $action)
		{
			// skip for delete
			if ($action == 'delete')
				continue;
				
			// get fieldnames
			$fieldnames = $model['fieldnames'];
			$types = $model['type'];
			
			// get action template
			$data = file_get_contents(APPDIR . 'views' . DS . 'apps' . DS . 'viewer' . DS . $action . '.php');
			
			// listing action
			if ($action == 'list')
			{					
				// create header
				$header = "<tr>\n\t<th>Action</th>\n\t<th>" . implode("</th>\n\t<th>", $model['fieldnames']) . "</th>\n</tr>\n";
				
				// create row content
				$content = "<tr>\n\t<td>&nbsp;";
				
				// create add link
				$addlink = in_array('create', $actions) ? '<b><a href="<?php echo CONF_BASE_URL; ?>/{controller}/create">Add New</a></b>' : '';
				
				// create view link
				if (in_array('view', $actions))
					$content .= '<a href="<?php echo CONF_BASE_URL; ?>/' . $controller . '/view/<?php echo ${tablename}[\'' . $model['primary'] . '\']; ?>">View</a> ';
				
				// create update link
				if (in_array('update', $actions))
					$content .= '<a href="<?php echo CONF_BASE_URL; ?>/' . $controller . '/update/<?php echo ${tablename}[\'' . $model['primary'] . '\']; ?>">Update</a> ';
					
				// create delete link
				if (in_array('delete', $actions))
					$content .= '<a href="<?php echo CONF_BASE_URL; ?>/' . $controller . '/delete/<?php echo ${tablename}[\'' . $model['primary'] . '\']; ?>">Delete</a>';
					
				$content .= '</td>';
				
				// loop over fieldname
				foreach($fieldnames as $field)
					$content .= "\t<td><?php echo \$" . $model['tablename'] . "['" . $field . "']; ?></td>\n";
				
				// close row content
				$content .= "</tr>\n";
				
				// populate view template
				$data = str_replace('{tablehead}', $header, $data);
				$data = str_replace('{tabledata}', substr($content, 0, -1), $data);
				$data = str_replace('{addlink}', $addlink, $data);
				$data = str_replace('{controller}',  $controller, $data);
				$data = str_replace('{controller_class}',  ucwords($controller), $data);
				$data = str_replace('{tablename}',  $model['tablename'], $data);
			}
			
			// view action
			elseif ($action == 'view')
			{
				$content = '';
				
				// loop over fieldnames
				foreach($fieldnames as $field)
				{
					$content .= "<p><b>" . ucwords($field) . "</b><br />\n";
					$content .= "<?php echo \$" . $field . "; ?></p>\n\n";
				}
				
				// populate view template
				$data = str_replace('{content}',  substr($content, 0, -1), $data);					
				$data = str_replace('{controller}',  $controller, $data);
				$data = str_replace('{controller_class}',  ucwords($controller), $data);
			}
			
			// create action
			elseif ($action == 'create')
			{
				$content = '';
				
				// loop over fieldnames
				foreach($fieldnames as $field)
				{
					if (empty($model['autoincrement']) || $model['autoincrement'] != $field)
					{
						$content .= "\t<div id='label'>" . ucwords(str_replace('_', ' ', $field)) . "</div>\n";
						if (preg_match("/text/i", $types[$field]))
							$content .= "\t<div id='input'><textarea name='data[" . $field . "]' rows='5' cols='40'></textarea></div>\n\n";
						else
							$content .= "\t<div id='input'><input type='text' name='data[" . $field . "]' size='40'></div>\n\n";
					}
				}
				
				// populate view template
				$data = str_replace('{content}',  substr($content, 0, -1), $data);					
				$data = str_replace('{controller}',  $controller, $data);
				$data = str_replace('{controller_class}',  ucwords($controller), $data);
			}
			
			// update action
			elseif ($action == 'update')
			{
				$content = '';
				
				// loop over field
				foreach($fieldnames as $field)
				{
					// set hidden form for primary key
					if ($model['primary'] == $field)
						$content .= "<input type='hidden' name='data[" . $field . "]' value='<?php echo \$" . $field . "; ?>'>\n";
						
					// standard form
					else
					{
						$content .= "\t<div id='label'>" . ucwords(str_replace('_', ' ', $field)) . "</div>\n";
						if (preg_match("/text/i", $types[$field]))
							$content .= "\t<div id='input'><textarea name='data[" . $field . "]'  rows='5' cols='40'><?php echo htmlentities(\$" . $field . "); ?></textarea></div>\n\n";
						else
							$content .= "\t<div id='input'><input type='text' name='data[" . $field . "]' size='40' value='<?php echo \$" . $field . "; ?>'></div>\n\n";
					}
				}
				
				// populate view template
				$data = str_replace('{content}',  substr($content, 0, -1), $data);					
				$data = str_replace('{controller}',  $controller, $data);
				$data = str_replace('{controller_class}',  ucwords($controller), $data);
			}
			
			// write the viewer
			$this->_writeout(APPDIR . 'views' . DS . $controller . '_' . $action . '.php', $data);
		}
	}
	
	/**
	 * _writeout
	 *
	 * function to create file form the source code
	 *
	 * @access	private
	 * @param	string $filepath
	 * @param 	string $content
	 */
	private function _writeout($filepath, $content)
	{
		// create handler
		if (!$handler = @fopen($filepath, 'w+'))
			throw new Exception('Cannot open file (' . $filepath . ')', 500);
		
		// write data
		if (fwrite($handler, $content) === FALSE) 
			throw new Exception('Cannot write to file (' . $filepath . ')', 500);
		
		// close file
		fclose($handler);
	}
}

/* End of tools.php */
/* Location: /application/controllers/tools.php */
?>