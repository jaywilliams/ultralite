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

class Pixelpost_DB_Oracle extends Pixelpost_DB_Core
{
	/**
	 * Connects to database immediately, unless $dbname is blank
	 *
	 * @param string $dbuser Database username
	 * @param string $dbpassword Database password
	 * @param string $dbname Database name (if blank, will not connect)
	 * @param string $dbhost Hostname, optional, default is 'localhost'
	 * @return bool Connect status
	 *
	 */
	
	public function __construct($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost')
	{
		if($dbname == '') return false;
		
		if($this->connect($dbuser, $dbpassword, $dbhost))
		{
			return $this->select($dbname);
		}
		
		return false;
	}
	
	/**
	 * Explicitly close the connection on destruct
	 */
	public function __destruct()
	{
		$this->close();
	}
	
	
	/** 
	 * Close the connection
	 */
	public function close()
	{
		return @oci_close($this->dbh);
	}
	
	/**
	 * Connects to database immediately, unless $dbname is blank
	 * 
	 * In the case of Oracle quick_connect is not really needed
	 *  because std. connect already does what quick connect does -
	 *  but for the sake of consistency it has been included
	 *
	 * @param string $dbuser Database username
	 * @param string $dbpassword Database password
	 * @param string $dbname Database name (if blank, will not connect)
	 * @param string $dbhost Hostname, optional, default is 'localhost'
	 * @return bool Connect status
	 *
	 */
	public function quick_connect($dbuser='', $dbpassword='', $dbname='')
	{
		return $this->connect($dbuser='', $dbpassword='', $dbname='');
	}
	
	/**
	 * Connect to MySQL, but not to a database
	 *
	 * @param string $dbuser Username
	 * @param string $dbpassword Password
	 * @return bool Success
	 *
	 */
	public function connect($dbuser='', $dbpassword='', $dbname='')
	{		
		if (!$this->dbh = oci_new_connect($dbuser, $dbpassword, $dbname))
		{
			$err = ocierror();
			
			$this->register_error($err['message'], $err['code']);
			return false;
		}
		else
		{	
			$this->clear_errors();
			return true;
		}
	}
	
	/**
	 *  No real equivalent of mySQL select in Oracle
	 *  once again, function included for the sake of consistency
	 */
	public function select($dbuser='', $dbpassword='', $dbname='')
	{
		return $this->connect($dbuser='', $dbpassword='', $dbname='');
	}

	/**
	 * Format an Oracle string correctly for safe Oracle insert
	 *  (no matter if magic quotes are on or not)
	 *
	 * @param string $str String to escape
	 * @return string Returns the escaped string
	 *
	 */
	public function escape($str)
	{
		return str_replace("'","''",str_replace("''","'",stripslashes($str)));
	}

	/**
	 * Returns the DB specific timestamp function (Oracle: SYSDATE, MySQL: NOW())
	 *
	 * @return string Timestamp function
	 *
	 */
	public function sysdate()
	{
		return "SYSDATE";
	}

	/**
	 *  These special Oracle functions make sure that even if your test
	 *  pattern is '' it will still match records that are null if
	 *  you don't use these funcs then oracle will return no results
	 *  if $user = ''; even if there were records that = ''
	 *
	 *  SELECT * FROM USERS WHERE USER = ".$db->is_equal_str($user)."
	 */

	public function is_equal_str($str='')
	{
		return ($str==''?'IS NULL':"= '".$this->escape($str)."'");
	}

	public function is_equal_int($int)
	{
		return ($int==''?'IS NULL':'= '.$int);
	}

	/**
	 *  Another oracle specific function - if you have set up a sequence
	 *  this function returns the next ID from that sequence
	 */
	public function insert_id($seq_name)
	{
		$return_val = $this->get_var("SELECT $seq_name.nextVal id FROM Dual");

		// If no return value then try to create the sequence
		if ( ! $return_val )
		{
			$this->query("CREATE SEQUENCE $seq_name maxValue 9999999999 INCREMENT BY 1 START WITH 1 CACHE 20 CYCLE");
			$return_val = $this->get_var("SELECT $seq_name.nextVal id FROM Dual");
			
			$this->register_error("Oracle CREATE SEQUENCE error: $seq_name");
		}

		return $return_val;
	}

	/**
	 * Run the SQL query, and get the result. Returns false on failure
	 *  Check $this->error() and $this->errno() functions for any errors
	 *  MySQL returns errno() == 0 for no error. That's the most reliable check
	 *
	 * @param string $query SQL Query
	 * @return mixed Return values
	 *
	 */	
	public function query($query)
	{

		$return_value = 0;

		// Flush cached values..
		$this->flush();

		// Log how the function was called
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug..
		$this->last_query = $query;

		$this->num_queries++;

		// Use core file cache function
		if ( $cache = $this->get_cache($query) )
		{
			return $cache;
		}

		// If there is no existing database connection then try to connect
		if ( ! $this->dbh )
		{
			$this->register_error('There is no active database connection!');
			return false;
		}

		// Parses the query and returns a statement..
		if(($stmt = oci_parse($this->dbh, $query)) === false)
		{
			$error = oci_error($this->dbh);
			
			if($error['code'] == 0)
			{
				$this->clear_errors();
				return true;
			}
			else
			{
				$this->register_error($error['message'], $error['code']);
				return false;
			}
		}

		// Execut the query..
		elseif (($this->result = oci_execute($stmt)) === false)
		{
			$error = oci_error($stmt);
			
			if($error['code'] == 0)
			{
				$this->clear_errors();
				return true;
			}
			
			$this->register_error($error['message'], $error['code']);
			return false;
		}

		// If query was an insert
		$is_insert = false;
		if(preg_match('/^(insert|delete|update|create) /i', $query))
		{
			$is_insert = true;
			// num affected rows
			$return_value = $this->rows_affected = @oci_num_rows($stmt);
		}
		// If query was a select
		else
		{
			// Get column information
			if($num_cols = @oci_num_fields($stmt))
			{
				// Fetch the column meta data
    			for($i=1;$i <= $num_cols;$i++)
    			{
    				$this->col_info[($i-1)]->name = @oci_field_name($stmt,$i);
    				$this->col_info[($i-1)]->type = @oci_field_name($stmt,$i);
    				$this->col_info[($i-1)]->size = @oci_field_name($stmt,$i);
			    }
			}

			// If there are any results then get them
			if ($this->num_rows = @oci_fetch_all($stmt,$results))
			{
				// Convert results into object orientated results..
				// Due to Oracle strange return structure - loop through columns
				foreach ( $results as $col_title => $col_contents )
				{
					$row_num=0;
					// then - loop through rows
					foreach (  $col_contents as $col_content )
					{
						$this->last_result[$row_num]->{$col_title} = $col_content;
						$row_num++;
					}
				}
			}

			// num result rows
			$return_value = $this->num_rows;
		}

		// disk caching of queries
		$this->store_cache($query,$is_insert);

		// If debug ALL queries
		$this->trace || $this->debug_all ? $this->debug() : null ;

		return $return_value;
	}
}
?>