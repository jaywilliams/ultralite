<?php
/**
 * This class allows you to do all kinds of stuff with plugins for Ultralight
 * This plugin (as well as the comments) is still under development!
 *
 * @author Dennis & Jay
 * @version 0.2
 * @since ???
 * @package Ultralight
 * @subpackage plugins
 **/

class Pixelpost_Plugin {

	/**
	 * Internal storage of action hooks
	 *
	 * @access protected
	 * @var array
	 */
	private $actions = array();

	/**
	 * Internal storage of filter hooks
	 *
	 * @access protected
	 * @var array
	 */
	private $filters = array();

	/**
	 * List of plugin filenames
	 *
	 * @var array
	 */
	private $plugins = array();
	
	/**
	 * Path to plugin directory
	 *
	 * @var string
	 */
	private $path = __PLUGIN_PATH;
	
	private static $instance;

	private function __construct()
	{
		// do nothing here, just make sure we cannot initiate the object
		$this->plugins = Pixelpost_Config::getInstance()->enabled_plugins;
	}		  
	
	public static function & getInstance()
	{
		if ( empty( self::$instance ) ) 
		{
			self::$instance = new self();
			self::$instance->loadPlugins();
		}
		return self::$instance;
	}
	
	/**
	 * Include all available plugins
	 *
	 * @return bool|array $this->plugins
	 * @todo make it return only activated plugins!
	 */
	protected function loadPlugins() 
	{
		/**
		 * Verify that the plugin directory exists
		 */
		 
		if (!(is_dir($this->path) && is_readable($this->path)))
		{
			throw new Exception("Unable to open plugin path");
			return false;		
		}
			
		/**
		 * Load the information for each plugin
		 */
		foreach ((array) $this->plugins as $id => $plugin)
		{
			if (!is_readable("$this->path/$plugin/$plugin.php"))
				continue;

			/**
			 * Include the Plugin
			 */
			$result = include_once("$this->path/$plugin/$plugin.php");

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
	public static function registerAction($hook, $callback_function, $priority = 10, $accept_args = 0)
	{		
		if (!function_exists($callback_function))
			return false;
		
		$self = self::getInstance();
		
		$priority	 = (int) $priority;
		$accept_args = (int) $accept_args;
		
		$self->actions[$hook][$priority][$callback_function] = array( 'function' => $callback_function, 'accept_args' => $accept_args );
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
	public static function removeAction($hook, $callback_function, $priority = 10)
	{
		
		$self = self::getInstance();
		
		$priority = (int) $priority;
		
		if (array_key_exists($callback_function, $self->actions[$hook][$priority][$callback_function])) 
		{
			unset($self->actions[$hook][$priority][$callback_function]);
			
			if (empty($self->actions[$hook][$priority]))
				unset($self->actions[$hook][$priority]);
		}

		return true;
	}
	
	/**
	 * Run the actions for the specified hook
	 *
	 * @param string $hook Action hook name
	 * @return bool|mixed
	 */
	public static function executeAction($hook,&$string=true)
	{
		$self = self::getInstance();

		/**
		 * First, check to see if any plugins are using this hook:
		 */
		if (!isset($self->actions[$hook]))
			return $string;
		
		/**
		 * Verify that all of the $priorities are in order
		 */
		ksort($self->actions[$hook]);
		
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
		foreach ($self->actions[$hook] as  $priority => &$functions) 
		{
			foreach ($functions as &$function) 
			{
				$func_name	 = & $function['function'];
				$accept_args = & $function['accept_args'];

				if ($accept_args > 0) 
				{
					
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
					
				}
				else 
				{
					
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
  public static function registerFilter($hook, $callback_function, $priority = 10, $accept_args = 1)
  {
	if (!function_exists($callback_function) || $accept_args < 1)
		return false;
	
	$self = self::getInstance();
	
	$priority	 = (int) $priority;
	$accept_args = (int) $accept_args;
  
	$self->filters[$hook][$priority][$callback_function] = array( 'function' => $callback_function, 'accept_args' => $accept_args );
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
  public static function removeFilter($hook, $callback_function, $priority = 10)
  {
	$self = self::getInstance();
	
	$priority = (int) $priority;
  
	if (array_key_exists($callback_function, $self->filters[$hook][$priority][$callback_function])) {
		
		unset($self->filters[$hook][$priority][$callback_function]);
  
		if (empty($self->filters[$hook][$priority]))
			unset($self->filters[$hook][$priority]);
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
  public static function executeFilter($hook,&$string)
  {
	
	$self = self::getInstance();
  
	/**
	 * First, check to see if any filters are using this hook:
	 */
	if (!isset($self->filters[$hook]))
		return $string;
	
	/**
	 * Verify that all of the $priorities are in order
	 */
	ksort($self->filters[$hook]);
  
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
	foreach ($self->filters[$hook] as  $priority => &$functions) 
	{
		foreach ($functions as &$function) 
		{
			
			$func_name	 = & $function['function'];
			$accept_args = & $function['accept_args'];
  
			/**
			 * We know that all filter plugins must have at least one argument
			 * So we'll check if this callback function is using more than one.
			 */
			if ($accept_args > 1) 
			{
				
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
				
			}
			else 
			{
				
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