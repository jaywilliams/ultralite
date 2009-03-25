<?php

/**
 * This class allows you to do all kinds of stuff with plugins for Ultralight
 * This plugin (as well as the comments) is still under development!
 *
 * @author Dennis
 * @version 0.1
 * @since ???
 * @package Ultralight
 * @subpackage plugins
 **/

class plugins
{

	/**
	 * Relative path to plugin directory
	 * Note: The trailing slash is required.
	 *
	 * @var string
	 */
	var $path = 'plugins/';
	
	/**
	 * List of plugin filenames
	 *
	 * @var array
	 */
	var $plugins = array();
	
	/**
	 * Internal storage of action hooks
	 *
	 * @access protected
	 * @var array
	 */
	protected $actions = array();
	
	/**
	 * Internal storage of filter hooks
	 *
	 * @access protected
	 * @var array
	 */
	protected $filters = array();


/*
	function __construct()
	{
	
	}
*/


	/**
	 * Include all available plugins
	 *
	 * @return bool|array $this->plugins
	 */
	public function get()
	{		
		/**
		 * Verify that the plugin directory exists
		 */
		if (!(is_dir($this->path) && is_readable($this->path)))
			return false;

		/**
		 * Hard coded plugin list
		 * @todo to be replaced by a db or php function
		 */
/*
		$this->plugins[] = 'example.php';
		$this->plugins[] = 'example.php';
		$this->plugins[] = 'example.php';
		$this->plugins[] = 'example.php';
		$this->plugins[] = 'example.php';
*/


		/**
		 * Load the information for each plugin
		 */
		foreach ((array) $this->plugins as $id => $plugin)
		{
			if (!is_readable($this->path.$plugin))
				continue;

			/**
			 * Include the Plugin
			 */
			$result = include_once $this->path.$plugin;

			/**
			 * Verify that the file was included properly
			 * If it wasn't, remove it from the list
			 */
			if ($result !== 1)
				unset($this->plugins[$id]);
		}

		return $this->plugins;
	}


	/**
	 * Add an action to the specified hook
	 * 
	 * Note: Unlike filters, actions do not require arguments, so the accepted arguments is set to 0 by default.
	 *
	 * @param string $hook Action hook name
	 * @param string $callback_function Action plugin to add
	 * @param int $priority (optional|default = 10) Action plugin priority, only set this if you want your plugin to run before or after the other actions
	 * @param int $accept_args (optional|default = 0) Number of arguments your action plugin accepts
	 * @return bool
	 */
	public function add_action($hook, $callback_function, $priority = 10, $accept_args = 0)
	{
		if (!function_exists($callback_function))
			return false;

		$priority    = (int) $priority;
		$accept_args = (int) $accept_args;

		$this->actions[$hook][$priority][$callback_function] = array( 	'function' => $callback_function, 
																		'accept_args' => $accept_args );
		return true;
	}


	/**
	 * Remove an action from the specified hook
	 *
	 * @param string $hook Action hook name
	 * @param string $callback_function Action plugin to remove
	 * @param int $priority (optional) Action plugin priority, only set this if the plugin was set with a custom priority
	 * @return bool
	 */
	public function remove_action($hook, $callback_function, $priority = 10)
	{

		$priority    = (int) $priority;

		if (array_key_exists($callback_function, $this->actions[$hook][$priority][$callback_function])) {
			
			unset($this->actions[$hook][$priority][$callback_function]);

			if (empty($this->actions[$hook][$priority]))
				unset($this->actions[$hook][$priority]);
		}

		return true;
	}


	/**
	 * Run the actions for the specified hook
	 *
	 * @param string $hook Action hook name
	 * @return bool|mixed
	 */
	public function do_action($hook,&$string=true)
	{

		/**
		 * First, check to see if any plugins are using this hook:
		 */
		if (!isset($this->actions[$hook]))
			return $string;
		
		/**
		 * Verify that all of the $priorities are in order
		 */
		ksort($this->actions[$hook]);
		
		/**
		 * Get the additional hook arguments and trim the $hook variable 
		 * from the $args array, as it is not needed. 
		 */
		$args = array_slice(func_get_args(), 2);
		
		/**
		 * This is used to send the correct number of 
		 * arguments to the callback function.
		 */
		$args_total = count($args);

		/**
		 * Now for the magic, lets bring the actions to life:
		 */
		foreach ($this->actions[$hook] as  $priority => &$functions) {
			foreach ($functions as &$function) {
				
				$func_name   = & $function['function'];
				$accept_args = & $function['accept_args'];

				if ($accept_args > 0) {
					
					/**
					 * Functions can be funny, they can be particular with the number of arguments they accept, 
					 * so we'll be nice and only give it the number of arguments they request.
					 * 
					 * We subtract 1 from $accept_args when adding to the array, since we will be adding
					 * the $string variable back as a reference a bit later with the array_merge().
					 */
					if ($accept_args > 1 && $accept_args < $args_total)
						$the_args = array_slice($args, 0, $accept_args);
					elseif($accept_args > 1)
						$the_args = array_pad($args, ($accept_args-1),null);
					
					/**
					 * If the function only accepts one argument,
					 * we can save some time by calling the function directly.
					 */
					if ($accept_args == 1)
						$func_name($string);
					else
						call_user_func_array($func_name, array_merge(array(&$string), $the_args));
					
				}else {
					
					/**
					 * Simple and sweet,
					 * this calls our action,
					 * into er, um, action!
					 */
					$func_name();	
				}
				
				
			}
		}
		
		return $string;
	}

/**
 * Add a filter to the specified hook
 * 
 * Note: It is required that your filter accept at least one argument, 
 * the first of which will be passed by reference for your to modify as needed.
 *
 * @param string $hook Filter hook name
 * @param string $callback_function Filter plugin to add
 * @param int $priority (optional|default = 10) Filter plugin priority, only set this if you want your plugin to run before or after the other filters
 * @param int $accept_args (optional|default = 1) Number of arguments your filter plugin accepts
 * @return bool
 */
public function add_filter($hook, $callback_function, $priority = 10, $accept_args = 1)
{
	if (!function_exists($callback_function) || $accept_args < 1)
		return false;

	$priority    = (int) $priority;
	$accept_args = (int) $accept_args;

	$this->filters[$hook][$priority][$callback_function] = array( 	'function' => $callback_function, 
																	'accept_args' => $accept_args );
	return true;
}


/**
 * Remove a filter from the specified hook
 *
 * @param string $hook Filter hook name
 * @param string $callback_function Filter plugin to remove
 * @param int $priority (optional) Filter plugin priority, only set this if the plugin was set with a custom priority
 * @return bool
 */
public function remove_filter($hook, $callback_function, $priority = 10)
{

	$priority    = (int) $priority;

	if (array_key_exists($callback_function, $this->filters[$hook][$priority][$callback_function])) {
		
		unset($this->filters[$hook][$priority][$callback_function]);

		if (empty($this->filters[$hook][$priority]))
			unset($this->filters[$hook][$priority]);
	}

	return true;
}

/**
 * Apply filters to a specific string
 *
 * @param string $hook Filter hook name
 * @param string $string Raw input string
 * @return string $string Filtered output string
 */
public function apply_filters($hook,&$string)
{

	/**
	 * First, check to see if any filters are using this hook:
	 */
	if (!isset($this->filters[$hook]))
		return $string;
	
	/**
	 * Verify that all of the $priorities are in order
	 */
	ksort($this->filters[$hook]);

	/**
	 * Get any additional filter arguments to accompany to the hook & string and
	 * trim the $hook and $string from the $args array, as they are not needed. 
	 */
	$args = array_slice(func_get_args(), 2);
	
	/**
	 * This is used to send the correct number of 
	 * arguments to the callback function.
	 */
	$args_total = count($args);


	/**
	 * Now for the magic, lets bring the filters to life:
	 */
	foreach ($this->filters[$hook] as  $priority => &$functions) {
		foreach ($functions as &$function) {
			
			$func_name   = & $function['function'];
			$accept_args = & $function['accept_args'];

			/**
			 * We know that all filter plugins must have at least one argument
			 * So we'll check if this callback function is using more than one.
			 */
			if ($accept_args > 1) {
				
				/**
				 * Functions can be funny, they can be particular with the number of arguments they accept, 
				 * so we'll be nice and only give it the number of arguments they request.
				 * 
				 * We subtract 1 from $accept_args when adding to the array, since we will be adding
				 * the $string variable back as a reference a bit later with the array_merge().
				 */
				if ($accept_args < $args_total)
					$the_args = array_slice($args, 0, $accept_args);
				else
					$the_args = array_pad($args, ($accept_args-1),null);

				/**
				 * If the callback function only accepts two arguments,
				 * we can save some time by calling the function directly.
				 */
				if ($accept_args == 2)
					$func_name($string,$the_args);
				else
					call_user_func_array($func_name, array_merge(array(&$string), $the_args));
				
			}else {
				
				/**
				 * Simple and sweet,
				 * this calls our filter,
				 * into action.
				 */
				$func_name($string);
			}
			
			
		}
	}
	
	return $string;
}


}
