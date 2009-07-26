<p>
<a href="index.php">Back To Index</a>
</p>

<p>
The main config file is located in application/config/config.ini.php. This is a standard ini file which contains all settting for the application. It can be used globally for any modules you develop. Calling config options is a simple matter of getting the config instance and the section. This example shows how you would show the application version from the config file.
</p>
<?php
$code='
<?php
	$config = config::getInstance();
	$version =  $config->config_values[\'application\'][\'version\'];
?>
';
highlight_string($code);
?>
