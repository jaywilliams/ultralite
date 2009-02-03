<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $site_language; ?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	
	<title><?php echo $site_title; ?></title>
</head>

<body>

<div id="ultralite">
	<h1><?php echo $site_title; ?></h1>
	
	<h2><?php echo $site_slogan; ?></h2>
	
	<h3><?php echo $image_title; ?></h3>
	
	<img src="images/<?php echo $image_filename; ?>" alt="<?php echo $image_title; ?>" <?php echo $image_dimensions; ?> id="photo" />
	
	<div id="description">
		<p><?php echo $image_description; ?></p>
	</div>
</div>

</body>
</html>