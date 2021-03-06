<?php
/**
 * builder.php
 *
 * application builder controller
 *
 * @package	    	phpHyppo
 * @subpackage		phpHyppo Application Builder
 * @author			Muhammad Hamizi Jaminan <hymns@time.net.my>
 * @copyright		Copyright (c) 2008 - 2014, Green Apple Software.
 * @license			LGPL, see included license file
 * @link			http://www.phphyppo.org
 * @since			Version 11.2
 */
 
class Builder_Controller extends AppController
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
		// load uri & input library
		$this->load->library('uri');
		$this->load->library('input');
		$this->load->library('acl');
		
		// load builder model alias as builder
		$this->load->model('builder_model', 'builder');		
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
		$content = $this->builder->get_config(true);
		
		// directory permission
		$content['permission_controller'] = is_writeable(APPDIR . 'controllers') ? 'ok' : 'ko';
		$content['permission_model'] = is_writeable(APPDIR . 'models') ? 'ok' : 'ko';
		$content['permission_view'] = is_writeable(APPDIR . 'views') ? 'ok' : 'ko';
		
		// get table lists from database
		$content['tables'] = $this->builder->show_table();
		
		// bind content to template & display
		$this->view->display('builder/index', $content);
	}
	
	/**
	 * model
	 *
	 * process selected database for application builder
	 *
	 * @access	public
	 */
	public function model($tablename)
	{
		// get model name
		$tablename = $this->input->xss_clean($tablename);
		
		// get db config
		$content = $this->builder->get_config();
		
		// show table details
		$content['fields'] = $this->builder->desc_table($tablename);
		
		// bind content to template & display
		$this->view->assign('tablename', $tablename);
		$this->view->display('builder/model', $content);
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
		$acl = $this->input->post('acl', true);
		
		// default controller name
		if ( empty($controller) )
			exit('Please enter controller name (characters only)');
		
		// check reserve name
		if ( in_array($controller, array('builder', 'session')) )
			exit('Cannot use reserved controller name: ' . $controller);
		
		// default value for action
		if ( empty($actions) )
			$actions = array('list', 'create');
		
		// set default acl value
		if ( $acl === false )
			$acl = array();
		
		// build acl
		else
		{
			$this->acl->initiate();
		}
		
		// filter out
		$controller = preg_replace('!\W!', '', $controller);
		
		// get field & primary key
		$model['tablename'] = $this->input->post('tablename', true);
		$model['fieldnames'] = $this->input->post('fieldname', true);
		$model['primary'] = $this->input->post('primary', true);
		$model['type'] = $this->input->post('type', true);
		
		// check auto increment field
		$autoincrement = $this->input->post('autoincrement', true);
		$model['autoincrement'] = $autoincrement !== false ? $autoincrement : null;

		// get db config
		$content = $this->builder->get_config();

		// show output
		$this->view->display('builder/deploy', $content);
		
		// generate controller, model, view & roles
		$this->_generate_controller($controller, $actions, $model, $acl);
		$this->_generate_model($controller, $actions, $model);
		$this->_generate_view($controller, $actions, $model); 
		$this->_generate_role($controller, $actions, $acl); 

		// show notice
		echo '<br>Your application has been successfully deploy! Access your <a href="' . CONF_BASE_PATH . '/' . $controller . '" target="_blank">application here</a> or build another <a href="' . CONF_BASE_PATH . '/builder">here</a>.';
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
	private function _generate_controller($controller, $actions, $model, $acl)
	{
		// action lists data
		$controller_action = '';
		$acl_load = '';
		
		// acl trigger
		if ( ! empty($acl) AND sizeof($acl) > 0 )
		{
			$acl_template = file_get_contents(APPDIR . 'views' . DS . 'builder' . DS . 'controller' . DS . 'acl_template.php');
			$acl_template = explode('@@@@@', $acl_template);
			$acl_load = (sizeof($acl) > 4) ? $acl_template[0] . str_replace('{action}', '*', $acl_template[1]) : $acl_template[0];
		}
			
		// loop over action
		foreach($actions as $action)
		{
			// acl template
			$acl_check = (!empty($acl) && (sizeof($acl) < 5) && in_array($action, $acl)) ? $acl_template[1] : '';
			
			// populate template action
			$data = file_get_contents(APPDIR . 'views' . DS . 'builder' . DS . 'controller' . DS . $action . '.php');
			$data = str_replace('{acl_check}',  $acl_check, $data);				
			$data = str_replace('{controller}',  $controller, $data);
			$data = str_replace('{tablename}',  $model['tablename'], $data);
			$data = str_replace('{action}', $action, $data);
			
			// assign controller action
			$controller_action .= $data;
		}
		
		// populate template controller
		$data = file_get_contents(APPDIR . 'views' . DS . 'builder' . DS . 'controller' . DS . 'controller.php');
		$data = str_replace('{acl_load}',  $acl_load, $data);		
		$data = str_replace('{controller}',  $controller, $data);
		$data = str_replace('{controller_class}',  ucwords($controller), $data);
		$data = str_replace('{controller_action}',  $controller_action, $data);
		
		// show output
		echo 'Generate code for ' . $controller . ' controller...<br />';
		
		// write controller to application directory
		$this->_writeout(APPDIR . 'controllers' . DS . $controller . '.php', $data);				
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
			$data = file_get_contents(APPDIR . 'views' . DS . 'builder' . DS . 'model' . DS . $action . '.php');
			$data = str_replace('{tablename}',  $model['tablename'], $data);
			$data = str_replace('{primary}',  $model['primary'], $data);
			
			// assign model action
			$model_action .= $data;
		}
				
		// populate template model
		$data = file_get_contents(APPDIR . 'views' . DS . 'builder' . DS . 'model' . DS . 'model.php');
		$data = str_replace('{controller}',  $controller, $data);
		$data = str_replace('{controller_class}',  ucwords($controller), $data);
		$data = str_replace('{model_action}',  $model_action, $data);
		
		// show output
		echo '<br>Generate code for ' . $controller . ' model...<br />';
		
		// write model to application directory
		$this->_writeout(APPDIR . 'models' . DS . $controller . '_model.php', $data);		
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
		// directory for viewer
		$directory = APPDIR . 'views' . DS . $controller;
		
		// show output
		echo '<br>Create directory view for ' . $controller . ' controller...<br />';

		// check existing directory
		if (!file_exists($directory) || !is_dir($directory))
		{
			mkdir($directory, 0777);
		}
		else
		{
			echo 'Directory view for ' . $controller . ' already exists! Skip...<br>';
		}

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
			$data = file_get_contents(APPDIR . 'views' . DS . 'builder' . DS . 'viewer' . DS . $action . '.php');
			
			// listing action
			if ($action == 'list')
			{					
				// create header
				$header = "<tr>\n\t";
				foreach($model['fieldnames'] as $head)
				{
					$header .= "\t<th>" . strtoupper(str_replace('_', ' ', $head)) . "</th>\n";
				}
				$header .= "<th>ACTION</th>\n";
				$header .= "</tr>\n";

				// create add link
				$addlink = in_array('create', $actions) ? '<b><a href="<?php echo CONF_BASE_PATH; ?>/{controller}/create" class="btn btn-success">Add New</a></b>' : '';
								
				// create row content
				$content = "<tr>";
				
				// loop over fieldname
				foreach($fieldnames as $field)
					$content .= "\t<td><?php echo \$row['" . $field . "']; ?></td>\n";

				// action link
				$content .= "\n\t<td>";

				// create view link
				if (in_array('view', $actions))
					$content .= '<a href="<?php echo CONF_BASE_PATH; ?>/' . $controller . '/view/<?php echo $row[\'' . $model['primary'] . '\']; ?>" class="btn btn-info">View</a> ';
				
				// create update link
				if (in_array('update', $actions))
					$content .= '<a href="<?php echo CONF_BASE_PATH; ?>/' . $controller . '/update/<?php echo $row[\'' . $model['primary'] . '\']; ?>" class="btn btn-warning">Update</a> ';
					
				// create delete link
				if (in_array('delete', $actions))
					$content .= '<a href="<?php echo CONF_BASE_PATH; ?>/' . $controller . '/delete/<?php echo $row[\'' . $model['primary'] . '\']; ?>" onclick="return confirm(\'Are you sure to delete this data?\')" class="btn btn-danger">Delete</a>';
					
				$content .= "</td>\n";

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
					$content .= "<div class=\"row\">\n";
					$content .= "\t<div class=\"col-md-3\">" . ucwords(str_replace('_', ' ', $field)) . ":</div>\n";
					$content .= "\t<div class=\"col-md-9\"><?php echo \$" . $field . "; ?></div>\n";
					$content .= "</div>\n";
				}
				
				// populate view template
				$data = str_replace('{content}',  substr($content, 0, -1), $data);					
				$data = str_replace('{controller}',  $controller, $data);
				$data = str_replace('{controller_class}',  ucwords($controller), $data);
				$data = str_replace('{primary}', $model['primary'], $data);
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
						$content .= "<div class=\"form-group\">";
						$content .= "\n\t<label for=\"data[" . $field . "]\">" . ucwords(str_replace('_', ' ', $field)) . ":</label>\n";
						if (preg_match("/text/i", $types[$field]))
							$content .= "\n\t<textarea class=\"form-control\" name=\"data[" . $field . "]\" rows=\"5\" cols=\"40\"></textarea>\n";
						else
							$content .= "\t<input type=\"text\" class=\"form-control\" name=\"data[" . $field . "]\" size=\"40\">\n";
						$content .= "</div>\n";
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
						$content .= "\t<input type=\"hidden\" name=\"update[" . $field . "]\" value=\"<?php echo \$" . $field . "; ?>\">\n";
						
					// standard form
					else
					{
						$content .= "<div class=\"form-group\">";
						$content .= "\n\t<label for=\"data[" . $field . "]\">" . ucwords(str_replace('_', ' ', $field)) . "</label>\n";
						if (preg_match("/text/i", $types[$field]))
							$content .= "\t<textarea class=\"form-control\" name=\"data[" . $field . "]\" rows=\"5\" cols=\"40\"><?php echo htmlentities(\$" . $field . "); ?></textarea>\n";
						else
							$content .= "\t<input class=\"form-control\" type=\"text\" name=\"data[" . $field . "]\" size=\"40\" value=\"<?php echo \$" . $field . "; ?>\">\n";
						$content .= "</div>\n";
					}
				}
				
				// populate view template
				$data = str_replace('{content}',  substr($content, 0, -1), $data);					
				$data = str_replace('{controller}',  $controller, $data);
				$data = str_replace('{controller_class}',  ucwords($controller), $data);
				$data = str_replace('{primary}', $model['primary'], $data);
			}
			
			// show output
			echo '<br>Generate code for ' . $controller . '  ' . $action . ' action viewer...<br />';
			
			// write the viewer
			$this->_writeout(APPDIR . 'views' . DS . $controller . DS . $action . '.php', $data);
		}
	}

	/**
	 * _generate_role
	 *
	 * function to generate view files
	 *
	 * @access	private
	 * @param	string $controller
	 * @param 	array $actions
	 * @param	array $acl
	 */
	private function _generate_role($controller, $actions, $acl)
	{
		if ( ! empty($acl) AND sizeof($acl) > 0 )
		{
			// show output
			echo '<br/>Generate access control for ' . $controller . ' module...<br />';					

			// load database
			$db = $this->load->database();

			// delete existing roles
			$db->where('group_id', 1)->where('module', $controller)->delete('roles');

			foreach ($actions as $action) 
			{
				if ( in_array($action, $acl) )
				{
					$role = ['group_id' => 1, 'module' => $controller, 'role' => $action];
					$db->insert('roles', $role);	

					// show output
					echo 'Assign ' . $action . ' access role...<br />';										
				}
			}
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
		// checking if file not exists
		if ( ! file_exists($filepath) )
		{
			// create handler
			if ( ! $handler = @fopen($filepath, 'w+') )
				throw new Exception('Cannot open file (' . $filepath . ')', 500);

			// write data
			if ( fwrite($handler, $content) === FALSE )
				throw new Exception('Cannot write to file (' . $filepath . ')', 500);

			// show notice
			echo 'Write code to ' . $filepath . ' file...<br>';

			// close file
			fclose($handler);
		}
		else
		{
			echo 'File ' . $filepath . ' already exists! Skip...<br>';
		}
	}
}

/* End of builder.php */
/* Location: /application/controllers/builder.php */