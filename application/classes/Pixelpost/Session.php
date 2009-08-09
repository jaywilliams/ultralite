<?php

/**
 * Session Class
 * A wrapper around PHP's session functions as well as
 * a custom session handling class that will save the session 
 * data to a database.  Because of the fixed input params to 
 * the various functions some variables might be discarded.
 * 
 * Usage:
 * 	$session = new Pixelpost_Session();
 * 	$session->set('message','Hello World!');
 * 	echo ( $session->get('message'); // Displays 'Hello World!'
 *
 * @package Pixelpost
 * @author Dennis Mooibroek (modified from code from the PHP Antology, 2nd edition)
 *
 * @uses  Pixelpost_DB
 *
 */

class Pixelpost_Session
{
	/**
	 * Session constructor<br />
	 * Starts the session with session_start()
	 * <b>Note:</b> that if the session has already started,
	 * session_start() does nothing
	 * @access public
	 */
	public function __construct($dbstore = true)
	{
		if ($dbstore)
		{
			session_set_save_handler(
				array($this, 'db_open'), 
				array($this, 'db_close'), 
				array($this, 'db_read'), 
				array($this, 'db_write'), 
				array($this, 'db_destroy'), 
				array($this, 'db_gc')
			);
		}
	}

	/**
	 * Sets a session variable
	 * @param string name of variable
	 * @param mixed value of variable
	 * @return void
	 * @access public
	 */
	public function set($name, $value)
	{
		$_SESSION[$name] = $value;
	}

	/**
	 * Clears a session
	 * @return void
	 * @access public
	 */
	public function clear()
	{
		$_SESSION = array();
	}

	/**
	 * Fetches a session variable
	 * @param string name of variable
	 * @return mixed value of session varaible
	 * @access public
	 */
	public function get($name)
	{
		if (isset($_SESSION[$name]))
		{
			return $_SESSION[$name];
		}
		else
		{
			return false;
		}
	}

	/**
	 * unsets a session variable
	 * @param string name of variable
	 * @return void
	 * @access public
	 */
	public function del($name)
	{
		unset($_SESSION[$name]);
	}

	/**
	 * Destroys the whole session
	 * @return void
	 * @access public
	 */
	public function destroy()
	{
		// Unset all of the session variables.
		$_SESSION = array();
		// destroy the cookie as well
		if (isset($_COOKIE[session_name()]))
		{
			setcookie(session_name(), '', time() - 42000, '/');
		}
		// Finally, destroy the session.
		session_destroy();
	}

	/**
	 * Starts or resume a session
	 * @return void
	 * @access public
	 */
	public function start()
	{
		// clean up the session table. Delete all sessions older than 7 days
		$ttl = 60*60*24*7;
		$this->db_gc($ttl);
		
		session_name('pixelpost');
		// try to get the session from the cookie
		if (isset($_COOKIE[session_name()]))
		{
			// try resuming the previous session. If the previous session was destroyed then
			// there is no problem, this is an unique id.
			// First check against the database if it is a valid id
			$sess_id = Pixelpost_DB::escape($_COOKIE[session_name()]);
			$row = Pixelpost_DB::get_row("SELECT `sess_data`, `sess_ip`,`sess_servervars` FROM `sessions` WHERE `sess_id` = '{$sess_id}' LIMIT 1");
			if ($row)
			{
				// There seems to be a session that is valid, now we perform various checks
				if ($this->getIP()==$row->sess_ip)
				{
					// We have a valid IP that matches the data stored in the db
					if ($_SERVER['HTTP_USER_AGENT']==$row->sess_servervars)
					{
						// The user agent is correct as well. Resume session
						session_id($sess_id);
					}
				
				}
			}
		}
		session_start();
	}
	
	/**
	 * Sets a session cookie
	 * 
	 * @param lifetime (time()+60*60*24*7 == 7 days)
	 * @return void
	 * @access public
	 */
	public function setCookie($lifetime)
	{
		setcookie(session_name(),session_id(),$lifetime);
	}

	/**
	 * Try to get the current IP of the client
	 * 
	 * @return void
	 * @access public
	 */
	public function getIP()
	{
		// although the HTTP headers can be faked it is worthwile to check them
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy

		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	/**
	 * Open a session by testing the connection to the database holding 
	 * the session data.
	 * Return true or false.  
	 * 
	 * This method is called with two string arguments
	 * 		— the path of the session file
	 *      - the name of the file
	 * and must return either true or false. The path and filename information 
	 * is irrelevant to us as we’re using a database, so we do nothing with it.
	 * 
	 * @param $open
	 * @param $path
	 * @access public
	 * @return bool 
	 */
	public function db_open($path, $name)
	{
		return Pixelpost_DB::$connected;
	}

	/**
	 * End the session 
	 * Return true or false.
	 * 
	 * @access public
	 * @return bool
	 */
	public function db_close()
	{
		return true;
	}

	/**
	 * retrieve the session data from the DB and place it in a string.
	 * Must return a string - even an empty one.
	 * 
	 * @param mixed $sess_id
	 * @access public
	 * @return string
	 */
	public function db_read($sess_id)
	{
		// make sure we sanitize the $sess_id, just to be on the safe side
		$sess_id = Pixelpost_DB::escape($sess_id);
		// get the data associated with the session from the table
		$row = Pixelpost_DB::get_row("SELECT `sess_data`, `sess_ip` FROM `sessions` WHERE `sess_id` = '{$sess_id}' LIMIT 1");
		//Pixelpost_DB::debug();
		if ($row) return isset($row->sess_data) ? $row->sess_data : '';
		else  return '';
	}

	/**
	 * Insert or update session data in the DB.
	 * Return true or false.
	 * 
	 * @param $sess_id
	 * @param $data
	 * @access public
	 * @return bool
	 */
	public function db_write($sess_id, $data)
	{
		$sess_id = Pixelpost_DB::escape($sess_id);
		$datetime = Pixelpost_DB::sysdate(); // use datetime('now','localtime') to store it not in UTC
		// get the data associated with the session from the table
		$row = Pixelpost_DB::get_row("SELECT `sess_data` FROM `sessions` WHERE `sess_id` = '{$sess_id}' LIMIT 1");
		//Pixelpost_DB::debug();
		if ($row)
		{
			$sql = "UPDATE `sessions` SET `sess_last_acc` = {$datetime}, `sess_data` = '{$data}' WHERE `sess_id` = '{$sess_id}'";
			$result = Pixelpost_DB::query($sql);
			//Pixelpost_DB::debug();
		}
		else
		{
			$ip = $this->getIP();
			// we store the $servervars so we can check various things while resuming a session from
			// a cookie, in this case we only check against the HTTP_USER_AGENT
			$servervars = Pixelpost_DB::escape($_SERVER['HTTP_USER_AGENT']);
			// should be an if statement here to check if the session vars of the login have been set
			// so we can grab the username and store it in the table. Reason being you can easily see
			// all open sessions in a sessionmanager by selecting on username.
			$sql = "INSERT INTO `sessions`(`sess_id`, `sess_start`, `sess_last_acc`, `sess_data`, `sess_ip`, `sess_servervars`) VALUES ('{$sess_id}', {$datetime}, {$datetime}, '{$data}', '{$ip}', '{$servervars}')";
			$result = Pixelpost_DB::query($sql);
			//Pixelpost_DB::debug();
		}
		return $result;
	}

	/**
	 * Delete the session data row in the database
	 * Return true or false.  
	 * 
	 * @param $sess_id
	 * @access public
	 * @return bool
	 */
	public function db_destroy($sess_id)
	{
		$sess_id = Pixelpost_DB::escape($sess_id);
		$sql = "DELETE FROM sessions WHERE sess_id = '{$sess_id}'";
		$result = Pixelpost_DB::query($sql);
		//Pixelpost_DB::debug();
		return $result;
	}

	/**
	 * Garbage collector for those that don't properly end 
	 * a session or the session times out. 
	 * 
	 * It receives an integer argument for the “time to live” (TTL) 
	 * value for a session. In our class method, gc, we delete any 
	 * session record where the last access time is less then the 
	 * current time, minus the TTL value
	 * 
	 * @param $ttl
	 * @access public
	 * @return bool
	 */
	public function db_gc($ttl)
	{
		$end = time() - $ttl;
		$sql = "DELETE FROM sessions WHERE sess_last_acc < {$end}";
		$result = Pixelpost_DB::query($sql);
		//Pixelpost_DB::debug();
		// may want to consider optimizing the table at a given rate to clean up all the
		// deletes of a high traffic site - maybe use OPTIMIZE
		// Pixelpost_DB::query('OPTIMIZE sessions');
		return $result;
	}

	/**
	 * class destructor
	 * because of a few changes in the implementation of the way
	 * sessions are closed out (after PHP v. 5.0.5) - when a page  
	 * is done we now have to explicitly call the write and close 
	 * ourselves.  PHP no longer does it automagically for us.
	 * 
	 * @param void
	 * @return void
	 */
	public function __destruct()
	{
		session_write_close();
	}
}