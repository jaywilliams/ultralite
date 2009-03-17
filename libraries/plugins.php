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
	 * Add a filter to a specified hook
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $hook the name of the hook to bind to
	 * @param string $function_to_add the function to call for the hook
	 * @param int $priority OPTIONAL priority of execution (1 is highest, 10 is lowest)
	 * @param int $accept_args OPTIONAL number of accepted arguments
	 *
	 * @return bool
	 *
	 */
	public function add_filter($hook, $function_to_add, $priority = 8, $accept_args = 1)
	{
		/**
		 * Priority is defined as a number 1 to 10, with 1 being the highest and 10 being the lowest.
		 * Functions with priority == 1 will be executed first.
		 * Default priority for plugins is 8, PixelPost core files can use priority 9 which leaves priority 10
		 * for plugins that needs to do things after PixelPost core code.
		 */

		$priority = (int)$priority;
		$accept_args = (int)$accept_args;

		/**
		 * Make sure a given function is not accidentally added twice to the hook.
		 * If this happens return 'false'.
		 */
		if (isset($this->config->plugins['plugin_functions'][$hook][$priority]) &&
			array_key_exists($function_to_add, $this->config->plugins['plugin_functions'][$hook][$priority]))
		{
			return false;
		}

		/**
		 * Add given function to the hook.
		 */
		$this->config->plugins['plugin_functions'][$hook][$priority][$function_to_add] =
			array('function' => $function_to_add, 'accept_args' => $accept_args);

		return true;
	}

	/**
	 * Add an action to a specified hook
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $hook the name of the hook to bind to
	 * @param string $function_to_add the function to call for the hook
	 * @param int $priority OPTIONAL priority of execution (1 is highest, 10 is lowest)
	 * @param int $accept_args OPTIONAL number of accepted arguments
	 *
	 * @return bool
	 *
	 */
	public function add_action($hook, $function_to_add, $priority = 8, $accept_args =1)
	{
		/**
		 * Since filters and actions have a similar semantics and both use the same
		 * table the add_filter call can be used to add an action.
		 *
		 * Priority is defined as a number 1 to 10, with 1 being the highest and 10 being the lowest.
		 * Functions with priority == 1 will be executed first.
		 * Default priority for plugins is 8, PixelPost core files can use priority 9 which leaves priority 10
		 * for plugins that needs to do things after PixelPost core code.
		 */
		return $this->add_filter($hook, $function_to_add, $priority, $accept_args);
	}

	/**
	 * Remove a filter from a specified hook
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $hook the name of the hook to bind to
	 * @param string $function_to_remove function to remove for the hook
	 * @param int $priority OPTIONAL priority of execution (1 is highest, 10 is lowest)
	 *
	 * @return bool
	 *
	 */
	public function remove_filter($hook, $function_to_remove, $priority = 8)
	{
		$removed = false; //gives information if the function was removed or not
		if (isset($this->config->plugins['plugin_functions'][$hook][$priority]))
		{
			if (array_key_exists($function_to_remove, $this->config->plugins['plugin_functions'][$hook][$priority]))
			{
				/**
				 * If the function was found then unset it from the array.
				 */
				unset($this->config->plugins['plugin_functions'][$hook][$priority][$function_to_remove]);
				$removed = true;
				/**
				 * If the removal of the function leaves the priority container empty, remove
				 * this as well.
				 */
				if (empty($this->config->plugins['plugin_functions'][$hook][$priority]))
				{
					unset($this->config->plugins['plugin_functions'][$hook][$priority]);
				}
			}
		}
		return $removed;
	}

	/**
	 * Remove an action from a specified hook
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $hook the name of the hook to bind to
	 * @param string $function_to_remove function to remove for the hook
	 * @param int $priority OPTIONAL priority of execution (1 is highest, 10 is lowest)
	 *
	 * @return bool
	 *
	 */
	public function remove_action($hook, $function_to_remove, $priority = 8)
	{
		/**
		 * Since filters and actions have a similar semantics and both use the same
		 * table the remove_filter call can be used to remove an action.
		 */

		return $this->remove_filter($hook, $function_to_remove, $priority);
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
	public function do_action($hook, $arg = '')
	{
		$extra_args = array_slice(func_get_args(), 2);
		$args = array_merge(array($arg), $extra_args);

		/**
		 * If no plugins are using this hook, return null.
		 * Otherwise, if there are plugins using the hook,
		 * sort them and start running through them.
		 */
		if (!isset($this->config->plugins['plugin_functions'][$hook]))
		{
			return null;
		} else
		{
			ksort($this->config->plugins['plugin_functions'][$hook]);
		}
		foreach ($this->config->plugins['plugin_functions'][$hook] as $priority => $functions)
		{
			if (!is_null($functions))
			{
				foreach ($functions as $function)
				{
					$func_name = $function['function'];
					$accept_args = $function['accept_args'];
					if ($accept_args == 1)
					{
						$the_args = array($arg);
					} elseif ($accept_args > 1)
					{
						/**
						 * If the function accepts more than 1 argument make sure we only extract
						 * the number of arguments allowed by the function.
						 */
						$the_args = array_slice($args, 0, $accept_args);
					} elseif ($accept_args == 0)
					{
						$the_args = null;
					} else
					{
						$the_args = $args;
					}
					$output[$func_name] = call_user_func_array($func_name, $the_args);
				}
			}
		}
		return $output;
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
