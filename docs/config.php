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

<p>
Each module may also have its own config.ini.php file. This file must located in application/&lt;module name&gt;/config/config.ini.php 
</p>
<p>
When this file is in place, the config values are are added automatically to the config_values array and are available globally as seen above. Care should be taken by module creators not to over write config values in other modules.
</p>

<p>
Of course, if you do not wish to have a seperate config file for each module, the values can simply be added to the main system config.ini.php file.
</p>
