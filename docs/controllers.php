<p>
<a href="index.php">Back To Index</a>
</p>

<p>
WEB2BB encourages 'coding by contract'. Each controller in the WEB2BB system must extend the base controller class, and implement the iController interface. The controller names following a naming convention also, so the blog controller class declaration would look like this
</p>
<?php
$code='
<?php
	class blogController extends baseController implements IController
?>
';
highlight_string($code);
?>

<p>
Each should have an index method, which is called automatically when accessed.
</p>
