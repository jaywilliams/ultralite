<?php
/**
 *  Author: Justin Vincent (justin@visunet.ie)
 *  Web...: http://php.justinvincent.com
 *  Name..: ezSQL_mysql
 *  Desc..: mySQL component (part of ezSQL databse abstraction library)
 *
 */
/**
 * @author Justin Vincent <justin@visunet.ie>,  Modifications by Nabeel Shahzad <nabeel@nsslive.net>
 * @link www.nsslive.net 
 */

define('EZSQL_VERSION','2.03');
define('OBJECT','OBJECT', true);
define('ARRAY_A','ARRAY_A', true);
define('ARRAY_N','ARRAY_N', true);
define('EZSQL_CORE_ERROR','ezSQLcore can not be used by itself (it is designed for use by database specific modules).');


/**********************************************************************
*  Core class containg common functions to manipulate query result
*  sets once returned
*/

class Pixelpost_DB_Core
{
	
	public $trace            = false;  // same as $debug_all
	public $debug_all        = false;  // same as $trace
	public $debug_called     = false;
	public $vardump_called   = false;
	public $show_errors      = true;
	public $num_queries      = 0;
	public $last_query       = null;
	public $error		      = null;
	public $errno			  = null;
	public $col_info         = null;
	public $captured_errors  = array();
	public $cache_dir        = false;
	public $cache_queries    = false;
	public $cache_inserts    = false;
	public $use_disk_cache   = false;
	public $cache_timeout    = 24; // hours
	public $insert_id;
	
	public $dsn = false;
	public $dbuser = false;
	public $dbpassword = false;
	public $dbname = false;
	public $dbhost = false;
	public $result;
	
	public $get_col_info = false;
	
	// == TJH == default now needed for echo of debug function
	public $debug_echo_is_on = true;
	
	/**
	 * Clear any previous errors
	 */
	public function clear_errors()
	{
		$this->error = '';
		$this->errno = 0;
	}	
	
	/**
	 * Save an error that occurs in our log
	 *
	 * @param string $err_str This is the error string
	 * @param int $err_no This is the error number
	 * @return bool True
	 *
	 */
	public function register_error($err_str, $err_no=-1)
	{
		// Keep track of last error
		$this->error = $err_str;
		$this->errno = $err_no;
	
		// Capture all errors to an error array no matter what happens
		$this->captured_errors[] = array(
							'error' => $err_str,
							'errno' => $err_no,
							'query' => $this->last_query);
			
		//show output if enabled
		//$this->show_errors ? trigger_error($this->error . '(' . $this->last_query . ')', E_USER_WARNING) : null;
	}
	
	
	/**
	 * Get the error log from all the query
	 *
	 * @return array Queries and their error/errno values
	 *
	 */
	public function get_all_errors()
	{
		return $this->captured_errors;
	}
	
		
	/**
	 * Returns the error string from the previous query
	 *
	 * @return string Error string
	 *
	 */
	public function error()
	{
		return $this->error;
	}
	
	
	/**
	 * Returns the error code from the previous query
	 *
	 * @return mixed Error code
	 *
	 */
	public function errno()
	{
		return $this->errno;
	}
	
	/**
	 * Show all errors by default
	 *
	 * @return bool true
	 *
	 */
	public function show_errors()
	{
		$this->show_errors = true;
		return true;
	}
	
	
	/**
	 * Hide any errors from showing by default.
	 * Can also access the property as $this->show_errors=false
	 *
	 * @return bool true
	 *
	 */
	public function hide_errors()
	{
		$this->show_errors = false;
		return true;
	}
	
	/**
	 * Remove the results from the last query
	 *
	 * @return bool Returns true
	 *
	 */
	public function flush()
	{
		// Get rid of these
		$this->last_result = null;
		$this->col_info = null;
		$this->last_query = null;
		$this->from_disk_cache = false;
		
		return true;
	}
			
	/**
	 * Get a single column/variable
	 *
	 * @param string $query SQL query
	 * @param int $x Column offset (default 0, returns first column)
	 * @param int $y Row offset (default 0, first row returned)
	 * @return mixed Returns the value of the variable
	 *
	 */
	public function get_var($query=null,$x=0,$y=0)
	{
		
		// Log how the function was called
		$this->func_call = "\$db->get_var(\"$query\",$x,$y)";
		
		// If there is a query then perform it if not then use cached results..
		if ( $query )
		{
			$this->query($query);
		}
		
		// Extract var out of cached results based x,y vals
		if ( $this->last_result[$y] )
		{
			$values = array_values(get_object_vars($this->last_result[$y]));
		}
		
		// If there is a value return it else return null
		return (isset($values[$x]) && $values[$x]!=='')?$values[$x]:null;
	}
	
		
	/**
	 * Return one row from the DB query (use if your doing LIMIT 1)
	 *	or are expecting/want only one row returned
	 *
	 * @param string $query The SQL Query
	 * @param type $output OBJECT (fastest, default), ARRAY_A, ARRAY_N
	 * @param string $y Row offset (0 for first, 1 for 2nd, etc)
	 * @return type Returns type as defined in $output
	 *
	 */
	public function get_row($query=null,$output=OBJECT,$y=0)
	{
		
		// Log how the function was called
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";
		
		// If there is a query then perform it if not then use cached results..
		if ( $query )
		{
			$this->query($query);
		}
		
		// If the output is an object then return object using the row offset..
		if ( $output == OBJECT )
		{
			return $this->last_result[$y]?$this->last_result[$y]:null;
		}
		// If the output is an associative array then return row as such..
		elseif ( $output == ARRAY_A )
		{
			return $this->last_result[$y]?get_object_vars($this->last_result[$y]):null;
		}
		// If the output is an numerical array then return row as such..
		elseif ( $output == ARRAY_N )
		{
			return $this->last_result[$y]?array_values(get_object_vars($this->last_result[$y])):null;
		}
		// If invalid output type was specified..
		else
		{
			$this->print_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
		}
		
	}
	
	/**
	 * Build a SELECT query, specifying the table, fields and extra conditions
	 *
	 * @param string $table Table to SELECT from
	 * @param mixed $fields Array of fields to SELECT for, or comma delimited string of fields
	 * @param string $cond Extra conditions (include the WHERE, LIMIT, etc)
	 * @param int $limit Number of results to limit
	 * @param type $type OBJECT, ARRAY_A, ARRAY_n
	 * @return type Array of results
	 *
	 */
	public function quick_select($table, $fields, $cond='', $type=OBJECT)
	{
		if($table ==  '') return false;
			
		$sql = 'SELECT ';
		
		if(is_array($fields))
		{
			$sql.= implode(',', $fields);
		}
		else
		{
			$sql .= $fields;
		}
		
		$sql .= ' FROM '.$table;
		
		if($cond != '')
			$sql .= ' '.$cond;
		
		return $this->get_results($sql, $type);
	}
	
	
	/**
	 * Build a UPDATE SQL Query. All values except for
	 *  numeric and NOW() will be put in quotes
	 * 
	 * Values are NOT escaped
	 *
	 * @param string $table Table to build update on
	 * @param array $fields Associative array, with [column]=value
	 * @param string $cond Extra conditions, without WHERE
	 * @return bool Results
	 *
	 */
	public function quick_update($table, $fields, $cond='')
	{
		if($table ==  '') return false;
		
		$sql = 'UPDATE '.$table.' SET ';
		
		if(is_array($fields))
		{
			foreach($fields as $key=>$value)
			{
				$sql.= "$key=";
				
				if(is_numeric($value) || $value == $this->sysdate())
					$sql.=$value.',';
				else
					$sql.="'$value',";
				
			}
			
			$sql = substr($sql, 0, strlen($sql)-1);
		}
		else
		{
			$sql .= $fields;
		}
		
		if($cond != '')
			$sql .= ' WHERE '.$cond;
		
		return $this->query($sql);
	}
	
	
	/**
	 * Build a quick INSERT query. For simplistic INSERTs only,
	 *  all values except numeric and NOW() are put in quotes
	 * 
	 * Values are NOT escaped
	 *
	 * @param string $table Table to insert into
	 * @param array $fields Associative array [column] = value
	 * @param string $flags Extra INSERT flags to add
	 * @return bool Results
	 *
	 */
	public function quick_insert($table, $fields, $flags= '')
	{
		if($table ==  '') return false;
		//if(!is_array($fields) == false) return false;
	
		$sql = 'INSERT '. $flags .' INTO '.$table.' ';

		$cols = $col_values = '';
		if(is_array($fields))
		{
			foreach($fields as $key=>$value)
			{
				// build both strings
				$cols .= $key.',';
							
				// Quotes or none based on value
				if(is_numeric($value) || $value == $this->sysdate())
					$col_values .= "$value,";
				else
				{
					$col_values .= "'$value',";
				}
					
			}
			
			$cols = substr($cols, 0, strlen($cols)-1);
			$col_values = substr($col_values, 0, strlen($col_values)-1);
			
			$sql .= '('.$cols.') VALUES ('.$col_values.')';
		}

		return $this->query($sql);
	}
	
	/**
	 * Get the value of one column from a query
	 *
	 * @param string $query The SQL query
	 * @param string $x Column to return
	 * @return array Return's the results of that one column
	 *
	 */
	public function get_col($query=null,$x=0)
	{
		
		// If there is a query then perform it if not then use cached results..
		if ( $query )
		{
			$this->query($query);
		}
		
		// Extract the column values
		for ( $i=0; $i < count($this->last_result); $i++ )
		{
			$new_array[$i] = $this->get_var(null,$x,$i);
		}
		
		return $new_array;
	}
		
	/**
	 * Returns the query as a set of results. Default returns OBJECT,
	 * that is much faster than translating to ARRAY_A or ARRAY_N
	 *
	 * @param string $query SQL query
	 * @param define $output OBJECT, ARRAY_A (associative array), ARRAY_N (numeric indexed). OBJECT is default and fastest
	 * @return object Array of results, each array value being what $output is defined as
	 *
	 */
	public function get_results($query=null, $output = OBJECT)
	{
		
		// Log how the function was called
		$this->func_call = "\$db->get_results(\"$query\", $output)";
		
		// If there is a query then perform it if not then use cached results..
		if ( $query )
		{
			$this->query($query);
		}
		
		// Send back array of objects. Each row is an object
		if ( $output == OBJECT )
		{
			return $this->last_result;
		}
		elseif ( $output == ARRAY_A || $output == ARRAY_N )
		{
			if ( $this->last_result )
			{
				$i=0;
				foreach( $this->last_result as $row )
				{
					$new_array[$i] = get_object_vars($row);
					
					if ( $output == ARRAY_N )
					{
						$new_array[$i] = array_values($new_array[$i]);
					}
					
					$i++;
				}
				
				return $new_array;
			}
			else
			{
				return null;
			}
		}
	}
		
	/**
	 * Get metadata regarding a column, about a column in the last query
	 *
	 * @param string $info_type Column information type to get
	 * @param int $col_offset Column number, -1 returns all columns
	 * @return array Column information
	 *
	 */
	public function get_col_info($info_type='name',$col_offset=-1)
	{
		if ($this->col_info )
		{
			if ( $col_offset == -1 )
			{
				$i=0;
				foreach($this->col_info as $col )
				{
					$new_array[$i] = $col->{$info_type};
					$i++;
				}
				return $new_array;
			}
			else
			{
				return $this->col_info[$col_offset]->{$info_type};
			}
		}
		
		return false;
	}
	
	
	/**
	 * Store a results in the cache for a certain query
	 *
	 * @param string $query SQL query to store
	 * @param bool $is_insert If it's an INSERT or not
	 * @return bool Success
	 *
	 */
	public function store_cache($query,$is_insert)
	{
		
		// The would be cache file for this query
		$cache_file = $this->cache_dir.'/'.md5($query);
		
		// disk caching of queries
		if ( $this->use_disk_cache && ( $this->cache_queries && ! $is_insert ) || ( $this->cache_inserts && $is_insert ))
		{
			if ( ! is_dir($this->cache_dir) )
			{
				$this->register_error("Could not open cache dir: $this->cache_dir");
				return false;
			}
			else
			{
				// Cache all result values
				$result_cache = array
					(
						'col_info' => $this->col_info,
						'last_result' => $this->last_result,
						'num_rows' => $this->num_rows,
						'return_value' => $this->num_rows,
						);
				error_log ( serialize($result_cache), 3, $cache_file);
			}
		}
		
		return true;		
	}
	
	
	/**
	 * Get the cached results for a query. This is called more internally
	 *
	 * @param string $query SQL query to return results for
	 * @return mixed Returns the unserialized results
	 *
	 */
	public function get_cache($query)
	{
		
		// The would be cache file for this query
		$cache_file = $this->cache_dir.'/'.md5($query);
		
		// Try to get previously cached version
		if ( $this->use_disk_cache && file_exists($cache_file) )
		{
			// Only use this cache file if less than 'cache_timeout' (hours)
			if ( (time() - filemtime($cache_file)) > ($this->cache_timeout*3600) )
			{
				unlink($cache_file);
			}
			else
			{
				$result_cache = unserialize(file_get_contents($cache_file));
				
				$this->col_info = $result_cache['col_info'];
				$this->last_result = $result_cache['last_result'];
				$this->num_rows = $result_cache['num_rows'];
				
				$this->from_disk_cache = true;
				
				// If debug ALL queries
				$this->trace || $this->debug_all ? $this->debug() : null ;
				
				return $result_cache['return_value'];
			}
		}
		
	}
	
	
	/**
	 * Show values of any variable type "nicely"
	 *
	 * @param mixed $mixed Variable to show
	 * @param bool $return Return the results or show on screen
	 * @return mixed This is the return value description
	 *
	 */
	public function vardump($mixed='', $return=false)
	{
		
		// Start outup buffering
		ob_start();
		
		echo "<p><table><tr><td bgcolor=ffffff><blockquote><font color=000090>";
		echo "<pre><font face=arial>";
		
		if ( ! $this->vardump_called )
		{
			echo "<font color=800080><b>ezSQL</b> (v".EZSQL_VERSION.") <b>Variable Dump..</b></font>\n\n";
		}
		
		$var_type = gettype ($mixed);
		print_r(($mixed?$mixed:"<font color=red>No Value / False</font>"));
		echo "\n\n<b>Type:</b> " . ucfirst($var_type) . "\n";
		echo "<b>Last Query</b> [$this->num_queries]<b>:</b> ".($this->last_query?$this->last_query:"NULL")."\n";
		echo "<b>Last Function Call:</b> " . ($this->func_call?$this->func_call:"None")."\n";
		echo "<b>Last Rows Returned:</b> ".count($this->last_result)."\n";
		echo "</font></pre></font></blockquote></td></tr></table>";
		echo "\n<hr size=1 noshade color=dddddd>";
		
		// Stop output buffering and capture debug HTML
		$html = ob_get_contents();
		ob_end_clean();
		
		// Only echo output if it is turned on
		if ( $this->debug_echo_is_on || $return == false)
		{
			echo $html;
		}
		
		$this->vardump_called = true;
		
		return $html;
		
	}
	
	/**
	 * Show values of any variable type "nicely"
	 *
	 * @param mixed $mixed Variable to show
	 * @param bool $return Return the results or show on screen
	 * @return mixed This is the return value description
	 *
	 */
	public function dumpvar($mixed, $return=false)
	{
		$this->vardump($mixed, $return);
	}
		
	/**
	 *  Displays the last query string that was sent to the database & a
	 * table listing results (if there were any).
	 * (abstracted into a seperate file to save server overhead).
	 *
	 * @param bool $return Return the results, or display right away
	 * @return string The debug table is $return = true
	 *
	 */
	public function debug($return=false)
	{
		
		// Start outup buffering
		ob_start();
		
		echo "<blockquote>";
		
		// Only show ezSQL credits once..
		if ( ! $this->debug_called )
		{
			echo "<font color=800080 face=arial size=2><b>ezSQL</b> (v".EZSQL_VERSION.") <b>Debug..</b></font><p>\n";
		}
		
		if ( $this->error )
		{
			echo "<font face=arial size=2 color=000099><b>Last Error --</b> [<font color=000000><b>$this->error ($this->errno)</b></font>]<p>";
		}
		
		if ( $this->from_disk_cache )
		{
			echo "<font face=arial size=2 color=000099><b>Results retrieved from disk cache</b></font><p>";
		}
		
		echo "<font face=arial size=2 color=000099><b>Query</b> [$this->num_queries] <b>--</b> ";
		echo "[<font color=000000><b>$this->last_query</b></font>]</font><p>";
		
		echo "<font face=arial size=2 color=000099><b>Query Result..</b></font>";
		echo "<blockquote>";
		
		if ( $this->col_info )
		{
			
			// =====================================================
			// Results top rows
			
			echo "<table cellpadding=5 cellspacing=1 bgcolor=555555>";
			echo "<tr bgcolor=eeeeee><td nowrap valign=bottom><font color=555599 face=arial size=2><b>(row)</b></font></td>";
			
			
			for ( $i=0; $i < count($this->col_info); $i++ )
			{
				echo "<td nowrap align=left valign=top><font size=1 color=555599 face=arial>{$this->col_info[$i]->type} {$this->col_info[$i]->max_length}</font><br><span style='font-family: arial; font-size: 10pt; font-weight: bold;'>{$this->col_info[$i]->name}</span></td>";
			}
			
			echo "</tr>";
			
			// ======================================================
			// print main results
			
			if ( $this->last_result )
			{
				
				$i=0;
				foreach ( $this->get_results(null,ARRAY_N) as $one_row )
				{
					$i++;
					echo "<tr bgcolor=ffffff><td bgcolor=eeeeee nowrap align=middle><font size=2 color=555599 face=arial>$i</font></td>";
					
					foreach ( $one_row as $item )
					{
						echo "<td nowrap><font face=arial size=2>$item</font></td>";
					}
					
					echo "</tr>";
				}
				
			} // if last result
			else
			{
				echo "<tr bgcolor=ffffff><td colspan=".(count($this->col_info)+1)."><font face=arial size=2>No Results</font></td></tr>";
			}
			
			echo "</table>";
			
		} // if col_info
		else
		{
			echo "<font face=arial size=2>No Results</font>";
		}
		
		echo "</blockquote></blockquote><hr noshade color=dddddd size=1>";
		
		// Stop output buffering and capture debug HTML
		$html = ob_get_contents();
		ob_end_clean();
		
		// Only echo output if it is turned on
		if ( $this->debug_echo_is_on || $return == false )
		{
			echo $html;
		}
		
		$this->debug_called = true;
		
		return $html;
		
	}
	
	/**********************************************************************
	*  Naughty little function to ask for some remuniration!
	*/
	
	public function donation()
	{
		return "<font size=1 face=arial color=000000>If ezSQL has helped <a href=\"https://www.paypal.com/xclick/business=justin%40justinvincent.com&item_name=ezSQL&no_note=1&tax=0\" style=\"color: 0000CC;\">make a donation!?</a> &nbsp;&nbsp;<!--[ go on! you know you want to! ]--></font>";
	}
	
}

?>
