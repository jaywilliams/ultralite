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
	
	
	var $actions = array();
	
	/**
	 * Stores stuff, mostly junk.
	 * 
	 * @var array
	 */
	var $config;
	

	function __construct($config=array())
	{
		$this->config = &$config;
	}

	/**
	 * Include all available plugins
	 *
	 * @return array $this->plugins
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
		$this->plugins[] = 'example.php';
		$this->plugins[] = 'example.php';
		$this->plugins[] = 'example.php';
		$this->plugins[] = 'example.php';
		$this->plugins[] = 'example.php';


		/**
		 * Load the information for each plugin
		 */
		foreach ($this->plugins as $id => $plugin)
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
	 * Add a plugin callback function to the specified action
	 *
	 * @param string $hook The Hook to tie into
	 * @param string $function_to_add The name of the plugin function to call
	 * @param int $priority (optional) Ability to apply before or after another function
	 * @param int $accept_args (optional) The number of arguments the plugin function will accept
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
	 * Remove a plugin callback function from the specified action
	 *
	 * @param string $hook The Hook, nuff said
	 * @param string $function_to_add The name of the plugin function to remove
	 * @param int $priority (optional) Ability to apply before or after another function
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
	 * Do action on specified hook
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $hook the name of the hook to bind to
	 * @param string|array $arg additional arguments
	 *
	 * @return depending on action
	 *
	 */
	public function do_action($hook)
	{

		/**
		 * First, check to see if any plugins are using this hook:
		 */
		if (!isset($this->actions[$hook]))
			return false;
		
		/**
		 * Verify all of the $priorities are in order
		 */
		ksort($this->actions[$hook]);

		/**
		 * Get the hook arguments to accompany to the hook
		 */
		$args = func_get_args();
		
		/**
		 * Remove the first one, because it's only the hook name
		 */
		array_shift($args);
		
		/**
		 * Does this hook even use arguments?
		 */
		$args_exist = (count($args) > 0)? true : false;


		/**
		 * Now for the magic, lets bring the plugins to life:
		 */
		foreach ($this->actions[$hook] as  $priority => &$functions) {
			foreach ($functions as &$function) {
				
				$func_name   = & $function['function'];
				$accept_args = & $function['accept_args'];

				if ($args_exist && $accept_args > 0) {
					
					/**
					 * Functions can be funny, they can be particular with the number of arguments they accept, 
					 * so we'll be nice and only give it the number of arguments they request.
					 */
					$the_args = array_slice($args, 0, $accept_args);
					
					/**
					 * If the function only accepts one argument,
					 * we can save some time by calling the function directly.
					 */
					if ($accept_args == 1)
						$func_name($the_args);
					else
						call_user_func_array($func_name, $the_args);
					
				}else {
					
					/**
					 * Simple and sweet,
					 * this calls our plugin,
					 * into action
					 */
					$func_name();	
				}
				
				
			}
		}
		
		return true;
	}

	/**
	 * Apply filter on specified hook
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $hook the name of the hook to bind to
	 * @param string $string the text to filer
	 *
	 * @return string $string the filtered text
	 *
	 */
	public function apply_filters($hook, $string)
	{

		if (!isset($this->config->plugins['plugin_functions'][$hook]))
		{
			/**
			 * if the current hook doesn't have any functions associated return the unmodified input
			 */
			return $string;
		} else
		{
			/**
			 * Plugins are sorted based upon priority in for a given hook.
			 */
			ksort($this->config->plugins['plugin_functions'][$hook]);
		}

		$args = array_slice(func_get_args(), 2);
		foreach ($this->config->plugins['plugin_functions'][$hook] as $priority => $functions)
		{
			/**
			 * Plugins with the same priority for a given hook are executed based upon
			 * alphabetical sorting of the function names.
			 */
			ksort($functions);
			if (!is_null($functions))
			{
				foreach ($functions as $function)
				{
					$all_args = array_merge(array($string), $args);
					$all_args[0] = $string;
					/**
					 * Create the argument list for the function
					 */
					if ($function['accept_args'] == 1)
					{
						$the_args = array($string);
					} elseif ($function['accept_args'] > 1)
						$the_args = array_slice($all_args, 0, $accept_args);
					elseif ($function['accept_args'] == 0)
						$the_args = null;
					else
						$the_args = $all_args;
					/**
					 * Check if funcion exists and then call the function providing the arguments
					 * as an array.
					 */
					if (function_exists($function['function']))
					{
						$return = call_user_func_array($function['function'], $the_args);
						/**
						 * If the return of the function is not a string return the unmodified input.
						 * Otherwise give back the return of the function.
						 */
						$string = (is_string($return)) ? $return : $string;
					}
				}
			}
		}
		return $string;
	}


}
