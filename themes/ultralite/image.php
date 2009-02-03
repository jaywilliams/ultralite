<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $site_language; ?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	
	<title><?php echo $site_title; ?></title>
	
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
		}

		body {
			width: <?php echo $image_width; ?>px;
			margin: 0 auto;
			text-align: center;
			font: 14px "Lucida Grande", Lucida, Verdana, sans-serif;
		}

		#ultralite {
			text-align: left;
		}

		h1 {
			font-size: 2.0em;
		}

		h2 {
			font-size: 1.3em;
		}

		h3#title {
			font-size: 1.6em;
			float: left;
		}

		h2,h3 {
			margin-bottom: 12px;
		}
		
		#published {
			float: right;
			margin-top: 10px;
		}

		img#photo {
			border: 1px solid #000;
		}

		#description p {
			padding: 12px;
			text-align: justify;
		}
	</style>
</head>

<body>

<div id="ultralite">
	<h1><?php echo $site_title; ?></h1>
	
	<h2><?php echo $site_slogan; ?></h2>
	
	<h3 id="title"><?php echo $image_title; ?></h3>
	
	<span id="published"><?php echo $image_published; ?></span>
	
	<img src="images/<?php echo $image_filename; ?>" alt="<?php echo $image_title; ?>" <?php echo $image_dimensions; ?> id="photo" />
	
	<div id="description">
		<p><?php echo $image_description; ?></p>
	</div>
</div>

</body>
</html>