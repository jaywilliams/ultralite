<p>
<a href="index.php">Back To Index</a>
</p>

<h3>Caching and Templating</h3>
<p>
By default, caching is turned off to avoid caching of content that may not require caching. Caching is turned on simply by using the setCaching() method.
</p

<div class="codebox">
<?php
$code = '
<?php
	/*** a new view instance ***/
	$view = new view;

	/*** turn caching on for this page ***/
	$view->setCaching(true);
?>';
echo highlight_string($code,1);
?>
</div>

<p>
The cache_id is set from the controller and is in the format of /path/filename.php/options.
</p>

<div class="codebox">
<?php
$code = '
<?php
	/*** the path to the template ***/
	$path =__APP_PATH . \'/views/index.php\';

	/*** a new view instance ***/
	$view = new view;

	/*** set the cache id ***/
	$view->cache_id = md5( $path );

	/*** fetch the template ***/
	$result = $view->fetch( $path );

	/*** a new front controller ***/
	$fc = FrontController::getInstance();

	/*** set the body ***/
	$fc->setBody($result);
?>';
echo highlight_string($code,1);
?>
</div>

<p>
To get add options, that may have been passed via the url or other source, it would be like this
</p>
<div class="codebox">
<?php
$code = '
<?php
/*** the path to the template ***/
$path =__APP_PATH . \'/views/index.php\';

	/*** a new uri instance ***/
	$uri = uri::getInstance();

	/*** get an option from the uri ***/
	$options = $uri->fragment(1);

/*** a new view instance ***/
$view = new view;

/*** set the cache id ***/
$view->cache_id = md5( $path );

/*** fetch the template ***/
$result = $view->fetch( $path.$options );

/*** a new front controller ***/
$fc = FrontController::getInstance();

/*** set the body ***/
$fc->setBody($result);
?>';
echo highlight_string($code,1);
?>
</div>
