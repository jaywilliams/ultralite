<p>
<a href="index.php">Back To Index</a>
</p>

<p>
Database connectivity and use is simple in WEB2BB. The initial database connection is singleton which allows you to connect simply by calling the getInstance() method
</p>
<?php
$code='
<?php
	// connect to database
	$db = db::getInstance();
?>
';
highlight_string($code);
?>

<p>
The configuration of the database connection is governed by the config.ini.php file in the database section. There you will see something like this..
</p>
<pre>
[database]
db_type = mysql
db_name = web2bb
db_hostname = localhost
db_username = username
db_password = password
db_port = 3306
</pre>

<p>
As the system uses PDO as the interface, all the functionality of PDO is available from this single instance.

<?php
$code = '
<?php
	// connect to database
	$db = db::getInstance();

	$sql = "SELECT blog_heading, blog_text FROM my_blog WHERE blog_id=:blog_id";

	$stmt = $db->prepare( $sql );

	$stmt->bindParam(":blog_id", $blog_id, PDO::PARAM_INT );

	$stmt->execute();

	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
';
highlight_string($code);
?>
</dd>
</dl>
