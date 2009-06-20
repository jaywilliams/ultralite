<?php

error_reporting(E_ALL|E_STRICT);

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/app'));

// Ensure classes/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/classes'),
    get_include_path(),
)));


require_once 'Zend/Loader/Autoloader.php';

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Pixelpost_');
$autoloader->registerNamespace('Horde_');




function Test1_1() {

    /* The Test */
    $t = microtime(true);

	$i = 0;
	while ($i <= 10000) {
		Pixelpost_Config::current(true);
		$i++;
	}

    return (microtime(true) - $t);
}

function Test1_2() {

    /* The Test */
    $t = microtime(true);

	$i = 0;
	while ($i <= 10000) {
		Pixelpost_Config::set("theme", "greyspace".rand(),true);
		$i++;
	}

    return (microtime(true) - $t);
}

function Test2_1() {

    /* The Test */
    $t = microtime(true);

	$i = 0;
	
		Pixelpost_DB::init('mysqli'); 
		Pixelpost_DB::connect('root', '', 'ultralite', 'localhost');
	while ($i <= 10000) {
		
	
		$fields = array('option_name', 'option_value');
		Pixelpost_DB::quick_select('pixelpost_options', $fields);
	
		$i++;
	}
	Pixelpost_DB::close();
    return (microtime(true) - $t);
}

function Test2_2() {

    /* The Test */
    $t = microtime(true);

	Pixelpost_DB::init('mysqli'); 
	Pixelpost_DB::connect('root', '', 'ultralite', 'localhost');
	
	$i = 0;
	while ($i <= 10000) {
		
		$fields = array('option_value'=>'greyspace'.rand());
		Pixelpost_DB::quick_update('pixelpost_options', $fields, "option_name='theme'");
		$i++;
	}

	Pixelpost_DB::close();
    return (microtime(true) - $t);
}

echo "Load entire config file, 10,000 times:\n";
echo round(Test1_1(),4) . ' seconds';

sleep(5);

echo "\n\nSave an option to the config file, 10,000 times:\n";
echo round(Test1_2(),4) . ' seconds';

sleep(5);


echo "\n\nLoad entire config database, 10,000 times:\n";
echo round(Test2_1(),4) . ' seconds';

sleep(5);

echo "\n\nSave an option to the config database, 10,000 times:\n";
echo round(Test2_2(),4) . ' seconds';



// Test1_End();

