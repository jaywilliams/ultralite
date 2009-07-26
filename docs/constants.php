<p>
<a href="index.php">Back To Index</a>
</p>

<p>
The WEB2BB system comes with several constants to ensure that include paths and public web root paths are always correct. They are.
</p>
<dl>
<dt>__SITE_PATH</dt>
<dd>
This constant defines the location of the directory in which the main index.php file resides.
</dd>

<dt>__APP_PATH</dt>
<dd>
This constant defines the location of the application directory which contains the code to run the system. It is generally considered good practice that this directory not be in the web tree, however, having it as part of the web tree is not fatal and is default to ensure that the application "just works" out-of-the-box. This constant should be used when including ANY files required for the application to run.
</dd>
<dd>
<?php
$code='
<?php
	include __APP_PATH . \'/blog/lib/config.php\';
?>
';
highlight_string($code);
?>
</dd>

<dt>__PUBLIC_PATH</dt>
<dd>
This is the path of the web root of the system, which effectively gives method to ensure all URL paths are correct. This constant should be used with ALL URL paths in your application.
</dd>
<dd>
<?php
$code = '
<?php
	$link = \'<a href="\'.__PUBLIC_PATH.\'/blog/show/123">Link</a>\';
?>
';
highlight_string($code);
?>
</dd>
</dl>
