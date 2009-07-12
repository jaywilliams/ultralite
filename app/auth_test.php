<?php

/**
 * @author Dennis Mooibroek
 * @copyright 2009
 */
echo '<strong>The following message is brought to you by an included file with authentication check</strong><hr>';
// The purpose of this file is to test the authentication
if ($login->confirmAuth())
{
	echo '<p>Congratulations, if you see this line you\'re authenticated</p>';
}
else
{
	echo '<p>Authentication Failed!!!</p>';
}
echo '<hr>';
?>