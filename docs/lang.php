<p>
<a href="index.php">Back To Index</a>
</p>

<p>
Setting language translations in WEB2BB is quite simple. You merely set the lang config option in the application/config/config.ini.php file to your language of choice and the language class is loaded automatically for you. The defaule is "en" or english. A demonstration french translation class is also provided, but you can make up as many as you like.
</p>

<p>
The language classes themselves, reside in application/lang directory and simply contain class constants to do the translations. The demonstration menu in the layout makes use of these. Here is how to call a language constant.
</p>

<?php
$code='
<?php
	echo lang::__HOME; 
?>
';
highlight_string($code);
?>

<p>
Thats it. Thats all you need to do to provide a simple and effecting multi language implementation.
</p>
