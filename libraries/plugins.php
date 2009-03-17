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

	var $path = 'plugins'; //location of the pluginfolder (relative path)
	var $config;

	function __construct($config=null)
	{
		$this->config = &$config;
	}

	/**
	 * Currently this script loads all the available plugins in the directory
	 *
	 * @todo Only load "activated" plugins (Load a stored array via get_option())
	 *
	 * @since ???
	 * @return array Returns an array with each file plugin file name
	 *
	 */
	public function get()
	{
		/**
		 * Create a list of every .php file in the plugins directory
		 *
		 * Note: any filenames (including php files) that begin with a "." (period) will not be included.
		 */

		// since classes can be extended we have to make sure there is no trailing slash in the $directory
		if ('/' == substr($this->path, strlen($this->path) - 1))
			$this->path = substr_replace($this->path, '', strlen($this->path) - 1);

		$plugin_files = array();
		$plugins_dir = @opendir($this->path);
		if ($plugins_dir)
		{
			while (($file = readdir($plugins_dir)) !== false)
			{
				if (substr($file, 0, 1) == '.')
					continue;
				if (is_dir($this->path . '/' . $file))
				{
					$plugins_subdir = @opendir($this->path . '/' . $file);
					if ($plugins_subdir)
					{
						while (($subfile = readdir($plugins_subdir)) !== false)
						{
							if (substr($subfile, 0, 1) == '.')
								continue;
							if (substr($subfile, -4) == '.php')
								$plugin_files[] = $file . '/' . $subfile;
						}
					}
				} else
				{
					if (substr($file, -4) == '.php')
						$plugin_files[] = $file;
				}
			}
		}
		@closedir($plugins_dir);
		@closedir($plugins_subdir);


		/**
		 * If for some reason we couldn't find any plugins
		 * or the plugin directory, return false.
		 */
		if (!$plugins_dir || !$plugin_files)
			return false;

		/**
		 * Load the information for each plugin
		 */
		foreach ($plugin_files as $plugin_file)
		{
			if (!is_readable($this->path . '/' . $plugin_file))
				continue;

			$plugin_data = $this->get_plugin_data($this->path . '/' . $plugin_file);

			/**
			 * If the plugin doesn't have a defined name, don't load it!
			 */
			if (empty($plugin_data['Name']))
				continue;

			$plugins[$this->plugin_basename($plugin_file)] = $plugin_data;


			/**
			 * Include the Plugin
			 *
			 * @todo Possibly move this to its own function? Have it run off of the $pp_cache[ 'plugins' ] array?
			 */
			include_once ($this->path . '/' . $this->plugin_basename($plugin_file));

		}

		/**
		 * Verify that we have legitimate plugins to include
		 */
		if (isset($plugins) && is_array($plugins))
		{
			uasort($plugins, create_function('$a, $b',
				'return strnatcasecmp( $a["Name"], $b["Name"] );'));
			return $plugins;
		} else
		{
			return false;
		}
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
	public function add_filter($hook, $function_to_add, $priority = 8, $accept_args =
		1)
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
	public function add_action($hook, $function_to_add, $priority = 8, $accept_args =
		1)
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

	/**
	 * Check if plugin exists
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $plugin the name of the plugin
	 *
	 * @return bool
	 *
	 */
	public function plugin_available($plugin)
	{
		return in_array($plugin, $this->config->plugins['plugins']);
	}

	/**
	 * Get the data specified for the addon
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $plugin_file the name of the plugin_file
	 *
	 * @return array (Name, Title, Description, Author, Version)
	 *
	 */
	private function get_plugin_data($plugin_file)
	{
		/**
		 * Each plugin file has to have the Standard Plugin Information as defined in
		 * the comment block below. This function will extract this information show it
		 * can be used in various ways (e.g. shown in the addons list).
		 */

		/*
		Plugin Name: Name Of The Plugin
		Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
		Description: A brief description of the plugin.
		Version: The plugin's Version Number, e.g.: 1.0
		Author: Name Of The Plugin Author
		Author URI: http://URI_Of_The_Plugin_Author
		*/

		if (isset($this->config->plugins['plugin'][$plugin_file]))
		{
			/**
			 * If the information has been stored in the Config then display the information.
			 */
			return $this->config->plugins['plugin'][$plugin_file];
		}
		/**
		 * If no cached version is available extract the information from the file
		 */
		$plugin_data = implode('', file($plugin_file));
		preg_match('|Plugin Name:(.*)$|mi', $plugin_data, $plugin_name);
		preg_match('|Plugin URI:(.*)$|mi', $plugin_data, $plugin_uri);
		preg_match('|Description:(.*)$|mi', $plugin_data, $description);
		preg_match('|Author:(.*)$|mi', $plugin_data, $author_name);
		preg_match('|Author URI:(.*)$|mi', $plugin_data, $author_uri);
		if (preg_match("|Version:(.*)|i", $plugin_data, $version))
			$version = trim($version[1]);
		else
			$version = '';

		$description = @trim($description[1]);

		$name = @$plugin_name[1];
		$name = trim($name);
		$plugin = $name;
		/**
		 * Create HTML output with links to the homepage of the plugin or author homepage
		 */

		if ('' != @trim($plugin_uri[1]) && '' != $name)
		{
			$plugin = '<a href="' . trim($plugin_uri[1]) .
				'" title="Visit plugin homepage">' . $plugin . '</a>';
		}

		if (empty($author_uri[1]))
		{
			$author = @trim($author_name[1]);
		} else
		{
			$author = '<a href="' . @trim($author_uri[1]) .
				'" title="Visit author homepage">' . @trim($author_name[1]) . '</a>';
		}

		return array('Name' => $name, 'Title' => $plugin, 'Description' => $description,
			'Author' => $author, 'Version' => $version);
	}

	/**
	 * plugin_basename() - Gets the basename of a plugin.
	 *
	 * This method extracts the name of a plugin from its filename.
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @access private
	 *
	 * @param string $file The filename of plugin.
	 * @return string The name of a plugin.
	 * @uses $this->path
	 */
	private function plugin_basename($file)
	{
		$file = str_replace('\\', '/', $file); // sanitize for Win32 installs
		$file = preg_replace('|/+|', '/', $file); // remove any duplicate slash
		$plugin_dir = str_replace('\\', '/', $this->path); // sanitize for Win32 installs
		$plugin_dir = preg_replace('|/+|', '/', $plugin_dir); // remove any duplicate slash
		$file = preg_replace('|^' . preg_quote($plugin_dir, '|') . '/|', '', $file); // get relative path from plugins dir
		return $file;
	}

}
