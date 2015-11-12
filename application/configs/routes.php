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
 * @since			Version 8.02
 */

/**
 * route.php
 *
 * routing configuration
 *
 * @package	    	phpHyppo
 * @subpackage		Application Configuration
 * @author			Muhammad Hamizi Jaminan
 */


/**
 * Default / Reserved Route
 *
 * Name of controller when no controller supply in the URL
 *
 * @access public
 * @value string
 */
$route['default_controller'] = 'hello';


/**
 * Application route configuration
 *
 * Please add your application routes configuration
 * under this section
 *
 * example :
 *		http://www.yourdomain.com/login 		= 	http://www.yourdomain.com/user/login
 *		http://www.yourdomain.com/logout 		= 	http://www.yourdomain.com/user/logout
 *
 *		$route['login'] 			= '/user/login';
 *		$route['logout']			= '/user/logout';
 *
 * scenario route with uri library:
 *		Please set the right uri segment number for trigger some uri value, please use segment seq value same with route link
 *		for example below, we need number 7 as page id for article route. The right value of uri segment is one (1).
 *
 *		http://www.youdomain.com/article/7/seo-title-of-article		= 	http://www.yourdomain.com/page/view/7/seo-title-of-article
 *
 *		$route['article']			= '/page/view';
 *
 *	controller code with route:
 *		$page_id = $this->uri->segment(1);
 *		$content['page'] = $this->page_model->get_page($page_id);
 *
 *	controller code without route:
 *		$page_id = $this->uri->segment(2);
 *		$content['page'] = $this->page_model->get_page($page_id);
 */

