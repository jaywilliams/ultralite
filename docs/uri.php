<p>
<a href="index.php">Back To Index</a>
</p>

<p>
WEB2BB uses the concept of 'fragments' to break up a URI. Simply put, the URI is split up on the "/" character and each 'fragment' may be called by its numeric position.
</p>
<?php
$code='
<?php
	$uri = uri::getInstance();
	echo $uri->fragment(2);
?>
';
highlight_string($code);
?>
