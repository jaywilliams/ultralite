<?php

/**
 * Authentication Class
 * Used to authenticate users.
 * 
 * Usage:
 *     Pixelpost_Auth::login($username, $password);
 *     Pixelpost_Auth::logout();
 * 	   Pixelpost_Auth::confirmAuth()
 * 
 *
 * @package Pixelpost
 * @author Dennis Mooibroek (modified from code from the PHP Antology, 2nd edition)
 *
 * Dependancies: DB class
 *
 */
class Pixelpost_Auth
{
	/**
	 * Instance of Session class
	 * @access protected
	 * @var Session object
	 */
	protected $session;

	/**
	 * String to use when making hash of username and password
	 * @access protected
	 * @var string
	 */
	protected $hashKey;
	
	/**
	 * Auth constructor
	 * 
	 * @return boolean
	 * @access public
	 */
	function __construct($session,$hashKey)
	{
		//we need to pass the sessionvariable
		$this->session = $session;
		$this->hashKey = $hashKey;
	}

	/**
	 * Attempt to login, and set the session
	 */
	public function login($username='', $password='')
	{
		// convert the plaintext password to a SHA1 encoded string
		$password = hash('sha1',$password);
		$username = Pixelpost_DB::escape($username);
		
		// First check if there is a session available with a login_hash. 
		if ($this->session->get('login_vars'))
		{
			// There is login data present in the session so compare the username and password
			// with data stored in session
			$sess_loginarr = $this->session->get('login_vars');
			if (($sess_loginarr['login']==$username) && ($sess_loginarr['password']==$password))
			{
				// The given data corresponds with the data stored in the session
				// Next step is to establish if the hash can be confirmed
				//$old_sess_id = session_id();
				session_regenerate_id();
				//$this->session->db_write(session_id(),$_SESSION);
				//$this->session->db_destroy($old_sess_id);
				//unset($old_sess_id);
				// COMMENT: I'm not sure if the session handler is smart enough to do this
				return $this->confirmAuth();
			}
			else
			{
				// The given data is not the data in the session, do not login the user.
				// destroy the current session
				$this->logout();
				return false;
			}
		}
		else
		{
			// If there isn't any session we need to check the given credentials against the database
			// In order to do so we select the status of a user. If that status == 1 then the user can login
			$status = (int) Pixelpost_DB::get_var("SELECT `status` FROM users WHERE username = '{$username}' AND password = '{$password}' LIMIT 1");
			if ($status == 1) {
    			// We're good to go!
    			// Store the username, password and hash into the session
    			session_regenerate_id();
				$this->storeAuth($username, $password);
    			return true;
			}else {
    			// Login invalid, or the user is banned
    			return false;
			}
		}
	}

	/**
	 * Sets the session variables after a successful login
	 * @return void
	 * @access public
	 */
	public function storeAuth($login, $password)
	{
		// remember the $password var is a SHA1 - never keep the plaintext password
		// Create a session variable to use to confirm sessions
		$hashKey = hash('md5',$this->hashKey . $login . $password);
		$this->session->set('login_vars',array('login'=>$login, 'password'=>$password,'hash'=>$hashKey));
	}

	/**
	 * Confirms that an existing login is still valid
	 * @return boolean
	 * @access public
	 */
	public function confirmAuth()
	{
		$sess_loginarr = $this->session->get('login_vars');
		if ($sess_loginarr){
    		if (hash('md5',$this->hashKey . $sess_loginarr['login'] . $sess_loginarr['password']) != $sess_loginarr['hash'])
			{
				$this->logout();
				return false;
			}
			else
			{
				//valid authentication
				return true;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Logout, destroy the session
	 * @return void
	 * @access public
	 */
	public function logout()
	{
		$this->session->del('login_vars');
		// destroy the session
		$this->session->destroy();
	}
}
?>