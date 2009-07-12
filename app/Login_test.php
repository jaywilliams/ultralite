<?php

/**
 * @author Dennis Mooibroek
 * @copyright 2009
 */

header('Content-Type: text/html; charset=utf-8');

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

$namespaces[] = 'Pixelpost_';
$namespaces[] = 'Horde_';
$autoloader->registerNamespace($namespaces);

Pixelpost_DB::init('mysql'); 
//Pixelpost_DB::init('pdo'); 

Pixelpost_DB::connect('root', '', 'ultralite', 'localhost');
//Pixelpost_DB::connect('sqlite:'.APPLICATION_PATH.'/ultralite.sqlite'); 


/*
 *	Login example
*/


// prior to login we start a new session
$session = new Pixelpost_Session(true);
$session->start();
// use this to set a cookie so you can resume at a given time
$session->setCookie(time()+60*60*24*30);

$test_user='ultralite';			//this is coming from the form
$test_password='ultralite'; 	//this is coming from the form
$test_hash = 'SQj7$La}-/]p@%x0Feh_~^ lpC|N+1|(s?4#`vde?=^ H3bTJp53Ktj_JB,n!f+&';  //this is a config var!!

// initiate login object
$login = new Pixelpost_Auth($session, $test_hash);

if ($login->login($test_user, $test_password)){
	echo '<p>Congratulations, the login has worked!!!!</p>';
	require_once('auth_test.php');
}
else
{
	echo '<p>Ooops.... Something has gone wrong.....</p>';
	require_once('auth_test.php');
	var_dump($_SESSION);
}



?>